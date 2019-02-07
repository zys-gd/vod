<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 12:56
 */

namespace App\Domain\Service\Games;


use App\Domain\Entity\Game;

class GameSerializer
{
    /**
     * @var IconPathProvider
     */
    private $provider;


    /**
     * GameSerializer constructor.
     * @param IconPathProvider $provider
     */
    public function __construct(IconPathProvider $provider)
    {
        $this->provider = $provider;
    }

    public function serializeGame(Game $game): array
    {
        return [
            'uuid'     => $game->getUuid(),
            'title'    => $game->getTitle(),
            'iconPath' => $this->provider->getFullPath($game->getIconPath())
        ];
    }
}