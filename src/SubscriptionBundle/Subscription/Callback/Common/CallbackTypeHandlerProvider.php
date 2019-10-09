<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.05.18
 * Time: 10:55
 */

namespace SubscriptionBundle\Subscription\Callback\Common;


use SubscriptionBundle\Subscription\Callback\Common\Handler\CallbackHandlerInterface;

class CallbackTypeHandlerProvider
{
    /**
     * @var \SubscriptionBundle\Subscription\Callback\Common\Handler\CallbackHandlerInterface[]
     */
    private $handlers;


    /**
     * HelperProvider constructor.
     * @param array $handlers
     */
    public function __construct(...$handlers)
    {
        foreach ($handlers as $handler) {

            if (!$handler instanceof CallbackHandlerInterface) {
                throw new \InvalidArgumentException(sprintf('%s is not should be instance of %s', get_class($handler), CallbackHandlerInterface::class));
            }

            $this->handlers[] = $handler;
        }
    }

    public function getHandler($type)
    {
        foreach ($this->handlers as $helper) {
            if ($helper->isSupport($type)) {
                return $helper;
            }
        }
        throw new \InvalidArgumentException("Unsupported callback type");
    }
}