<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 10:17
 */

namespace SubscriptionBundle\BillingFramework\Process\Exception;


use Throwable;

abstract class AbstractProcessException extends \RuntimeException
{

    /**
     * @var string
     */
    private $billingMessage;

    public function __construct(string $message = "", int $code = null, string $billingMessage, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->billingMessage = $billingMessage;
    }

    /**
     * @return string
     */
    public function getBillingMessage(): string
    {
        return $this->billingMessage;
    }
}