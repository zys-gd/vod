<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 18.07.19
 * Time: 12:22
 */

namespace App\Domain\Service\Games;



use App\Domain\DTO\SizeData;

class GameImageSizeProvider
{

    public function getPosterSmallSize(): SizeData
    {
        return new SizeData(500, 500);
    }

    public function getPosterMediumSize(): SizeData
    {
        return new SizeData(500, 500);
    }

    public function getPosterLargeSize(): SizeData
    {
        return new SizeData(500, 500);
    }

    public function getScreenshotSmallSize(): SizeData
    {
        return new SizeData(515, 515);
    }

    public function getScreenshotMediumSize(): SizeData
    {
        return new SizeData(430, 375);
    }

    public function getScreenshotLargeSize(): SizeData
    {
        return new SizeData(515, 515);
    }

    public function getThumbnailMediumSize(): SizeData
    {
        return new SizeData(170, 170);
    }

    public function getCarouselLargeSize(): SizeData
    {
        return new SizeData(515, 515);

    }

    public function getCarouselMediumSize(): SizeData
    {
        return new SizeData(515, 515);

    }

    public function getCarouselSmallSize(): SizeData
    {
        return new SizeData(515, 515);

    }

}