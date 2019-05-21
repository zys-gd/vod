<?php

namespace IdentificationBundle\Carriers\OrangeEGTpay;

use App\Domain\Constants\ConstBillingCarrierId;
use Doctrine\ORM\EntityManagerInterface;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\BillingFramework\Process\Exception\PinRequestProcessException;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Exception\WifiIdentConfirmException;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinRequestRules;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinResendRules;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinVerifyRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OrangeEGWifiIdentificationHandler
 */
class OrangeEGWifiIdentificationHandler implements
    WifiIdentificationHandlerInterface,
    HasCustomPinVerifyRules,
    HasCustomPinResendRules,
    HasCustomPinRequestRules
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
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * OrangeEGWifiIdentificationHandler constructor
     *
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param LocalExtractor $localExtractor
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        LocalExtractor $localExtractor
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->localExtractor = $localExtractor;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::ORANGE_EGYPT_TPAY;
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
     * @param PinRequestResult $pinRequestResult
     *
     * @return array
     */
    public function getAdditionalPinResendParameters(PinRequestResult $pinRequestResult): array
    {
        $pinRequestResultData = $pinRequestResult->getRawData();
        $clientUser = empty($pinRequestResultData['subscription_contract_id'])
            ? null
            : $pinRequestResultData['subscription_contract_id'];

        return ['client_user' => $clientUser];
    }

    /**
     * @return array
     */
    public function getAdditionalPinRequestParams(): array
    {
        return ['lang' => $this->localExtractor->getLocal()];
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

    /**
     * @param \Exception $exception
     */
    public function afterFailedPinVerify(\Exception $exception): void
    {

    }

    public function afterSuccessfulPinRequest(PinRequestResult $result): void
    {
        // TODO: Implement afterSuccessfulPinRequest() method.
    }
}