<?php

namespace SubscriptionBundle\Reminder;

use SubscriptionBundle\Reminder\Handler\ReminderHandlerInterface;

/**
 * Class ReminderHandlerProvider
 */
class ReminderHandlerProvider
{
    /**
     * @var ReminderHandlerInterface[]
     */
    private $handlers;

    /**
     * @param ReminderHandlerInterface $handler
     */
    public function addHandler(ReminderHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param int $billingCarrierId
     *
     * @return ReminderHandlerInterface|null
     */
    public function getHandler(int $billingCarrierId): ?ReminderHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($billingCarrierId)) {
                return $handler;
            }
        }

        return null;
    }
}