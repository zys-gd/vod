<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 10:58
 */

namespace IdentificationBundle\Service\Action\Identification\Common;


use App\Utils\UuidGenerator;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;

class UserFactory
{
    public function create(string $msisdn, CarrierInterface $carrier): User
    {
        $user = new User(UuidGenerator::generate());

        $user->setIdentifier($msisdn);

        $user->setCarrier($carrier);


        return $user;
    }
}