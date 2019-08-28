<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 12:56
 */

namespace App\Domain\Service\Games;


use App\Domain\Entity\GameImage;

class GameImagesSerializer
{
    /**
     * @var ImagePathProvider
     */
    private $imagePathProvider;


    /**
     * GameSerializer constructor.
     * @param ImagePathProvider $provider
     */
    public function __construct(ImagePathProvider $provider)
    {
        $this->imagePathProvider = $provider;
    }

    /**
     * @param GameImage[] $gameImages
     *
     * @return array
     */
    public function serializeGameImages(array $gameImages): array
    {
        $aGameImages = [];
        foreach ($gameImages ?? [] as $gameImage) {
            $aGameImages[] = [
                'uuid' => $gameImage->getUuid(),
                'iconPath' => $this->imagePathProvider->getGameScreenshotPath($gameImage->getName())
            ];
        }
        return $aGameImages;
    }
}