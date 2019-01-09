<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 19/09/17
 * Time: 1:56 PM
 */

namespace SubscriptionBundle\BillingFramework\Notification\API\DTO;


class NotificationMessage
{
    /** @var  string */
    private $type;

    /** @var  string */
    private $body;

    /** @var  string */
    private $phone;

    /** @var  integer */
    private $operatorId;

    /** @var integer */
    private $billingProccess;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return int
     */
    public function getOperatorId(): int
    {
        return $this->operatorId;
    }

    /**
     * @param int $operatorId
     */
    public function setOperatorId(int $operatorId)
    {
        $this->operatorId = $operatorId;
    }

    /**
     * @param int $billingProccess
     */
    public function setBillingProccess($billingProccess = null)
    {
        $this->billingProccess = $billingProccess;
    }

    /**
     * @return int|null
     */
    public function getBillingProccess()
    {
        return $this->billingProccess;
    }
}