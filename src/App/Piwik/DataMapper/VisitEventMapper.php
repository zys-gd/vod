<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.09.19
 * Time: 15:10
 */

namespace App\Piwik\DataMapper;


use SubscriptionBundle\Piwik\DTO\ConversionEvent;
use SubscriptionBundle\Piwik\DTO\UserInformation;

class VisitEventMapper
{



    public function map(\App\Piwik\DTO\VisitDTO $visitDTO): ConversionEvent
    {

        $userInformation = new UserInformation(
            $visitDTO->getCountry(),
            $visitDTO->getIp(),
            (string)$visitDTO->getConnection(),
            $visitDTO->getMsisdn(),
            $visitDTO->getOperator(),
            0,
            0,
            0,
            $visitDTO->getAffiliate()
        );


        return new ConversionEvent($userInformation);
    }
}