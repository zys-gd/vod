<?php


namespace SubscriptionBundle\Service\CAPTool;

use App\Domain\Entity\Affiliate;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\CAPTool\Exception\SubscriptionCapReachedOnAffiliate;
use SubscriptionBundle\Service\CAPTool\Exception\SubscriptionCapReachedOnCarrier;
use SubscriptionBundle\Service\CAPTool\Limiter\LimiterDataMapper;
use SubscriptionBundle\Service\CAPTool\Limiter\LimiterStorage;
use SubscriptionBundle\Service\CAPTool\Limiter\StorageKeyGenerator;
use SubscriptionBundle\Service\CAPTool\Limiter\SubscriptionCapChecker;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionLimiter
{

    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var LimiterDataMapper
     */
    private $limiterDataMapper;
    /**
     * @var LimiterStorage
     */
    private $limiterDataStorage;
    /**
     * @var SubscriptionCapChecker
     */
    private $carrierCapChecker;
    /**
     * @var StorageKeyGenerator
     */
    private $storageKeyGenerator;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubscriptionLimitNotifier
     */
    private $notifier;

    /**
     * SubscriptionLimiter constructor.
     *
     * @param LimiterStorage         $limiterDataStorage
     * @param SubscriptionExtractor  $subscriptionExtractor
     * @param LimiterDataMapper      $limiterDataMapper
     * @param SubscriptionCapChecker $carrierCapChecker
     * @param StorageKeyGenerator    $storageKeyGenerator
     * @param CampaignExtractor      $campaignExtractor
     */
    public function __construct(
        LimiterStorage $limiterDataStorage,
        SubscriptionExtractor $subscriptionExtractor,
        LimiterDataMapper $limiterDataMapper,
        SubscriptionCapChecker $carrierCapChecker,
        StorageKeyGenerator $storageKeyGenerator,
        CampaignExtractor $campaignExtractor,
        LoggerInterface $logger,
        SubscriptionLimitNotifier $notifier
    )
    {
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->limiterDataMapper     = $limiterDataMapper;
        $this->limiterDataStorage    = $limiterDataStorage;
        $this->carrierCapChecker     = $carrierCapChecker;
        $this->storageKeyGenerator   = $storageKeyGenerator;
        $this->campaignExtractor     = $campaignExtractor;
        $this->logger                = $logger;
        $this->notifier              = $notifier;
    }

    /**
     * @param SessionInterface $session
     *
     * @return void
     * @throws \Twig_Error_Loader
     * @throws SubscriptionCapReachedOnCarrier
     * @throws SubscriptionCapReachedOnAffiliate
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     */
    public function ensureCapIsNotReached(SessionInterface $session): void
    {
        if ($this->limiterDataStorage->isSubscriptionAlreadyPending($session->getId())) {
            $this->logger->debug('Already pending subscription', ['id' => $session->getId()]);
            return;
        }

        $data = $this->limiterDataMapper->mapFromSession($session);

        if ($this->carrierCapChecker->isCapReachedForCarrier($data->getCarrier())) {
            $this->notifier->notifyLimitReachedForCarrier($data->getCarrier());
            throw new SubscriptionCapReachedOnCarrier($data->getCarrier());
        }

        $constraint = $data->getConstraintByAffiliate();
        if ($constraint && $this->carrierCapChecker->isCapReachedForAffiliate($constraint)) {
            $this->notifier->notifyLimitReachedByAffiliate($constraint, $constraint->getCarrier());
            throw new SubscriptionCapReachedOnAffiliate($constraint, $constraint->getCarrier());
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function reserveSlotForSubscription(SessionInterface $session): void
    {
        $limiterData = $this->limiterDataMapper->mapFromSession($session);
        $key         = $this->storageKeyGenerator->generateKey($limiterData->getCarrier());

        $this->limiterDataStorage->storePendingSubscription($key, $session->getId());

        if ($affConstraint = $limiterData->getConstraintByAffiliate()) {
            $affKey = $this->storageKeyGenerator->generateAffiliateConstraintKey($affConstraint);
            $this->limiterDataStorage->storePendingSubscription($affKey, $session->getId());
        }
    }

    /**
     * @param CarrierInterface $carrier
     * @param Subscription     $subscription
     */
    public function finishSubscription(CarrierInterface $carrier, Subscription $subscription): void
    {
        $key = $this->storageKeyGenerator->generateKey($carrier);

        $this->limiterDataStorage->storeFinishedSubscription($key, $subscription->getUuid());

        if ($campaign = $this->campaignExtractor->getCampaignForSubscription($subscription)) {
            /** @var Affiliate $affiliate */
            $affiliate  = $campaign->getAffiliate();
            $constraint = $affiliate->getConstraint(
                ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE,
                $carrier->getBillingCarrierId()
            );
            if ($constraint) {
                /** @var ConstraintByAffiliate $subscriptionConstraint */
                $affKey = $this->storageKeyGenerator->generateAffiliateConstraintKey($constraint);
                $this->limiterDataStorage->storeFinishedSubscription($affKey, $subscription->getUuid());
            }
        }

        $this->releasePendingSlot($carrier);
    }

    /**
     * @param CarrierInterface $carrier
     */
    public function releasePendingSlot(CarrierInterface $carrier): void
    {
        $this->limiterDataStorage->removePendingSubscription();
    }

    /**
     * @param User $user
     *
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function need2BeLimited(User $user): bool
    {
        return !(bool)$this->subscriptionExtractor->getExistingSubscriptionForUser($user);
    }

    /**
     * @param SessionInterface $session
     *
     * @return bool
     */
    public function canStartProcess(SessionInterface $session): bool
    {
        // TODO: Implement canStartProcess() method.
    }
}