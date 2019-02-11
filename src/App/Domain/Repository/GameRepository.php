<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 12:16
 */

namespace App\Domain\Repository;


use App\Domain\Entity\Game;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;

class GameRepository extends EntityRepository
{
    /**
     * @param int $offset
     * @param int $count
     *
     * @return mixed
     */
    public function findBatchOfGames(int $offset = 0, int $count = 4)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.deletedAt is NULL');

        $qb->setMaxResults($count);
        $qb->setFirstResult($offset);


        return $qb->getQuery()->execute();
    }

    /**
     * @param Game  $game
     * @param array $excluded
     * @param int   $count
     *
     * @return Game[]
     */
    public function getSimilarGames(Game $game, $excluded = [], int $count = 6)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.deletedAt is NULL')
            ->andWhere('a.uuid != :gameUuid')
            ->setMaxResults($count)
            ->orderBy('RAND()')
            ->setParameter('gameUuid', $game->getUuid());

        return $qb->getQuery()->execute();
    }
}