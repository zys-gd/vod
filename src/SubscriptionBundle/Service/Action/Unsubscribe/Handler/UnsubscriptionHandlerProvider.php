<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 10:06
 */

namespace SubscriptionBundle\Service\Action\Unsubscribe\Handler;


use IdentificationBundle\Entity\CarrierInterface;

class UnsubscriptionHandlerProvider
{


    /**
     * @var UnsubscriptionHandlerInterface[]
     */
    private $unsubscribers = [];

    private $defaultHandler;

    /**
     * UnsubscriptionHandlerProvider constructor.
     * @param $defaultHandler
     */
    public function __construct(DefaultHandler $defaultHandler)
    {
        $this->defaultHandler = $defaultHandler;
    }

    /**
     * @param UnsubscriptionHandlerInterface $handler
     */
    public function addHandler(UnsubscriptionHandlerInterface $handler)
    {
        $this->unsubscribers[] = $handler;
    }

    /**
     * @param Carrier $carrier
     * @return UnsubscriptionHandlerInterface
     */
    public function getUnsubscriber(Carrier $carrier): UnsubscriptionHandlerInterface
    {
        /** @var UnsubscriptionHandlerInterface $subscriber */
        foreach ($this->unsubscribers as $subscriber) {
            if ($subscriber->canHandle($carrier)) {
                return $subscriber;
            }
        }

        return $this->defaultHandler;
    }

}