<?php

namespace App\Domain\DTO;

use App\Domain\Entity\Game;

class BatchOfGames
{
    /**
     * @var Game[]
     */
    private $games;
    /**
     * @var bool
     */
    private $isLast;

    /**
     * BatchOfGames constructor.
     * @param Game[] $games
     * @param bool   $isLast
     */
    public function __construct(array $games, bool $isLast)
    {
        $this->games = $games;
        $this->isLast = $isLast;
    }

    /**
     * @return Game[]
     */
    public function getGames(): array
    {
        return $this->games;
    }

    /**
     * @return bool
     */
    public function isLast(): bool
    {
        return $this->isLast;
    }
}