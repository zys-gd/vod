<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 14:01
 */

namespace IdentificationBundle\Identification\Common\Pixel;


use IdentificationBundle\BillingFramework\Process\AfterPixelProcess;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;

class PixelIdentVerifier
{
    /**
     * @var AfterPixelProcess
     */
    private $afterPixelProcess;


    /**
     * PixelIdentVerifier constructor.
     */
    public function __construct(AfterPixelProcess $afterPixelProcess)
    {
        $this->afterPixelProcess = $afterPixelProcess;
    }

    public function isIdentSuccess(int $carrierId, string $processId): bool
    {
        try {
            $parameters = $this->prepareParams($processId, $carrierId);
            $this->afterPixelProcess->doAfterIdent($parameters);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string $processId
     * @param        $carrier
     * @return ProcessRequestParameters
     */
    private function prepareParams(string $processId, $carrier): ProcessRequestParameters
    {
        $parameters                 = new ProcessRequestParameters();
        $parameters->client         = 'store';
        $parameters->carrier        = $carrier;
        $parameters->additionalData = ['process' => $processId];

        return $parameters;
    }
}