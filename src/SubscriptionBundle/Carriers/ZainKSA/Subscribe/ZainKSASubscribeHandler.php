<?php

namespace SubscriptionBundle\Carriers\ZainKSA\Subscribe;

use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;

/**
 * Class ZainKSASubscribeHandler
 */
class ZainKSASubscribeHandler implements SubscriptionHandlerInterface, HasCommonFlow, HasCustomAffiliateTrackingRules
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ZainKSASubscribeHandler constructor.
     *
     * @param CarrierRepository              $carrierRepository
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     * @param LoggerInterface                $logger
     */
    public function __construct(
        CarrierRepository $carrierRepository,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking,
        LoggerInterface $logger
    ) {
        $this->carrierRepository = $carrierRepository;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
        $this->logger = $logger;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ZAIN_SAUDI_ARABIA;
    }

    /**
     * @param ProcessResult     $result
     * @param CampaignInterface $campaign
     *
     * @return bool
     */
    public function isAffiliateTrackedForSub(ProcessResult $result, CampaignInterface $campaign): bool
    {
        $carrier = $this->carrierRepository->findOneByBillingId(ID::ZAIN_SAUDI_ARABIA);

        $isSuccess = $result->isFailedOrSuccessful() && $result->isFinal();

        $isZeroCreditsSub = $this
            ->zeroCreditSubscriptionChecking
            ->isZeroCreditAvailable(ID::ZAIN_SAUDI_ARABIA, $campaign);

        $this->logger->debug('start custom isAffiliateTrackedForSub', [
            '$isSuccess' => $isSuccess,
            '$isZeroCreditsSub' => $isZeroCreditsSub
        ]);

        if ($isZeroCreditsSub) {
            $this->logger->debug('result custom isAffiliateTrackedForSub', [
                '$isTrackForSub' => $carrier->getTrackAffiliateOnZeroCreditSub(),
                '$isSuccess' => $isSuccess
            ]);

            return $isSuccess && $carrier->getTrackAffiliateOnZeroCreditSub();
        }

        return $isSuccess;
    }

    /**
     * @param ProcessResult $result
     *
     * @return bool
     */
    public function isAffiliateTrackedForResub(ProcessResult $result): bool
    {
        return false;
    }

    /**
     * @param Request $request
     * @param User    $User
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