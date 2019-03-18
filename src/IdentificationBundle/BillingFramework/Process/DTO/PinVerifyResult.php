<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.03.19
 * Time: 16:02
 */

namespace IdentificationBundle\BillingFramework\Process\DTO;


class PinVerifyResult
{
    /**
     * @var array
     */
    private $rawData;

    /**
     * PinVerifyResult constructor.
     * @param array $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }


    /**
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
}