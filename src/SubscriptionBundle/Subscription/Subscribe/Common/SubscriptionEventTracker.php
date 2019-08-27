<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.03.19
 * Time: 18:52
 */

namespace SubscriptionBundle\Subscription\Subscribe\Common;


use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\DataMapper\ConversionEventMapper;
use SubscriptionBundle\Piwik\EventPublisher;

class SubscriptionEventTracker
{

    /**
     * @var SubscriptionEventChecker
     */
    private $trackPossibilityChecker;
    /**
     * @var ConversionEventMapper
     */
    private $conversionEventMapper;
    /**
     * @var EventPublisher
     */
    private $eventPublisher;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * SubscriptionEventTracker constructor.
     * @param SubscriptionEventChecker $trackPossibilityChecker
     * @param ConversionEventMapper        $conversionEventMapper
     * @param EventPublisher               $eventPublisher
     * @param LoggerInterface              $logger
     */
    public function __construct(
        SubscriptionEventChecker $trackPossibilityChecker,
        ConversionEventMapper $conversionEventMapper,
        EventPublisher $eventPublisher,
        LoggerInterface $logger
    )
    {

        $this->trackPossibilityChecker     = $trackPossibilityChecker;
        $this->conversionEventMapper       = $conversionEventMapper;
        $this->eventPublisher              = $eventPublisher;
        $this->logger                      = $logger;
    }
    public function trackSubscribe(Subscription $subscription, ProcessResult $response): void
    {
        $this->logger->info('Trying to send subscribe event');
        if (!$this->trackPossibilityChecker->isSubscribeNeedToBeTracked($response)) {
            return;
        }
        try {
            $conversionEvent = $this->conversionEventMapper->map(
                'subscribe',
                $response,
                $subscription->getUser(),
                $subscription
            );
            $result          = $this->eventPublisher->publish($conversionEvent);
            $this->logger->info('Sending is finished', ['result' => $result]);
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', [
                'msg'  => $ex->getMessage(),
                'line' => $ex->getLine(),
                'code' => $ex->getCode()
            ]);
        }
    }

    public function trackResubscribe(Subscription $subscription, ProcessResult $response): void
    {
        $this->logger->info('Trying to send resubscribe event');
        if (!$this->trackPossibilityChecker->isSubscribeNeedToBeTracked($response)) {
            return;
        }
        try {
            $conversionEvent = $this->conversionEventMapper->map(
                'resubscribe',
                $response,
                $subscription->getUser(),
                $subscription
            );
            $result          = $this->eventPublisher->publish($conversionEvent);
            $this->logger->info('Sending is finished', ['result' => $result]);
        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', [
                'msg'  => $ex->getMessage(),
                'line' => $ex->getLine(),
                'code' => $ex->getCode()
            ]);
        }
    }
}