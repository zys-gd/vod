<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 21/08/17
 * Time: 3:07 PM
 */

namespace SubscriptionBundle\BillingFramework\Process\Exception;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class BillingFrameworkProcessException extends BillingFrameworkException
{

    /** @var  ProcessResult */
    protected $response;
    protected $rawResponse;
    private $billingCode;

    /**
     * @return ProcessResult
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ProcessResult $response
     */
    public function setResponse(ProcessResult $response)
    {
        $this->response = $response;
    }

    public function setRawResponse(\stdClass $rawResponse)
    {
        $this->rawResponse = $rawResponse;

    }

    /**
     * @return mixed
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    public function setBillingCode(int $code)
    {
        $this->billingCode = $code;
    }

    /**
     * @return mixed
     */
    public function getBillingCode(): ?int
    {
        return $this->billingCode;
    }


}