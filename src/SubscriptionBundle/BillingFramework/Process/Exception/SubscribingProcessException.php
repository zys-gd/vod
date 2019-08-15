<?php

namespace SubscriptionBundle\BillingFramework\Process\Exception;

use Throwable;

/**
 * Class SubscribingProcessException
 */
class SubscribingProcessException extends AbstractProcessException
{
    const FAIL_REASON_NOT_ENOUGH_CREDIT = 'not_enough_credit';
    const FAIL_REASON_BLACKLISTED = 'blacklisted_number';

    /**
     * @var \stdClass
     */
    private $rawResponse;
    /**
     * @var string
     */
    private $operationPrefix;

    /**
     * SubscribingProcessException constructor
     *
     * @param string         $message
     * @param int|null       $code
     * @param string         $billingMessage
     * @param Throwable|null $previous
     * @param \stdClass      $rawResponse
     */
    public function __construct(string $message = "", int $code = null, string $billingMessage = "", Throwable $previous = null, \stdClass $rawResponse = null, string $operationPrefix = '')
    {

        parent::__construct($message, $code, $billingMessage, $previous);

        $this->rawResponse     = $rawResponse;
        $this->operationPrefix = $operationPrefix;
    }

    /**
     * @return \stdClass|null
     */
    public function getBillingData(): ?\stdClass
    {
        $data = null;

        if (!empty($this->rawResponse->data)) {
            $data = $this->rawResponse->data;
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getOperationPrefix(): string
    {
        return $this->operationPrefix;
    }


}