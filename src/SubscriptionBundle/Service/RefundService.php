<?php

namespace SubscriptionBundle\Service;

use Doctrine\ORM\EntityManager;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\Client;
use SubscriptionBundle\Entity\Refund;
use SubscriptionBundle\Entity\Subscription;

/**
 * Class RefundService
 */
class RefundService
{
    const PROCESS_IDENTIFIER = 'refund';
    const DATA_METHOD_PROCESS = 'process';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Client
     */
    private $billingClientApi;

    /**
     * @var ReportingToolService
     */
    private $reportingToolService;

    /**
     * RefundService constructor
     *
     * @param EntityManager $entityManager
     * @param Client $billingClientApi
     * @param ReportingToolService $reportingToolService
     */
    public function __construct(
        EntityManager $entityManager,
        Client $billingClientApi,
        ReportingToolService $reportingToolService
    ) {
        $this->entityManager = $entityManager;
        $this->billingClientApi = $billingClientApi;
        $this->reportingToolService = $reportingToolService;
    }

    /**
     * @param int $processId
     * @param int $billingCarrierId
     *
     * @return array
     */
    public function sendRefundRequest(int $processId, int $billingCarrierId): array
    {
        try {
            $options = ['process' => $processId, 'carrier' => $billingCarrierId];
            $response = $this->billingClientApi->sendGetRequest(self::PROCESS_IDENTIFIER, $options);

            $refundResponse = [
                'status' => $response->status,
                'error' => null,
                'refund_process_id' => $response->id,
                'charge_process_id' => $processId,
                'refund_value' => $response->charge_paid
            ];
        } catch (\Exception $e) {
            $refundResponse = [
                'status' => 'failed',
                'error' => 'billing_response_error',
                'refund_process_id' => null,
                'charge_process_id' => $processId,
                'refund_value' => null
            ];
        }

        return $refundResponse;
    }

    /**
     * @param array $users
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function readRefundResponses(array $users): array
    {
        foreach ($users as $user) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['identifier' => $user['msisdn']]);

            if (!empty($user['processes'])) {
                $refundRepository = $this->entityManager->getRepository(Refund::class);

                foreach ($user['processes'] as &$process) {
                    if (!$process['error']) {
                        $dataProcessResponse = $this
                            ->billingClientApi
                            ->sendGetDataRequestWithoutCache(self::DATA_METHOD_PROCESS, $process['refund_process_id']);

                        $process['status'] = $dataProcessResponse->status;
                        $process['refund_value'] = $dataProcessResponse->charge_paid;

                        /** @var Refund $refund */
                        $refund = $refundRepository->findOneBy([
                            'user' => $user,
                            'bfRefundProcessId' => $process['refund_process_id']
                        ]);

                        $refund
                            ->setStatus($dataProcessResponse->status)
                            ->setAttemptDate(new \DateTime('now'))
                            ->setRefundValue($dataProcessResponse->charge_paid);

                        if ($dataProcessResponse->status == Refund::STATUS_FAILED) {
                            $refund->setError('process_error');
                        }

                        $this->entityManager->persist($refund);
                    }
                }

                $this->entityManager->flush();
            }
        }

        return $users;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getUserProcessList(User $user): array
    {
        $processes = [];
        $successful = [];
        $waiting = [];

        /** @var Subscription $subscription */
        $subscription = $this->entityManager
            ->getRepository(Subscription::class)
            ->findOneBy(['user' => $user]);

        if (!empty($subscription)) {
            $reportingToolsResponse = $this->reportingToolService->getUsersStatsWithCharges($user);

            if ($reportingToolsResponse && !empty($reportingToolsResponse['data']['charges_successful'])) {
                foreach ($reportingToolsResponse['data']['charges_successful'] as $chargeData){
                    $processes[] = $chargeData['process'];
                }
            }

            $refundRepository = $this->entityManager->getRepository(Refund::class);

            $userRefunds = $refundRepository->findBy(['user' => $user, 'status' => Refund::STATUS_SUCCESSFUL]);

            /** @var Refund $refund */
            foreach ($userRefunds as $refund){
                $successful[] = $refund->getBfChargeProcessId();
            }

            $waitingRefunds = $refundRepository->findBy(['user' => $user, 'status' => Refund::STATUS_WAITING_PAYMENT]);
            foreach ($waitingRefunds as $refund){
                $waiting[] = $refund->getBfChargeProcessId();
            }
        }

        return [array_diff($processes, $successful, $waiting), $waiting];
    }
}