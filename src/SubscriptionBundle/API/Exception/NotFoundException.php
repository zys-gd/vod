<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.09.19
 * Time: 15:12
 */

namespace SubscriptionBundle\API\Exception;


class NotFoundException extends \RuntimeException
{
    /**
     * @var string
     */
    private $msisdn;

    /**
     * NotFoundException constructor.
     */
    public function __construct(string $message, string $msisdn)
    {
        $this->msisdn = $msisdn;

        parent::__construct($message);
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMsisdn(): string
    {
        return $this->msisdn;
    }


}