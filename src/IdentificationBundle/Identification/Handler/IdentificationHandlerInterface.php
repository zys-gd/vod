<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:39
 */

namespace IdentificationBundle\Identification\Handler;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Symfony\Component\HttpFoundation\Request;

interface IdentificationHandlerInterface
{
    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool;



}