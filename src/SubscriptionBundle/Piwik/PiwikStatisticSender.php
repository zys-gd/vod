<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.18
 * Time: 12:13
 */

namespace SubscriptionBundle\Piwik;


use PiwikBundle\Service\NewTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use UserBundle\Entity\BillableUser;

class PiwikStatisticSender
{
    /**
     * @var NewTracker
     */
    private $newTracker;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * PiwikStatisticSender constructor.
     * @param NewTracker $newTracker
     */
    public function __construct(NewTracker $newTracker, LoggerInterface $logger)
    {
        $this->newTracker = $newTracker;
        $this->logger     = $logger;
    }

    public function trackSubscribe(
        BillableUser $billableUser,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        return $this->send(NewTracker::TRACK_SUBSCRIBE, $billableUser, $subscriptionEntity, $responseData, $conversionMode);
    }

    public function trackResubscribe(
        BillableUser $billableUser,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        return $this->send(NewTracker::TRACK_RESUBSCRIBE, $billableUser, $subscriptionEntity, $responseData, $conversionMode);
    }

    public function trackRenew(
        BillableUser $billableUser,
        Subscription $subscriptionEntity,
        $responseData,
        $conversionMode = null
    )
    {
        return $this->send(NewTracker::TRACK_RENEW, $billableUser, $subscriptionEntity, $responseData, $conversionMode);
    }

    public function trackUnsubscribe(
        BillableUser $billableUser,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        return $this->send(NewTracker::TRACK_UNSUBSCRIBE, $billableUser, $subscriptionEntity, $responseData, $conversionMode);
    }

    public function trackDownload(
        BillableUser $billableUser,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        return $this->send(NewTracker::TRACK_DOWNLOAD, $billableUser, $subscriptionEntity, $responseData, $conversionMode);
    }


    public function send(
        string $trackEventName,
        BillableUser $billableUser,
        Subscription $subscriptionEntity,
        ProcessResult $responseData,
        $conversionMode = null
    )
    {
        try {
            $this->logger->info('Trying to send piwik event', [
                'eventName' => $trackEventName
            ]);
            $result = $this->newTracker->$trackEventName(
                $billableUser,
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