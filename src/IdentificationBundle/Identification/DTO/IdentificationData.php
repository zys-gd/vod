<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 17:48
 */

namespace IdentificationBundle\Identification\DTO;


class IdentificationData
{

    private $identificationToken;

    /**
     * IdentificationData constructor.
     * @param $identificationToken
     */
    public function __construct(string $identificationToken)
    {
        $this->identificationToken = $identificationToken;
    }

    /**
     * @return string
     */
    public function getIdentificationToken(): string
    {
        return $this->identificationToken;
    }


}