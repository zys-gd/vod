<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 17:38
 */

namespace SubscriptionBundle\Piwik\DataMapper;


use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\DTO\UserInformation;
use SubscriptionBundle\Piwik\Service\AffiliateStringProvider;

class UserInformationMapper
{
    /**
     * @var AffiliateStringProvider
     */
    private $affiliateStringProvider;


    /**
     * UserInformationMapper constructor.
     * @param AffiliateStringProvider $affiliateStringProvider
     */
    public function __construct(AffiliateStringProvider $affiliateStringProvider)
    {
        $this->affiliateStringProvider = $affiliateStringProvider;
    }

    public function mapUserInformation(User $user, Subscription $subscription, int $providerId): UserInformation
    {
        $affiliateString = $this->affiliateStringProvider->getAffiliateString($subscription);

        return new UserInformation(
            $user->getCountry(),
            $user->getIp(),
            // Kinda risky, because im not sure if we always have userConnection type.
            // We can use $this->maxMindIpInfo->getConnectionType() instead but what about renews etc?
            (string)$user->getConnectionType(),
            $user->getIdentifier(),
            (int)$user->getBillingCarrierId(),
            $providerId,
            0,
            0,
            $affiliateString
        );
    }
}