<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 16:57
 */

namespace IdentificationBundle\BillingFramework\Process\Exception;


use Throwable;

class PinRequestProcessException extends \RuntimeException
{

    /**
     * @var string
     */
    private $billingMessage;

    public function __construct(string $message = "", int $code = 0, string $billingMessage)
    {
        parent::__construct($message, $code, null);
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