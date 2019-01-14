<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 16:25
 */

namespace SubscriptionBundle\Affiliate\Service;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\Affiliate\DTO\UserInfo;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserInfoMapper
{
    public function mapFromCurrentSessionData(SessionInterface $session): UserInfo
    {
        return new UserInfo($session->get('user_ip'), $session->get('msisdn'));
    }

    public function mapFromUser(User $billableUser): UserInfo
    {
        return new UserInfo($billableUser->getIp(), $billableUser->getIdentifier());
    }
}