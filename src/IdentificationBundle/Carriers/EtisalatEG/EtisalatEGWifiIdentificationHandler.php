<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 17:17
 */

namespace IdentificationBundle\Carriers\EtisalatEG;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinRequestRules;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinVerifyRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;

class EtisalatEGWifiIdentificationHandler implements
    WifiIdentificationHandlerInterface,
    HasCustomPinVerifyRules

{
    /**
     * @var \IdentificationBundle\Identification\Service\IdentificationDataStorage
     */
    private $dataStorage;


    /**
     * EtisalatEGWifiIdentificationHandler constructor.
     */
    public function __construct(IdentificationDataStorage $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return ConstBillingCarrierId::ETISALAT_EGYPT === $carrier->getBillingCarrierId();
    }


    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }

    public function isPinSendAllowed($mobileNumber): bool
    {
        return true;
    }

    public function areSMSSentByBilling(): bool
    {
        return true;
    }

    public function getAdditionalPinVerifyParams(PinRequestResult $pinRequestResult): array
    {
        $contractId = $pinRequestResult->getRawData()['subscription_contract_id'];

        return [
            'client_user' => $contractId
        ];
    }

    public function afterSuccessfulPinVerify(ProcessResult $parameters): void
    {
    }

    public function afterFailedPinVerify(\Exception $exception): void
    {
    }
}