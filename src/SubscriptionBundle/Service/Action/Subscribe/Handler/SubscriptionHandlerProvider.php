<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 14:07
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;


use AppBundle\Entity\Carrier;

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

    private function ensureIsCorrect(SubscriptionHandlerInterface $handler)
    {
        $availableInterfaceString = json_encode([
            HasCommonFlow::class,
            HasCustomFlow::class
        ]);
        $handlerClass             = get_class($handler);

        if ((!$handler instanceof HasCommonFlow) && (!$handler instanceof HasCustomFlow)) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` should implement one of following two interfaces `%s`', $handlerClass, $availableInterfaceString));
        }

        if ($handler instanceof HasCommonFlow && $handler instanceof HasCustomFlow) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` cannot implement both flows. Please select one of `%s`', $handlerClass, $availableInterfaceString));
        }

    }

    /**
     * @param Carrier $carrier
     * @return SubscriptionHandlerInterface
     */
    public function getSubscriber(Carrier $carrier): SubscriptionHandlerInterface
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