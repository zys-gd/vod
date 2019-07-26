<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.07.19
 * Time: 14:53
 */

namespace SubscriptionBundle\CAPTool\Subscription\Notificaton;


class EmailProvider
{
    /**
     * @var string
     */
    private $mailTo;
    /**
     * @var string
     */
    private $mailFrom;


    /**
     * EmailProvider constructor.
     * @param string $mailTo
     * @param string $mailFrom
     */
    public function __construct(string $mailTo, string $mailFrom)
    {
        $this->mailTo   = $mailTo;
        $this->mailFrom = $mailFrom;
    }
}