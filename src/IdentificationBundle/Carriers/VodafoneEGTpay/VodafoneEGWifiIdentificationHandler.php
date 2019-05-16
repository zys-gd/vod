<?php

namespace IdentificationBundle\Carriers\VodafoneEGTpay;

use App\Domain\Constants\ConstBillingCarrierId;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Exception\WifiIdentConfirmException;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinVerifyRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class VodafonePKWifiIdentificationHandler
 */
class VodafoneEGWifiIdentificationHandler implements WifiIdentificationHandlerInterface, HasCustomPinVerifyRules
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * VodafonePKWifiIdentificationHandler constructor
     *
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::VODAFONE_EGYPT_TPAY;
    }

    /**
     * @return bool
     */
    public function areSMSSentByBilling(): bool
    {
        return true;
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function getExistingUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }

    /**
     * @param PinRequestResult $pinRequestResult
     *
     * @return array
     */
    public function getAdditionalPinVerifyParams(PinRequestResult $pinRequestResult): array
    {
        $data = $pinRequestResult->getRawData();

        if (empty($data['subscription_contract_id']) || empty($data['transactionId'])) {
            throw new WifiIdentConfirmException("Can't process pin verification. Missing required parameters");
        }

        return ['client_user' => $data['subscription_contract_id'], 'transactionId' => $data['transactionId']];
    }

    /**
     * @param PinVerifyResult $pinVerifyResult
     * @param string $phoneNumber
     *
     * @return string
     */
    public function getMsisdnFromResult(PinVerifyResult $pinVerifyResult, string $phoneNumber): string
    {
        return $phoneNumber;
    }

    /**
     * @param PinVerifyResult $parameters
     * @param User $user
     */
    public function afterSuccessfulPinVerify(PinVerifyResult $parameters, User $user): void
    {
        $data = $parameters->getRawData();

        if (!empty($data['subscription_contract_id'])) {
            $user->setProviderId($data['subscription_contract_id']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->router->generate('subscription.consent_page_subscribe');
    }

    /**
     * @param \Exception $exception
     */
    public function afterFailedPinVerify(\Exception $exception): void
    {

    }
}