<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 12:16
 */

namespace App\Domain\Repository;


use App\Domain\DTO\BatchOfGames;
use App\Domain\Entity\Game;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;

class GameRepository extends EntityRepository
{
    /**
     * @param int $offset
     * @param int $count
     *
     * @return BatchOfGames
     */
    public function findBatchOfGames(int $offset = 0, int $count = 4)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.deletedAt is NULL')
            ->andWhere('a.published = 1');

        $qb->setMaxResults($count);
        $qb->setFirstResult($offset);

        $paginator = new Paginator($qb);
        $total = $paginator->count();

        return new BatchOfGames(
            $qb->getQuery()->execute() ?? [],
            $total <= ($count + $offset)
        );
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
            ->andWhere('a.published = 1')
            ->setMaxResults($count)
            ->orderBy('RAND()')
            ->setParameter('gameUuid', $game->getUuid());

        return $qb->getQuery()->execute();
    }
}