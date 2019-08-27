<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 11:27
 */

namespace SubscriptionBundle\Subscription\Callback\Impl;


use Symfony\Component\HttpFoundation\Request;

interface HasCustomFlow
{
    public function process(Request $request, string $type);
}