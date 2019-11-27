<?php

namespace SubscriptionBundle\Carriers\BeelineKZ\Subscribe;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BeelineKZSubscribeHandler
 */
class BeelineKZSubscribeHandler implements SubscriptionHandlerInterface, HasCommonFlow
{
    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::BEELINE_KAZAKHSTAN_DOT;
    }

    /**
     * @param Request $request
     * @param User $User
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        return [];
    }

    public function afterProcess(Subscription $subscription, ProcessResult $result)
    {
        // TODO: Implement afterProcess() method.
    }
}