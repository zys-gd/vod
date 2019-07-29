<?php

namespace SubscriptionBundle\Subscription\Subscribe\Handler;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;

/**
 * Class SubscriptionHandlerProvider
 */
class SubscriptionHandlerProvider
{
    /**
     * @var SubscriptionHandlerInterface[]
     */
    private $subscribers = [];
    /**
     * @var DefaultHandler
     */
    private $defaultHandler;

    /**
     * SubscriptionHandlerProvider constructor.
     * @param DefaultHandler $handler
     */
    public function __construct(DefaultHandler $handler)
    {
        $this->defaultHandler = $handler;
    }

    /**
     * @param SubscriptionHandlerInterface $handler
     */
    public function addHandler(SubscriptionHandlerInterface $handler)
    {
        $this->ensureIsCorrect($handler);

        $this->subscribers[] = $handler;
    }

    /**
     * @param SubscriptionHandlerInterface $handler
     */
    private function ensureIsCorrect(SubscriptionHandlerInterface $handler): void
    {
        $availableInterfaceString = json_encode([
            HasCommonFlow::class,
            HasCustomFlow::class,
            HasConsentPageFlow::class
        ]);

        $handlerClass = get_class($handler);

        if ((!$handler instanceof HasCommonFlow) && (!$handler instanceof HasCustomFlow) && (!$handler instanceof HasConsentPageFlow)) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` should implement one of following two interfaces `%s`', $handlerClass, $availableInterfaceString));
        }

        if ($handler instanceof HasCommonFlow && $handler instanceof HasCustomFlow) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` cannot implement both flows. Please select one of `%s`', $handlerClass, $availableInterfaceString));
        }
    }

    /**
     * @param CarrierInterface $carrier
     * @return SubscriptionHandlerInterface
     */
    public function getSubscriber(CarrierInterface $carrier): SubscriptionHandlerInterface
    {
        /** @var SubscriptionHandlerInterface $subscriber */
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->canHandle($carrier)) {
                return $subscriber;
            }
        }

        return $this->defaultHandler;
    }

}