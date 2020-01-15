<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 16:38
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Service;


use Doctrine\ORM\EntityManagerInterface;
use Google\AdsApi\AdWords\v201809\cm\OfflineConversionFeed;
use Google\AdsApi\AdWords\v201809\cm\OfflineConversionFeedOperation;
use Google\AdsApi\AdWords\v201809\cm\OfflineConversionFeedService;
use Google\AdsApi\AdWords\v201809\cm\Operator;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Exception\FailedWithoutPossibleRetryException;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Exception\FailedWithPossibleRetryException;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;

class OfflineConversionTracker
{
    /**
     * @var OfflineConversionFeedService
     */
    private $conversionFeedService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * OfflineConversionTracker constructor.
     * @param FeedConversionServiceProvider $conversionFeedService
     * @param EntityManagerInterface        $entityManager
     */
    public function __construct(
        FeedConversionServiceProvider $conversionFeedService,
        EntityManagerInterface $entityManager
    )
    {
        $this->conversionFeedService = $conversionFeedService;
        $this->entityManager         = $entityManager;
    }

    /**
     * @param AffiliateLog $logRecord
     * @return void
     * @throws FailedWithPossibleRetryException
     * @throws FailedWithoutPossibleRetryException
     */
    public function trackConversion(AffiliateLog $logRecord): void
    {
        $conversionTime = $logRecord->getAddedAt()->format('Ymd His') . ' UTC';

        try {
            $operation = $this->makeOperation($logRecord, $conversionTime);
        } catch (\Throwable $exception) {
            throw new FailedWithoutPossibleRetryException();
        }

        try {

            $client       = $this->conversionFeedService->buildService();
            $APIResult    = $client->mutate([$operation]);
            $resultObject = $APIResult->getValue()[0];
            $newMessage   = sprintf("Uploaded offline conversion for Google Click ID = %s to %s",
                $resultObject->getGoogleClickId(),
                $resultObject->getConversionName()
            );
            $this->updateLogRecord(
                $logRecord,
                AffiliateLog::STATUS_SUCCESS,
                $newMessage
            );
        } catch (\Exception  $exception) {
            $canBeRetried = strpos($exception->getMessage(), 'TOO_RECENT') !== false;
            $newStatus    = $this->calculateStatus($canBeRetried, $logRecord->getStatus());
            $newMessage   = sprintf("Uploaded offline conversion for Google Click ID = %s to %s failed" . PHP_EOL . "%s",
                $operation->getOperand()->getGoogleClickId(),
                $operation->getOperand()->getConversionName(),
                $exception->getMessage()
            );

            $this->updateLogRecord($logRecord, $newStatus, $newMessage);

            if ($canBeRetried) {
                throw new FailedWithPossibleRetryException();
            } else {
                throw new FailedWithoutPossibleRetryException();
            }
        }
    }

    /**
     * @param AffiliateLog $logRecord
     * @param string       $conversionTime
     * @return OfflineConversionFeedOperation
     */
    private function makeOperation(AffiliateLog $logRecord, string $conversionTime): OfflineConversionFeedOperation
    {
        $feed = new OfflineConversionFeed(
            $logRecord->getCampaignParams()['gclid'],
            'Playwing Store Subscription Offline Conversion',
            $conversionTime
        );

        return new OfflineConversionFeedOperation(
            Operator::ADD,
            null,
            $feed
        );
    }


    private function updateLogRecord(AffiliateLog $logRecord, int $status, string $message): void
    {
        $logRecord->setStatus($status);
        $logRecord->setLog($message);
    }


    /**
     * Calculates the status to be set
     * @param bool $canBeRetried
     * @param int  $currentStatus
     * @return int
     */
    private function calculateStatus(bool $canBeRetried, int $currentStatus): int
    {
        $status = AffiliateLog::STATUS_FAILURE;
        if ($canBeRetried) {
            switch ($currentStatus) {
                case AffiliateLog::STATUS_WAITING:
                    $status = AffiliateLog::STATUS_RETRY6;
                    break;
                case AffiliateLog::STATUS_RETRY6:
                    $status = AffiliateLog::STATUS_RETRY24;
                    break;
            }
        }

        return $status;
    }
}