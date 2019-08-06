<?php


namespace PiwikBundle\Service\DTO;


class OrderInformation
{
    /**
     * @var string
     */
    private $orderId;
    /**
     * @var string
     */
    private $orderValue;
    /**
     * @var string
     */
    private $prodSku;
    /**
     * @var string
     */
    private $prodCat;

    public function __construct(string $orderId, string $orderValue, string $prodSku, string $prodCat)
    {
        $this->orderId    = $orderId;
        $this->orderValue = $orderValue;
        $this->prodSku    = $prodSku;
        $this->prodCat    = $prodCat;
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
    public function getOrderValue(): string
    {
        return $this->orderValue;
    }

    /**
     * @return string
     */
    public function getProdSku(): string
    {
        return $this->prodSku;
    }

    /**
     * @return string
     */
    public function getProdCat(): string
    {
        return $this->prodCat;
    }

}