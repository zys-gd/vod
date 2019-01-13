<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 13.01.2019
 * Time: 21:04
 */

namespace IdentificationBundle\Identification\DTO;


class ISPData
{
    /**
     * @var int
     */
    private $carrierId;

    /**
     * ISPData constructor.
     * @param int $carrierId
     */
    public function __construct(int $carrierId)
    {
        $this->carrierId = $carrierId;
    }

    /**
     * @return int
     */
    public function getCarrierId(): int
    {
        return $this->carrierId;
    }


}