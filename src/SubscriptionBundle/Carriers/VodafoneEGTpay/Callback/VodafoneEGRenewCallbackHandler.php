<?php

namespace SubscriptionBundle\Carriers\VodafoneEGTpay\Callback;

use App\Domain\Constants\ConstBillingCarrierId;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\Affiliate\Service\UserInfoMapper;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Service\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Service\ZeroCreditSubscriptionChecking;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VodafoneEGRenewCallbackHandler
 */
class VodafoneEGRenewCallbackHandler implements HasCommonFlow, CarrierCallbackHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserInfoMapper
     */
    private $userInfoMapper;

    /**
     * @var AffiliateSender
     */
    private $affiliateSender;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;

    /**
     * OrangeEGCallbackHandler constructor
     *
     * @param UserRepository $userRepository
     * @param UserInfoMapper $userInfoMapper
     * @param AffiliateSender $affiliateSender
     * @param EntityManagerInterface $entityManager
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     */
    public function __construct(
        UserRepository $userRepository,
        UserInfoMapper $userInfoMapper,
        AffiliateSender $affiliateSender,
        EntityManagerInterface $entityManager,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
    ) {
        $this->userRepository = $userRepository;
        $this->userInfoMapper = $userInfoMapper;
        $this->affiliateSender = $affiliateSender;
        $this->entityManager = $entityManager;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
    }

    /**
     * @param Request $request
     * @param int $carrierId
     *
     * @return bool
     */
    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ConstBillingCarrierId::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param Subscription $subscription
     * @param User $user
     * @param ProcessResult $processResponse
     */
    public function afterProcess(Subscription $subscription, User $user, ProcessResult $processResponse)
    {
        $affiliateToken = $subscription->getAffiliateToken();
        $carrier = $user->getCarrier();

        if ($processResponse->isSuccessful()
            && $this->zeroCreditSubscriptionChecking->isAvailable($carrier)
            && !$carrier->getTrackAffiliateOnZeroCreditSub()
            && !empty($affiliateToken['cid'])
            && empty($affiliateToken['isTracked'])
        ) {
            $userInfo = $this->userInfoMapper->mapFromUser($user);
            $this->affiliateSender->checkAffiliateEligibilityAndSendEvent($subscription, $userInfo);

            $affiliateToken['isTracked'] = 1;

            $subscription->setAffiliateToken(json_encode($affiliateToken));

            $this->entityManager->flush();
        }
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function getUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }
}