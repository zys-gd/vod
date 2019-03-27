<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.06.18
 * Time: 18:47
 */

namespace Tests\Core;


use App\Domain\Entity\Carrier;
use App\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;

class TestEntityProvider
{
    /**
     * @param Carrier $carrier
     * @param string  $identificationRequestToken
     *
     * @return User
     * @throws \Exception
     */
    public static function createUserWithIdentificationRequest(Carrier $carrier, string $identificationRequestToken)
    {
        $identifier = md5(mt_rand(0, 5000));

        $user = new User(UuidGenerator::generate());
        $user->setIdentifier($identifier);
        $user->setCountry('country');
        $user->setIp('255.255.255.255');
        $user->setUrlId(sprintf('urlid_%s', $identifier));
        $user->setCarrier($carrier);
        $user->setIdentificationToken($identificationRequestToken);

        return $user;
    }

    /**
     * @param User             $billableUser
     * @param SubscriptionPack $subscriptionPack
     * @param string           $status
     * @param string           $stage
     * @param string|null      $error
     *
     * @return Subscription
     * @throws \Exception
     */
    public static function createSubscription(
        User $billableUser,
        SubscriptionPack $subscriptionPack,
        string $status = Subscription::IS_ACTIVE,
        string $stage = Subscription::ACTION_SUBSCRIBE,
        string $error = null
    )
    {

        $subscription = new Subscription(UuidGenerator::generate());

        $subscription->setUser($billableUser);
        $subscription->setStatus($status);
        $subscription->setCurrentStage($stage);
        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setAffiliateToken(json_encode(['cid' => 'example_cid']));
        $error && $subscription->setError($error);

        return $subscription;

    }

}