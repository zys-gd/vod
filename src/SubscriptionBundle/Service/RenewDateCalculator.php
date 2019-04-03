<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.05.18
 * Time: 11:55
 */

namespace SubscriptionBundle\Service;


use Carbon\Carbon;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;

class RenewDateCalculator
{
    /**
     * @param Subscription $subscription
     * @return Carbon
     */
    public function calculateRenewDate(Subscription $subscription): Carbon
    {
        $subscriptionPack = $subscription->getSubscriptionPack();
        $periodicity      = $subscriptionPack->getPeriodicity();
        $renewDate        = $now = Carbon::now();
        switch ($periodicity) {
            case SubscriptionPack::DAILY:
                $renewDate = $now->addDay();
                break;
            case SubscriptionPack::WEEKLY:
                $renewDate = $now->addWeek();
                break;
            case SubscriptionPack::MONTHLY:
                $renewDate = $now->addMonth();
                break;
            case SubscriptionPack::CUSTOM_PERIODICITY:
                $renewDate = $now->addDays($subscriptionPack->getCustomRenewPeriod());
                break;
        }
        $preferredRenewalStart = $subscriptionPack->getPreferredRenewalStart()
            ? Carbon::instance($subscriptionPack->getPreferredRenewalStart())
            : Carbon::parse('00:00:00');

        $preferredRenewalEnd = $subscriptionPack->getPreferredRenewalEnd()
            ? Carbon::instance($subscriptionPack->getPreferredRenewalEnd())
            : Carbon::parse('23:59:59');
        $preferredRenewalStart->setDate($renewDate->year, $renewDate->month, $renewDate->day);
        $preferredRenewalEnd->setDate($renewDate->year, $renewDate->month, $renewDate->day);
        if (!$renewDate->between($preferredRenewalStart, $preferredRenewalEnd)) {
            if ($subscriptionPack->getPreferredRenewalStart()) {

                $min_epoch = strtotime($preferredRenewalStart);
                $max_epoch = strtotime($preferredRenewalEnd);

                $rand_epoch = rand($min_epoch, $max_epoch);

                $renewInterval =  new \DateTime();
                $renewInterval->setTimestamp($rand_epoch);

                $renewDate->setTime($renewInterval->format('h'), $renewInterval->format('i'));
            } else if ($subscriptionPack->getPreferredRenewalEnd()) {
                $renewDate->setTime(0, 0);
            }
        }
        return $renewDate;
    }
}