<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 17:10
 */

namespace IdentificationBundle\WifiIdentification\Service;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class MsisdnCleaner
{
    public function clean(string $msisdn, CarrierInterface $carrier): string
    {
        if ($carrier->getBillingCarrierId() == 381 || $carrier->getBillingCarrierId() == 2207) {
            return str_replace('+', '', $msisdn);
        }

        $phoneUtil   = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneUtil->parse(preg_replace('/[+\-_() ]/', '', $msisdn), $carrier->getCountryCode());

        return str_replace('+', '', $phoneUtil->format($phoneNumber, PhoneNumberFormat::E164));
    }
}