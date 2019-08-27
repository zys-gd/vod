<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.03.19
 * Time: 16:10
 */

namespace SubscriptionBundle\Subscription\Renew\Common;


use SubscriptionBundle\Entity\SubscriptionPack;

class RenewAlertTypeProvider
{
    public function getForSubscriptionPack(SubscriptionPack $pack): string
    {
        if ($pack->isFirstSubscriptionPeriodIsFree()) {
            return 'notify_renew_trial';
        } else {
            return 'notify_renew';
        }
    }
}