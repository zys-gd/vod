<?php

namespace SubscriptionBundle\Carriers\VodafoneEGTpay\Subscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Consent\HasConsentPageFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VodafoneEGTpaySubscriptionHandler
 */
class VodafoneEGSubscriptionHandler implements SubscriptionHandlerInterface, HasConsentPageFlow
{
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;

    /**
     * VodafoneEGSubscriptionHandler constructor
     *
     * @param IdentificationDataStorage $identificationDataStorage
     */
    public function __construct(IdentificationDataStorage $identificationDataStorage)
    {
        $this->identificationDataStorage = $identificationDataStorage;
    }

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