<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.10.19
 * Time: 17:13
 */

namespace IdentificationBundle\WifiIdentification\Handler;


interface HasCustomMsisdnCleaning
{
    public function cleanMsisdn(string $msisdn): string;
}