<?php

namespace SubscriptionBundle\Piwik;

use App\Domain\Entity\Game;
use IdentificationBundle\Entity\User;
use PiwikBundle\Service\NewTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

class PiwikStatisticSender
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var NewTracker
     */
    private $newTracker;

    /**
     * PiwikStatisticSender constructor.
     *
     * @param LoggerInterface $logger
     * @param NewTracker $newTracker
     */
    public function __construct(LoggerInterface $logger, NewTracker $newTracker)
    {
        $this->logger = $logger;
        $this->newTracker = $newTracker;
    }

    /**
     * @param User $user
     * @param Subscription $subscriptionEntity
     * @param ProcessResult $responseData
     * @param bool|null $conversionMode
     *
     * @return bool
     */
    public function trackSubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        return $this->trackWithBillingResponse($this->newTracker::TRACK_SUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
    }

    /**
     * @param User $user
     * @param Subscription $subscriptionEntity
     * @param ProcessResult $responseData
     * @param bool|null $conversionMode
     *
     * @return bool
     */
    public function trackResubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        return $this->trackWithBillingResponse($this->newTracker::TRACK_RESUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
    }

    /**
     * @param User $user
     * @param Subscription $subscriptionEntity
     * @param ProcessResult $responseData
     * @param bool|null $conversionMode
     *
     * @return bool
     */
    public function trackRenew(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        return $this->trackWithBillingResponse($this->newTracker::TRACK_RENEW, $user, $subscriptionEntity, $responseData, $conversionMode);
    }

    /**
     * @param User $user
     * @param Subscription $subscriptionEntity
     * @param ProcessResult $responseData
     * @param bool|null $conversionMode
     *
     * @return bool
     */
    public function trackUnsubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        return $this->trackWithBillingResponse($this->newTracker::TRACK_UNSUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
    }

    /**
     * @param User $user
     * @param Subscription $subscriptionEntity
     * @param Game $game
     * @param bool|null $conversionMode
     *
     * @return bool
     */
    public function trackDownload(
        User $user,
        Subscription $subscriptionEntity,
        Game $game,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => $this->newTracker::TRACK_DOWNLOAD
            ]);

            $result = $this->newTracker->trackDownload(
                $user,
                $game,
                $subscriptionEntity,
                $conversionMode
            );

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage()]);

            return false;
        }
    }


    public function trackVisit(
        User $user,
        $connection,
        $operator,
        $country,
        string $ip,
        string $msisdn,
        $affiliate = null,
        $campaign = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => 'pageVisit'
            ]);

            $result = $this->newTracker->trackPage(
                $user,
                $connection,
                $operator,
                $country,
                $ip,
                $msisdn,
                $affiliate,
                $campaign
            );

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage()]);

            return false;
        }
    }

    /**
     * @param string $trackEventName
     * @param User $user
     * @param Subscription $subscriptionEntity
     * @param ProcessResult $responseData
     *
     * @param bool|null $conversionMode
     *
     * @return bool
     */
    public function trackWithBillingResponse(
        string $trackEventName,
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        bool $conversionMode = null
    ): bool
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => $trackEventName
            ]);

             $result = $this->newTracker->$trackEventName(
                 $user,
                 $subscriptionEntity,
                 $responseData,
                 $conversionMode
             );

            $this->logger->info('Sending is finished', ['result' => $result]);

            return $result;
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage()]);

            return false;
        }
    }
}