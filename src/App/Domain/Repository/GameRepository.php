<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 12:16
 */

namespace App\Domain\Repository;


use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository
{
    public function findBatchOfGames(int $offset = 0, int $count = 4)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->setMaxResults($count);
        $qb->setFirstResult($offset);


        return $qb->getQuery()->execute();
    }

}