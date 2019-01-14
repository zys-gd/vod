<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Handler;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 14:04
 */
interface SubscriptionHandlerInterface
{
    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool;

}