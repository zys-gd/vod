<?php

namespace SubscriptionBundle\Piwik;

use App\Domain\Constants\ConstFakeTrackerActions;
use IdentificationBundle\Entity\User;
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
     * PiwikStatisticSender constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger     = $logger;
    }

    public function trackSubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        return $this->send(ConstFakeTrackerActions::TRACK_SUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
    }

    public function trackResubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        return $this->send(ConstFakeTrackerActions::TRACK_RESUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
    }

    public function trackRenew(
        User $user,
        Subscription $subscriptionEntity,
        $responseData,
        $conversionMode = null
    )
    {
        return $this->send(ConstFakeTrackerActions::TRACK_RENEW, $user, $subscriptionEntity, $responseData, $conversionMode);
    }

    public function trackUnsubscribe(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        return $this->send(ConstFakeTrackerActions::TRACK_UNSUBSCRIBE, $user, $subscriptionEntity, $responseData, $conversionMode);
    }

    public function trackDownload(
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        return $this->send(ConstFakeTrackerActions::TRACK_DOWNLOAD, $user, $subscriptionEntity, $responseData, $conversionMode);
    }


    public function send(
        string $trackEventName,
        User $user,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => $trackEventName
            ]);
            //TODO: implement tracker
            // $result = $this->newTracker->$trackEventName(
            //     $user,
            //     $subscriptionEntity,
            //     $responseData,
            //     $conversionMode
            // );
            $result = true;
            $this->logger->info('Sending is finished', ['result' => $result]);
            return $result;

        } catch (\Exception $ex) {

            $this->logger->info('Exception on piwik sending', ['msg' => $ex->getMessage()]);
            return false;
        }
    }
}