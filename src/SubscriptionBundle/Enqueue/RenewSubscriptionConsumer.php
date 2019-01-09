<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 04/09/17
 * Time: 6:45 PM
 */

namespace SubscriptionBundle\Enqueue;


use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\Command\RenewCommand;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\Action\Renew\Renewer;

class RenewSubscriptionConsumer implements Processor, TopicSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepo;
    /**
     * @var Renewer
     */
    private $renewer;

    /**
     * RenewSubscriptionConsumer constructor.
     * @param SubscriptionRepository    $subscriptionRepo
     * @param Renewer                   $renewer
     * @param LoggerInterface           $logger
     * @internal param LoggerInterface $logger
     */
    public function __construct(
        SubscriptionRepository $subscriptionRepo,
        Renewer $renewer,
        LoggerInterface $logger
    )
    {
        $this->logger              = $logger;
        $this->subscriptionRepo    = $subscriptionRepo;
        $this->renewer             = $renewer;
    }

    /**
     * The method has to return either self::ACK, self::REJECT, self::REQUEUE string.
     *
     * The method also can return an object.
     * It must implement __toString method and the method must return one of the constants from above.
     *
     * @param Message $message
     * @param Context $context
     *
     * @return string|object with __toString method implemented
     */
    public function process(Message $message, Context $context)
    {

        $subscriptionId = $message->getBody();
// comment for check
        $subscription = $this->subscriptionRepo->find($subscriptionId);

        $result = self::ACK;
        if ($subscription instanceof Subscription) {
            $subscriptionId = $subscription->getUuid();
            try {
                $this->logger->debug(
                    "Received subscription for renewal. Subscription id: {$subscriptionId}",
                    ["subscription" => $subscription]
                );
                $response = $this->renewer->renew($subscription);

                $this->logger->debug(
                    "Received subscription renewal response. Subscription id: {$subscriptionId}",
                    ["subscription" => $subscription, "response" => $response]
                );
            } catch (BillingFrameworkException $e) {
                $result = self::REJECT;
                $this->logger->error("billing framework does not work in proper way. id: {$subscriptionId}",
                    ["subscription" => $subscription]
                );
            } catch (\Exception $e) {
                $this->logger->error("Subscription renewal failed. Subscription id: {$subscriptionId}",
                    ["subscription" => $subscription, "exception" => $e]
                );
                $result = self::REQUEUE;
            }
        } else {
            $result = self::REJECT;
        }
        return $result;
    }

    public static function getSubscribedTopics()
    {
        return [RenewCommand::SUBSCRIPTION_RENEW_TOPIC];
    }
}