<?php


namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Handler;


use SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Exception\FailedWithoutPossibleRetryException;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Exception\FailedWithPossibleRetryException;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Service\OfflineConversionTracker;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\CampaignConfirmationInterface;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\HasDelayedConfirmation;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\AbstractResult;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\Failure;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\Retry;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\Success;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Repository\Affiliate\AffiliateLogRepository;
use Symfony\Component\Routing\RouterInterface;

class GoogleCampaignHandler implements CampaignConfirmationInterface, HasDelayedConfirmation
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var OfflineConversionTracker
     */
    private $conversionServiceWrapper;
    /**
     * @var AffiliateLogRepository
     */
    private $affiliateLogRepository;

    public function __construct(
        RouterInterface $router,
        OfflineConversionTracker $conversionServiceWrapper,
        AffiliateLogRepository $affiliateLogRepository
    )
    {
        $this->router                   = $router;
        $this->conversionServiceWrapper = $conversionServiceWrapper;
        $this->affiliateLogRepository   = $affiliateLogRepository;
    }

    /**
     * @param string $affiliateUuid
     *
     * @return bool
     */
    public function isAffiliateSupported(string $affiliateUuid): bool
    {
        return $affiliateUuid == "514fe478-ebd4-11e8-95c4-02bb250f0f22";
    }


    public function getHandlerId(): string
    {
        return 'google';
    }

    public function doConfirm(AffiliateLog $affiliateLog): AbstractResult
    {
        try {
            $this->conversionServiceWrapper->trackConversion($affiliateLog);
            return new Success();
        } catch (FailedWithoutPossibleRetryException $exception) {
            return new Failure();
        } catch (FailedWithPossibleRetryException $exception) {
            return new Retry();
        }
    }

    public function getBatchOfLogs(): array
    {
        $retry = $this->affiliateLogRepository->findBatch(
            AffiliateLog::STATUS_WAITING,
            new \DateTimeImmutable('-30 min')
        );

        $retry6Hour = $this->affiliateLogRepository->findBatch(
            AffiliateLog::STATUS_RETRY6,
            new \DateTimeImmutable('-6 hour')
        );

        $retry24Hour = $this->affiliateLogRepository->findBatch(
            AffiliateLog::STATUS_RETRY24,
            new \DateTimeImmutable('-24 hour')
        );

        return array_merge($retry, $retry6Hour, $retry24Hour);
    }
}