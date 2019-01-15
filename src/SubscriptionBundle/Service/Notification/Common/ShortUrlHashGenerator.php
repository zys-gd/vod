<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 12:07
 */

namespace SubscriptionBundle\Service\Notification\Common;


class ShortUrlHashGenerator
{
    public function generate(): string
    {
        return strtr(base64_encode(openssl_random_pseudo_bytes(8) . 'salty'), "+/=", "XXX");
    }
}