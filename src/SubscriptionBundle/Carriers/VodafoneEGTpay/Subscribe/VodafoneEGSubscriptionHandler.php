<?php

namespace SubscriptionBundle\Carriers\VodafoneEGTpay\Subscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasConsentPageFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class VodafoneEGTpaySubscriptionHandler
 */
class VodafoneEGSubscriptionHandler implements SubscriptionHandlerInterface, HasConsentPageFlow
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
        $subscriptionContractId = $user->getProviderId();

        if (empty($subscriptionContractId)) {
            throw new BadRequestHttpException("Can't process subscribe, required parameter `subscription_contract_id` not found");
        }

        return ['subscription_contract_id' => $user->getProviderId(), 'url_id' => $user->getShortUrlId()];
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     */
    public function getAdditionalResubscribeParams(Request $request, User $user): array
    {
        return [];
    }

    /**
     * @param Subscription $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void
    {

    }
}