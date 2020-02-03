<?php

namespace Carriers\BeelineKZ\SMS;

use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Reminder\DTO\RemindSettings;
use SubscriptionBundle\Reminder\Handler\ReminderHandlerInterface;

/**
 * Class BeelineKZReminderHandler
 */
class BeelineKZReminderHandler implements ReminderHandlerInterface
{
    const DAYS_INTERVAL = 7;

    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::BEELINE_KAZAKHSTAN_DOT;
    }

    /**
     * @return RemindSettings
     */
    public function getRemind(): RemindSettings
    {
        $text = '_autologin_url_ сілтемесін басып, барлық спорттық видеоларды көріңіз және қолданбаны жаңартыңыз. Жазылысты тоқтату үшін мынаны басыңыз [_unsub_url_]';

        return new RemindSettings(self::DAYS_INTERVAL, $text);
    }
}