<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.08.19
 * Time: 17:40
 */

namespace SubscriptionBundle\Subscription\Unsubscribe;


use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\DataMapper\ConversionEventMapper;
use SubscriptionBundle\Piwik\EventPublisher;

class UnsubscribeEventTracker
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UnsubscribeEventChecker
     */
    private $unsubscribeEventChecker;
    /**
     * @var ConversionEventMapper
     */
    private $conversionEventMapper;
    /**
     * @var EventPublisher
     */
    private $eventPublisher;


    /**
     * UnsubscribeEventTracker constructor.
     * @param LoggerInterface         $logger
     * @param UnsubscribeEventChecker $unsubscribeEventChecker
     * @param ConversionEventMapper   $conversionEventMapper
     * @param EventPublisher          $eventPublisher
     */
    public function __construct(
        LoggerInterface $logger,
        UnsubscribeEventChecker $unsubscribeEventChecker,
        ConversionEventMapper $conversionEventMapper,
        EventPublisher $eventPublisher
    )
    {
        $this->logger                  = $logger;
        $this->unsubscribeEventChecker = $unsubscribeEventChecker;
        $this->conversionEventMapper   = $conversionEventMapper;
        $this->eventPublisher          = $eventPublisher;
    }

    public function trackUnsubscribe(
        User $user,
        Subscription $subscription,
        ProcessResult $processResult
    ): void
    {

        $this->logger->info('Trying to send unsubscribe event');
        if (!$this->unsubscribeEventChecker->isNeedToBeTracked($processResult)) {
            return;
        }
        $conversionEvent = $this->conversionEventMapper->map(
            'unsubscribe',
            $processResult,
            $user,
            $subscription
        );
        $this->eventPublisher->publish($conversionEvent);

    }
}