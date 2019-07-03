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
     * SubscribingProcessException constructor
     *
     * @param string $message
     * @param int|null $code
     * @param string $billingMessage
     * @param Throwable|null $previous
     * @param \stdClass $rawResponse
     */
    public function __construct(string $message = "", int $code = null, string $billingMessage = "", Throwable $previous = null, \stdClass $rawResponse = null)
    {
        $this->rawResponse = $rawResponse;

        parent::__construct($message, $code, $billingMessage, $previous);
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
}