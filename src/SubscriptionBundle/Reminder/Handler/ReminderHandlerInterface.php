<?php

namespace SubscriptionBundle\Reminder\Handler;

use SubscriptionBundle\Reminder\DTO\RemindSettings;

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

    /**
     * @return RemindSettings
     */
    public function getRemind(): RemindSettings;
}