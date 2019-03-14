<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.18
 * Time: 14:11
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Common;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;

class SubscriptionEligibilityChecker
{

    /**
     * @param Subscription $subscription
     * @return bool
     */
    public function isStatusOkForResubscribe(Subscription $subscription): bool
    {
        if ($subscription->isActive()) {
            return false;
        }

        if ($subscription->isPending()) {
            return false;
        }

        return true;
    }

    public function isResubscriptionAfterUnsubscribeCase(Subscription $subscription, SubscriptionPack $pack): bool
    {
        if (!$subscription->isInActive()) {
            return false;
        }

        if (!$pack->isResubAllowed()) {
            return false;
        }

        return true;

    }

    public function isNotFullyPaidSubscriptionCase(Subscription $subscription): bool
    {
        if ($subscription->getStatus() != Subscription::IS_ON_HOLD) {
            return false;
        }
        if ($subscription->getCurrentStage() != Subscription::ACTION_SUBSCRIBE) {
            return false;
        }
        if ($subscription->getError() != ProcessResult::ERROR_NOT_ENOUGH_CREDIT) {
            return false;
        }
        return true;


    }


}