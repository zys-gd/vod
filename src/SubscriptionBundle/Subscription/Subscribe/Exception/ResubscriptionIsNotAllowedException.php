<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 14:56
 */

namespace SubscriptionBundle\Subscription\Subscribe\Exception;


use SubscriptionBundle\Entity\SubscriptionPack;

class ResubscriptionIsNotAllowedException extends \Exception
{
    /**
     * @var SubscriptionPack
     */
    private $subscriptionPack;

    /**
     * ResubscriptionIsNotAllowedException constructor.
     * @param SubscriptionPack $subscriptionPack
     */
    public function __construct(SubscriptionPack $subscriptionPack)
    {
        $this->subscriptionPack = $subscriptionPack;

        parent::__construct('Resubscription is not allowed');
    }

    /**
     * @return SubscriptionPack
     */
    public function getSubscriptionPack(): SubscriptionPack
    {
        return $this->subscriptionPack;
    }


}