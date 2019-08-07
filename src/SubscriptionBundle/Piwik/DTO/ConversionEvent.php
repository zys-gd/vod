<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 17:48
 */

namespace SubscriptionBundle\Piwik\DTO;


class ConversionEvent
{
    /**
     * @var UserInformation
     */
    private $userInformation;
    /**
     * @var OrderInformation
     */
    private $orderInformation;

    /**
     * ConversionEvent constructor.
     * @param UserInformation  $userInformation
     * @param OrderInformation $orderInformation
     */
    public function __construct(UserInformation $userInformation, OrderInformation $orderInformation)
    {
        $this->userInformation  = $userInformation;
        $this->orderInformation = $orderInformation;
    }

    /**
     * @return UserInformation
     */
    public function getUserInformation(): UserInformation
    {
        return $this->userInformation;
    }

    /**
     * @return OrderInformation
     */
    public function getOrderInformation(): OrderInformation
    {
        return $this->orderInformation;
    }


}