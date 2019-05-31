<?php

namespace SubscriptionBundle\Carriers\OrangeEGTpay\Subscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
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
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;

    /**
     * VodafoneEGSubscriptionHandler constructor
     *
     * @param LocalExtractor $localExtractor
     * @param IdentificationDataStorage $identificationDataStorage
     */
    public function __construct(LocalExtractor $localExtractor, IdentificationDataStorage $identificationDataStorage)
    {
        $this->localExtractor = $localExtractor;
        $this->identificationDataStorage = $identificationDataStorage;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $user): array
    {
        $data = [
            'url_id' => $user->getShortUrlId(),
            'lang' => $this->localExtractor->getLocal()
        ];

        if ((bool) $this->identificationDataStorage->readValue('is_wifi_flow')) {
            $data['subscription_contract_id'] = $this->identificationDataStorage->readValue('subscription_contract_id');
        }

        return $data;
    }

    /**
     * @param Subscription $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void
    {

    }
}