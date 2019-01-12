<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:55
 */

namespace IdentificationBundle\WifiIdentification\Handler;


use IdentificationBundle\Entity\CarrierInterface;

interface WifiIdentificationHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool;

    public function getRedirectUrl();

    public function isPinSendAllowed($mobileNumber): bool ;

    public function areSMSSentByBilling(): bool;
}