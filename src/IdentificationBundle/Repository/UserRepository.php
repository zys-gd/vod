<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 16:23
 */

namespace IdentificationBundle\Repository;


use Doctrine\ORM\EntityRepository;
use IdentificationBundle\Entity\User;

class UserRepository extends EntityRepository
{

    public function findOneByIdentificationToken(string $token): ?User
    {
        return $this->findOneBy(['identificationToken' => $token]);
    }
}