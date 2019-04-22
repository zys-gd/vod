<?php


namespace SubscriptionBundle\Service\SubscriptionLimiter\DTO;


use IdentificationBundle\Entity\CarrierInterface;

class CarrierLimiterData
{
    /**
     * @var CarrierInterface
     */
    private $carrier;
    /**
     * @var int|null
     */
    private $processingSlots;
    /**
     * @var int|null
     */
    private $openSubscriptionSlots;

    /**
     * CarrierLimiterData constructor.
     *
     * @param CarrierInterface $carrier
     * @param int|null         $processingSlots
     * @param int|null         $openSubscriptionSlots
     */
    public function __construct(CarrierInterface $carrier,
        ?int $processingSlots = null,
        ?int $openSubscriptionSlots = null)
    {
        $this->carrier               = $carrier;
        $this->processingSlots       = $processingSlots;
        $this->openSubscriptionSlots = $openSubscriptionSlots;
    }

    /**
     * @return CarrierInterface
     */
    public function getCarrier(): CarrierInterface
    {
        return $this->carrier;
    }

    /**
     * @return int|null
     */
    public function getProcessingSlots(): ?int
    {
        return $this->processingSlots;
    }

    /**
     * @return int|null
     */
    public function getOpenSubscriptionSlots(): ?int
    {
        return $this->openSubscriptionSlots;
    }


}