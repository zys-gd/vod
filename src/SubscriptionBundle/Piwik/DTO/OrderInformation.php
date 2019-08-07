<?php


namespace SubscriptionBundle\Piwik\DTO;


class OrderInformation
{


    /**
     * @var string
     */
    private $orderId;
    /**
     * @var string
     */
    private $price;
    /**
     * @var string
     */
    private $alias;
    /**
     * @var string
     */
    private $action;
    /**
     * @var string
     */
    private $currency;

    public function __construct(string $orderId, string $price, string $alias, string $action, string $currency)
    {

        $this->orderId  = $orderId;
        $this->price    = $price;
        $this->alias    = $alias;
        $this->action   = $action;
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }


}