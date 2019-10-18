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
            (string)$visitDTO->getCountry(),
            (string)$visitDTO->getIp(),
            (string)$visitDTO->getConnection(),
            (string)$visitDTO->getMsisdn(),
            (int)$visitDTO->getOperator(),
            0,
            0,
            0,
            (string)$visitDTO->getAffiliate()
        );


        return new ConversionEvent($userInformation, '');
    }
}