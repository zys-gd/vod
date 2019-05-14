<?php

namespace SubscriptionBundle\Carriers\OrangeEGTpay\Subscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasConsentPageFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrangeEGSubscriptionHandler
 */
class OrangeEGSubscriptionHandler implements SubscriptionHandlerInterface, HasConsentPageFlow
{
    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::VODAFONE_EGYPT_TPAY;
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $user): array
    {
        return ['subscription_contract_id' => $user->getIdentificationUrl()];
    }

    /**
     * @param Subscription $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void
    {

    }
}