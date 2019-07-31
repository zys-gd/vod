<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 18.07.19
 * Time: 12:23
 */

namespace App\Domain\DTO;


class SizeData
{
    /**
     * @var int
     */
    private $height;
    /**
     * @var int
     */
    private $width;

    /**
     * SizeData constructor.
     * @param int $height
     * @param int $width
     */
    public function __construct(int $height, int $width)
    {
        $this->height = $height;
        $this->width  = $width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }


}