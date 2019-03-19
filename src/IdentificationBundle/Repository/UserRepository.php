<?php

namespace IdentificationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use IdentificationBundle\Entity\User;

/**
 * Class UserRepository
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string $msisdn
     *
     * @return User|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByPartialMsisdnMatch(string $msisdn): ?User
    {
        $qb = $this->createQueryBuilder('u');

        $query = $qb
            ->where("u.identifier LIKE :msisdn")
            ->setParameter('msisdn', "$msisdn%")
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function findOneByMsisdn(string $msisdn): ?User
    {
        return $this->findOneBy(['identifier' => $msisdn]);
    }

    /**
     * @param string $token
     *
     * @return User|null
     */
    public function findOneByIdentificationToken(string $token): ?User
    {
        return $this->findOneBy(['identificationToken' => $token]);
    }

    /**
     * @param string $urlId
     *
     * @return User|null
     */
    public function findOneByUrlId(string $urlId): ?User
    {
        return $this->findOneBy(['urlId' => $urlId]);
    }
}