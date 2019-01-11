<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 15:58
 */

namespace IdentificationBundle\Service\Action\WifiIdentification\Common;


use IdentificationBundle\BillingFramework\Process\PinRequestProcess;
use IdentificationBundle\Entity\PinCode;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Service\Action\Identification\Common\Exception\FailedIdentificationException;
use IdentificationBundle\Service\Action\WifiIdentification\Common\InternalSMS\PinCodeSaver;
use IdentificationBundle\Service\Action\WifiIdentification\Handler\HasInternalSMSHandling;
use IdentificationBundle\Service\Action\WifiIdentification\Handler\WifiIdentificationHandlerProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;

class WifiIdentSMSSender
{
    /**
     * @var WifiIdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var PinRequestProcess
     */
    private $pinRequestProcess;
    /**
     * @var MessageComposer
     */
    private $messageComposer;
    /**
     * @var MsisdnCleaner
     */
    private $cleaner;
    /**
     * @var PinCodeSaver
     */
    private $pinCodeSaver;


    /**
     * WifiIdentSMSSender constructor.
     * @param WifiIdentificationHandlerProvider $handlerProvider
     * @param CarrierRepositoryInterface        $carrierRepository
     * @param PinRequestProcess                 $pinRequestProcess
     * @param MessageComposer                   $messageComposer
     * @param MsisdnCleaner                     $cleaner
     * @param PinCodeSaver                      $pinCodeSaver
     */
    public function __construct(
        WifiIdentificationHandlerProvider $handlerProvider,
        CarrierRepositoryInterface $carrierRepository,
        PinRequestProcess $pinRequestProcess,
        MessageComposer $messageComposer,
        MsisdnCleaner $cleaner,
        PinCodeSaver $pinCodeSaver
    )
    {
        $this->handlerProvider   = $handlerProvider;
        $this->carrierRepository = $carrierRepository;
        $this->pinRequestProcess = $pinRequestProcess;
        $this->messageComposer   = $messageComposer;
        $this->cleaner           = $cleaner;
        $this->pinCodeSaver      = $pinCodeSaver;
    }

    public function sendSMS(int $carrierId, string $mobileNumber)
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);

        $handler = $this->handlerProvider->get($carrier);

        // Resubscribe
        if (!$handler->isPinSendAllowed($mobileNumber)) {
            throw new FailedIdentificationException('Pin Ident is not allowed');
        }

        if (!$handler->areSMSSentByBilling()) {
            $this->pinCodeSaver->savePinCode(mt_rand(0,99999));
        } else {
            $parameters                 = new ProcessRequestParameters();
            $parameters->additionalData = [
                'msisdn'  => $this->cleaner->clean($mobileNumber, $carrier),
                'carrier' => $carrier->getBillingCarrierId(),
                'op_id'   => $carrier->getOperatorId(),

            ];

            $result = $this->pinRequestProcess->doPinRequest($parameters);
        }


    }

}