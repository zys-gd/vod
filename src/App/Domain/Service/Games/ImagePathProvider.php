<?php

namespace App\Domain\Service\Games;

/**
 * Class ImagePathProvider
 */
class ImagePathProvider
{
    const GAME_THUMBNAIL_SMALL_PATH = 'uploads/cache/game_thumbnail_small/images/game_thumbnails';
    const GAME_SCREENSHOT_PATH = 'uploads/images/game_screenshots';
    const GAME_POSTER_PATH = 'uploads/cache/similar_game/images/game_icons';

    /**
     * @var string
     */
    private $imagesHost;

    /**
     * ImagePathProvider constructor
     *
     * @param string $imagesHost
     */
    public function __construct(string $imagesHost)
    {
        $this->imagesHost = $imagesHost;
    }

    public function getFullPath(string $image): string
    {
        return sprintf('%s/uploads/cache/similar_game/%s', $this->imagesHost, $image);
    }

    public function getGameScreenshotPath(string $imageName, bool $isRelativePath = false): string
    {
        $relativePath = sprintf('/%s/%s', self::GAME_SCREENSHOT_PATH, $imageName);

        return $isRelativePath ? $relativePath : $this->imagesHost . $relativePath;
    }

    public function getGameSmallThumbnailPath(string $imageName, bool $isRelativePath = false): string
    {
        $relativePath = sprintf('/%s/%s', self::GAME_THUMBNAIL_SMALL_PATH, $imageName);

        return $isRelativePath ? $relativePath : $this->imagesHost . $relativePath;
    }

    public function getGamePosterPath(string $imageName, bool $isRelativePath = false): string
    {
        $relativePath = sprintf('/%s/%s', self::GAME_POSTER_PATH, $imageName);

        return $isRelativePath ? $relativePath : $this->imagesHost . $relativePath;
    }
}