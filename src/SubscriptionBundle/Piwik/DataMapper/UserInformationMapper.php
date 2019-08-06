<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 17:38
 */

namespace SubscriptionBundle\Piwik\DataMapper;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\Piwik\DTO\UserInformation;

class UserInformationMapper
{

    public function mapUserInformation(User $user, string $connectionType, string $affiliateString): UserInformation
    {
        return new UserInformation(
            $user->getCountry(),
            $user->getIp(),
            $connectionType,
            $user->getIdentifier(),
            $user->getBillingCarrierId(),
            $affiliateString
        );
    }
}