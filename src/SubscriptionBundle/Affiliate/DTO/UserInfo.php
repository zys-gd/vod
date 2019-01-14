<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.05.18
 * Time: 17:22
 */

namespace SubscriptionBundle\Affiliate\DTO;


class UserInfo
{
    private $userIp;
    private $msidsn;

    /**
     * UserInfo constructor.
     */
    public function __construct($userIp, $msidsn)
    {
        $this->userIp = $userIp;
        $this->msidsn = $msidsn;
    }

    /**
     * @return mixed
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * @return mixed
     */
    public function getMsidsn()
    {
        return $this->msidsn;
    }


}