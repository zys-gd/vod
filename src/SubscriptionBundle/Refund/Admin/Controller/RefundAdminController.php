<?php

namespace SubscriptionBundle\Refund\Admin\Controller;

use ExtrasBundle\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use Sonata\AdminBundle\Controller\CRUDController;
use SubscriptionBundle\Entity\Refund;
use SubscriptionBundle\Refund\Admin\Form\RefundForm;
use SubscriptionBundle\Refund\RefundService;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RefundAdminController
 */
class RefundAdminController extends CRUDController
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var RefundService
     */
    private $refundService;

    /**
     * @var array
     */
    private $tableHeaders = [
        'Msisdn',
        'Status',
        'Error',
        'Billing charge process',
        'Billing refund process',
        'Refund credits'
    ];

    /**
     * RefundAdminController constructor
     *
     * @param FormFactory $formFactory
     * @param RefundService $refundService
     */
    public function __construct(FormFactory $formFactory, RefundService $refundService)
    {
        $this->formFactory = $formFactory;
        $this->refundService = $refundService;
    }

    /**
     * @param Request|null $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function createAction(Request $request = null)
    {
        $form = $this->formFactory->create(RefundForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            /** @var UploadedFile $file */
            $file = $formData['file'];

            $msisdns = empty($file)
                ? [$formData['identifier']]
                : str_getcsv(file_get_contents($file->getRealPath()), ',');

            $users = [];
            $nonexistentUsers = [];
            $usersWithoutProcesses = [];
            $asyncPayments = false;
            $em = $this->getDoctrine()->getManager();

            foreach ($msisdns as $msisdn) {
                /** @var User $user */
                $user = $em->getRepository(User::class)->findOneBy(['identifier' => $msisdn]);

                if (!empty($user)) {
                    $users[$msisdn]['msisdn'] = $msisdn;
                    list ($processes, $waiting) = $this->refundService->getUserProcessList($user);

                    if (!empty($processes)) {
                        $billingCarrierId = $user->getCarrier()->getBillingCarrierId();
                        $refundRepository = $em->getRepository(Refund::class);

                        foreach ($processes as $processId) {
                            $refundResponse = $this->refundService->sendRefundRequest($processId, $billingCarrierId);

                            if($refundResponse['status'] === Refund::STATUS_WAITING_PAYMENT && !$asyncPayments) {
                                $asyncPayments = true;
                            }

                            $users[$msisdn]['processes'][] = $refundResponse;

                            $refund = $refundRepository->findOneBy([
                                'user' => $user,
                                'bfChargeProcessId' => $refundResponse['charge_process_id']
                            ]);

                            if (!$refund) {
                                $refund = new Refund(UuidGenerator::generate());
                            }

                            $refund
                                ->setStatus($refundResponse['status'])
                                ->setError($refundResponse['error'])
                                ->setAttemptDate(new \DateTime('now'))
                                ->setRefundValue($refundResponse['refund_value'])
                                ->setBfChargeProcessId($refundResponse['charge_process_id'])
                                ->setBfRefundProcessId($refundResponse['refund_process_id'])
                                ->setUser($user);

                            $em->persist($refund);
                            $em->flush();
                        }

                        if(!empty($waiting)) {
                            foreach ($waiting as $waitingProcessId) {
                                $waitingRefund = $refundRepository->findOneBy([
                                        'bfChargeProcessId' => $waitingProcessId,
                                        'status' => Refund::STATUS_WAITING_PAYMENT
                                    ]);

                                $refundResponse = [
                                    'status' => $waitingRefund->getStatus(),
                                    'error' => $waitingRefund->getError(),
                                    'refund_process_id' => $waitingRefund->getBfRefundProcessId(),
                                    'charge_process_id' => $waitingRefund->getBfChargeProcessId(),
                                    'refund_value' => 0
                                ];

                                $users[$msisdn]['processes'][] = $refundResponse;
                            }
                        }
                    } else {
                        $usersWithoutProcesses[] = $msisdn;
                    }
                } else {
                    $nonexistentUsers[] = $msisdn;
                }
            }

            if ($asyncPayments && !empty($users)) {
                sleep(3);
                $users = $this->refundService->readRefundResponses($users);
            }

            return $this->renderWithExtraParams('@SubscriptionAdmin/Refund/refunds_result.html.twig', [
                'users'=> $users,
                'headers' => $this->tableHeaders,
                'nonexistentUsers' => $nonexistentUsers,
                'usersWithoutProcesses' => $usersWithoutProcesses
            ]);
        }

        return $this->renderWithExtraParams('@SubscriptionAdmin/Refund/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}