<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.10.19
 * Time: 14:45
 */

namespace SubscriptionBundle\Piwik\DataMapper;


use SubscriptionBundle\Piwik\DTO\ConversionEvent;
use SubscriptionBundle\Piwik\DTO\UserInformation;

class UnsubscribeClickEventMapper
{

    public function map(UserInformation $userInformation): ConversionEvent
    {
        return new ConversionEvent($userInformation, 'unsubscribe-click-ok');
    }
}