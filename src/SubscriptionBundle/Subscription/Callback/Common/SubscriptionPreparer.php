<?php


namespace SubscriptionBundle\Subscription\Callback\Common;


use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\SubscriptionProvider;
use SubscriptionBundle\Service\UserProvider;

class SubscriptionPreparer
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var UserProvider
     */
    private $userProvider;
    /**
     * @var SubscriptionProvider
     */
    private $subscriptionProvider;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    /**
     * SubscriptionPreparer constructor.
     *
     * @param CarrierRepository    $carrierRepository
     * @param UserProvider         $userProvider
     * @param SubscriptionProvider $subscriptionProvider
     * @param EntitySaveHelper     $entitySaveHelper
     */
    public function __construct(
        CarrierRepository $carrierRepository,
        UserProvider $userProvider,
        SubscriptionProvider $subscriptionProvider,
        EntitySaveHelper $entitySaveHelper
    )
    {
        $this->carrierRepository    = $carrierRepository;
        $this->userProvider         = $userProvider;
        $this->subscriptionProvider = $subscriptionProvider;
        $this->entitySaveHelper     = $entitySaveHelper;
    }

    /**
     * @param ProcessResult $processResult
     *
     * @return array
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws \Exception
     */
    public function makeUserWithSubscription(ProcessResult $processResult): array
    {
        $billingCarrierId = $processResult->getCarrier();
        /** @var Carrier $carrier */
        $carrier          = $this->carrierRepository->findOneByBillingId($billingCarrierId);
        $msisdn           = $processResult->getClientUser() ?? $processResult->getProviderUser();
        $billingProcessId = $processResult->getId();
        $user             = $this->userProvider->obtainUser($msisdn, $carrier, $billingProcessId, '', null);
        $subscription     = $this->subscriptionProvider->obtainSubscription($user);
        $this->entitySaveHelper->persistAndSave($user);
        $this->entitySaveHelper->persistAndSave($subscription);

        return [$carrier, $user, $subscription];
    }
}