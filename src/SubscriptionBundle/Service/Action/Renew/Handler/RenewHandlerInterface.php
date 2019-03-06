<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Service\Action\Renew\Handler;


interface RenewHandlerInterface
{
    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool;
}