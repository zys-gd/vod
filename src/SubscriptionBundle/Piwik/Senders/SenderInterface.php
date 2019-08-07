<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.08.19
 * Time: 13:50
 */

namespace SubscriptionBundle\Piwik\Senders;


interface SenderInterface
{
    public function sendEvent($data, string $timestamp): bool;
}