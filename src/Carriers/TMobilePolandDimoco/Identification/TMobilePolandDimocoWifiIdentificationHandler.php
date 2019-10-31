<?php

namespace Carriers\TMobilePolandDimoco\Identification;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\DTO\PhoneValidationOptions;
use IdentificationBundle\WifiIdentification\Exception\WifiIdentConfirmException;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinRequestRules;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinVerifyRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TMobilePolandDimocoWifiIdentificationHandler
 */
class TMobilePolandDimocoWifiIdentificationHandler implements WifiIdentificationHandlerInterface, HasCustomPinVerifyRules, HasCustomPinRequestRules
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var WifiIdentificationDataStorage
     */
    private $storage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * TMobilePolandDimocoWifiIdentificationHandler constructor
     *
     * @param UserRepository                $repository
     * @param WifiIdentificationDataStorage $storage
     * @param RouterInterface               $router
     */
    public function __construct(UserRepository $repository, WifiIdentificationDataStorage $storage, RouterInterface $router)
    {
        $this->repository = $repository;
        $this->storage = $storage;
        $this->router = $router;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TMOBILE_POLAND_DIMOCO;
    }

    /**
     * @return PhoneValidationOptions
     */
    public function getPhoneValidationOptions(): PhoneValidationOptions
    {
        return new PhoneValidationOptions(
            '+48XXXXXXXXXX',
            '^\+48[0-9]{9}$',
            'XXXX',
            '^[0-9]{4}$'
        );
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function getExistingUser(string $msisdn): ?User
    {
        return $this->repository->findOneByMsisdn($msisdn);
    }

    /**
     * @return bool
     */
    public function areSMSSentByBilling(): bool
    {
        return false;
    }

    public function getRedirectUrl()
    {
    }

    /**
     * @param PinRequestResult $pinRequestResult
     * @param bool             $isZeroCreditSubAvailable
     *
     * @return array
     */
    public function getAdditionalPinVerifyParams(PinRequestResult $pinRequestResult, bool $isZeroCreditSubAvailable): array
    {
        $data = $pinRequestResult->getRawData();

        if (empty($data['transactionId'])) {
            throw new WifiIdentConfirmException("Can't process pin verification. Missing required parameters");
        }

        return ['transactionId' => $data['transactionId']];
    }

    /**
     * @param PinVerifyResult $pinVerifyResult
     * @param string          $phoneNumber
     *
     * @return string
     */
    public function getMsisdnFromResult(PinVerifyResult $pinVerifyResult, string $phoneNumber): string
    {
        return str_replace('+', '', $phoneNumber);
    }

    /**
     * @param PinVerifyResult $parameters
     */
    public function afterSuccessfulPinVerify(PinVerifyResult $parameters): void
    {
        $this->storage->setPinVerifyResult($parameters);
    }

    /**
     * @param \Exception $exception
     */
    public function afterFailedPinVerify(\Exception $exception): void
    {
        // TODO: Implement afterFailedPinVerify() method.
    }

    /**
     * @return array
     */
    public function getAdditionalPinRequestParams(): array
    {
        return [
            'redirect_url' => $this->router->generate('payment_confirmation', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ];
    }

    /**
     * @param PinRequestResult $result
     */
    public function afterSuccessfulPinRequest(PinRequestResult $result): void
    {
        // TODO: Implement afterSuccessfulPinRequest() method.
    }

    /**
     * @param PinRequestProcessException $exception
     *
     * @return string|null
     */
    public function getPinRequestErrorMessage(PinRequestProcessException $exception): ?string
    {
        return $exception->getMessage();
    }
}