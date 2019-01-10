<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.11.18
 * Time: 16:46
 */

namespace SubscriptionBundle\BillingFramework\Notification\API\DTO;


class SMSRequest
{
    private $clientUser;
    private $urlId;
    private $subscriptionPlan;
    private $lang;

    /**
     * SMSRequest constructor.
     * @param $clientUser
     * @param $urlId
     * @param $subscriptionPlan
     * @param $lang
     */
    public function __construct(string $clientUser, string $urlId, string $subscriptionPlan, string $lang)
    {
        $this->clientUser       = $clientUser;
        $this->urlId            = $urlId;
        $this->subscriptionPlan = $subscriptionPlan;
        $this->lang             = $lang;
    }

    /**
     * @return mixed
     */
    public function getClientUser(): string
    {
        return $this->clientUser;
    }

    /**
     * @return mixed
     */
    public function getUrlId(): string
    {
        return $this->urlId;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionPlan(): string
    {
        return $this->subscriptionPlan;
    }

    /**
     * @return mixed
     */
    public function getLang(): string
    {
        return $this->lang;
    }


}