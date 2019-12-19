<?php

namespace SubscriptionBundle\Subscription\Reminder\Handler;

/**
 * Interface ReminderHandlerInterface
 */
interface ReminderHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool;
}