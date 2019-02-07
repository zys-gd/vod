<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 13:05
 */

namespace App\Domain\Service\Games;


class IconPathProvider
{
    /**
     * @var string
     */
    private $imagesHost;


    /**
     * IconPathProvider constructor.
     */
    public function __construct(string $imagesHost)
    {
        $this->imagesHost = $imagesHost;
    }

    public function getFullPath(string $image): string
    {
        return sprintf('%s/uploads/cache/similar_game/%s', $this->imagesHost, $image);
    }
}