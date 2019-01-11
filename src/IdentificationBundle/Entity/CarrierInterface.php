<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 17:09
 */

namespace IdentificationBundle\Entity;


interface CarrierInterface
{
    public function getUuid(): string;

    public function getIsp(): string;

    public function getBillingCarrierId(): int;

    public function getCountryCode(): string;

    public function getOperatorId(): int ;
}