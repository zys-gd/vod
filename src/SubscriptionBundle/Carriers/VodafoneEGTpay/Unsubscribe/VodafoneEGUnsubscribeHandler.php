<?php

namespace SubscriptionBundle\Carriers\VodafoneEGTpay\Unsubscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerInterface;

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
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::VODAFONE_EGYPT_TPAY;
    }

    /**
     * @param ProcessResult $processResult
     *
     * @return bool
     */
    public function isPiwikNeedToBeTracked(ProcessResult $processResult): bool
    {
        return false;
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
        // TODO: Implement applyPostUnsubscribeChanges() method.
    }
}