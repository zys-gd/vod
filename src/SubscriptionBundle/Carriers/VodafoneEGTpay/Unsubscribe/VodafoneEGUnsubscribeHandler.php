<?php

namespace SubscriptionBundle\Carriers\VodafoneEGTpay\Unsubscribe;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VodafoneEGUnsubscribeHandler
 */
class VodafoneEGUnsubscribeHandler implements UnsubscriptionHandlerInterface
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
        return $carrier->getBillingCarrierId() === ID::VODAFONE_EGYPT_TPAY;
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
     * @param Request $request
     * @return array
     */
    public function getAdditionalUnsubscribeParameters(Request $request): array
    {
        return [
            'lang' => $this->localExtractor->extractLocale($request)
        ];
    }

    /**
     * @param Subscription $subscription
     */
    public function applyPostUnsubscribeChanges(Subscription $subscription): void
    {

    }
}