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
     * RefundService constructor
     *
     * @param EntityManager $entityManager
     * @param Client $billingClientApi
     */
    public function __construct(EntityManager $entityManager, Client $billingClientApi)
    {
        $this->entityManager = $entityManager;
        $this->billingClientApi = $billingClientApi;
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
            $processes = $this->getDataFromReportingTool($user);
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

    /**
     * @param User $user
     *
     * @return array
     */
    private function getDataFromReportingTool(User $user): array
    {
        $url = 'http://stage.reporting.playwing.com/a/api/stats/userstats_withcharges/'
            . $user->getCarrier()->getBillingCarrierId()
            . '/';
        $key = sha1(date("Y") . $user->getIdentifier() . date("d"));

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => ['msisdn' => $user->getIdentifier()]
        ]);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'X-REVERSE-KEY: ' . $key,
        ]);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        $processList = [];

        if ($response && !empty($response['data']['charges_successful'])) {
            foreach ($response['data']['charges_successful'] as $chargeData){
                $processList[] = $chargeData['process'];
            }
        }

        return $processList;
    }
}