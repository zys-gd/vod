<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.01.19
 * Time: 14:19
 */

namespace SubscriptionBundle\Twig;


class SubscriptionStatusExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('isSubscribed', function () {
            return true;
        })];
    }


}