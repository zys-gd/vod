<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Service\Action\Renew\Handler;


use IdentificationBundle\Entity\CarrierInterface;

class RenewHandlerProvider
{

    /**
     * @var RenewHandlerInterface[]
     */
    private $renewers = [];
    /**
     * @var DefaultHandler
     */
    private $defaultHandler;

    /**
     * RenewHandlerProvider constructor.
     * @param DefaultHandler $handler
     */
    public function __construct(DefaultHandler $handler)
    {
        $this->defaultHandler = $handler;
    }

    /**
     * @param RenewHandlerInterface $handler
     */
    public function addHandler(RenewHandlerInterface $handler)
    {
        $this->ensureIsCorrect($handler);

        $this->renewers[] = $handler;
    }


    private function ensureIsCorrect(RenewHandlerInterface $handler)
    {
        $availableInterfaceString = json_encode([
            HasCommonFlow::class,
        ]);
        $handlerClass             = get_class($handler);

        if ((!$handler instanceof HasCommonFlow)) {
            throw new \InvalidArgumentException(sprintf('Handler `%s` should implement one of following interfaces `%s`', $handlerClass, $availableInterfaceString));
        }

    }

    /**
     * @param CarrierInterface $carrier
     * @return RenewHandlerInterface
     */
    public function getRenewer(CarrierInterface $carrier): RenewHandlerInterface
    {
        /** @var RenewHandlerInterface $renewer */
        foreach ($this->renewers as $renewer) {
            if ($renewer->canHandle($carrier)) {
                return $renewer;
            }
        }

        return $this->defaultHandler;
    }

}