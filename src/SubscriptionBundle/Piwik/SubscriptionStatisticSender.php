<?php

namespace SubscriptionBundle\Piwik;

use App\Domain\Entity\Game;
use IdentificationBundle\Entity\User;
use PiwikBundle\Service\NewTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;

class SubscriptionStatisticSender
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
     * SubscriptionStatisticSender constructor.
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
        return $this->send($this->newTracker::TRACK_SUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
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
        return $this->send($this->newTracker::TRACK_RESUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
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
        return $this->send($this->newTracker::TRACK_RENEW, $user, $subscriptionEntity, $responseData, $conversionMode);
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
        return $this->send($this->newTracker::TRACK_UNSUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
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
    public function send(
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