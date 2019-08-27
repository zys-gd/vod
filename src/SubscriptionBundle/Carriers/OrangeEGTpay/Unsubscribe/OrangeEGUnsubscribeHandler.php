<?php

namespace SubscriptionBundle\Carriers\OrangeEGTpay\Unsubscribe;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerInterface;

/**
 * Class VodafoneEGUnsubscribeHandler
 */
class OrangeEGUnsubscribeHandler implements UnsubscriptionHandlerInterface
{
    /**
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * OrangeEGUnsubscribeHandler constructor
     *
     * @param LocalExtractor $localExtractor
     */
    public function __construct(LocalExtractor $localExtractor)
    {
        $this->localExtractor = $localExtractor;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param ProcessResult $processResult
     *
     * @return bool
     */
    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getAdditionalUnsubscribeParameters(): array
    {
        return [
            'lang' => $this->localExtractor->getLocal()
        ];
    }

    /**
     * @param Subscription $subscription
     */
    public function applyPostUnsubscribeChanges(Subscription $subscription)
    {

    }
}