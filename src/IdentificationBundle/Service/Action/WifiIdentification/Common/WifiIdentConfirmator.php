<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:58
 */

namespace IdentificationBundle\Service\Action\WifiIdentification\Common;


use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Service\Action\WifiIdentification\Common\InternalSMS\PinCodeVerifier;
use IdentificationBundle\Service\Action\WifiIdentification\Handler\HasInternalSMSHandling;
use IdentificationBundle\Service\Action\WifiIdentification\Handler\WifiIdentificationHandlerProvider;

class WifiIdentConfirmator
{
    /**
     * @var WifiIdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var PinCodeVerifier
     */
    private $codeVerifier;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;


    /**
     * WifiIdentConfirmator constructor.
     */
    public function __construct(WifiIdentificationHandlerProvider $handlerProvider, PinCodeVerifier $codeVerifier, CarrierRepositoryInterface $carrierRepository)
    {
        $this->handlerProvider   = $handlerProvider;
        $this->codeVerifier      = $codeVerifier;
        $this->carrierRepository = $carrierRepository;
    }

    public function confirm(int $carrierId, string $pinCode)
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);

        $handler = $this->handlerProvider->get($carrier);

        if (!$handler->areSMSSentByBilling()) {
            $result = $this->codeVerifier->verifyPinCode($pinCode);
        }
    }

}