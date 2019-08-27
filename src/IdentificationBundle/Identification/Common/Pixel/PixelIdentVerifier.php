<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 14:01
 */

namespace IdentificationBundle\Identification\Common\Pixel;


use IdentificationBundle\BillingFramework\Process\AfterPixelProcess;
use SubscriptionBundle\BillingFramework\BillingOptionsProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;

class PixelIdentVerifier
{
    /**
     * @var AfterPixelProcess
     */
    private $afterPixelProcess;
    /**
     * @var BillingOptionsProvider
     */
    private $billingOptionsProvider;


    /**
     * PixelIdentVerifier constructor.
     * @param AfterPixelProcess      $afterPixelProcess
     * @param BillingOptionsProvider $billingOptionsProvider
     */
    public function __construct(AfterPixelProcess $afterPixelProcess, BillingOptionsProvider $billingOptionsProvider)
    {
        $this->afterPixelProcess      = $afterPixelProcess;
        $this->billingOptionsProvider = $billingOptionsProvider;
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
        $parameters->client         = $this->billingOptionsProvider->getClientId();
        $parameters->carrier        = $carrier;
        $parameters->additionalData = ['process' => $processId];

        return $parameters;
    }
}