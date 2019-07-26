<?php

namespace SubscriptionBundle\Controller\Actions\Fake;


use Doctrine\ORM\EntityManager;
use ExtrasBundle\Controller\Traits\ResponseTrait;
use ExtrasBundle\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\Service\RouteProvider;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;
use IdentificationBundle\Identification\Service\UserExtractor;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class SubscribeAction
{
    use ResponseTrait;

    /**
     * @var \IdentificationBundle\Identification\Service\UserExtractor
     */
    private $userExtractor;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionProvider;
    /**
     * @var \SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var EntityManager
     */
    private $entitySaveHelper;
    /**
     * @var RouteProvider
     */
    private $routeProvider;

    /**
     * SubscribeAction constructor.
     *
     * @param \IdentificationBundle\Identification\Service\UserExtractor $userExtractor
     * @param Router                                                     $router
     * @param LoggerInterface                                            $logger
     * @param SubscriptionExtractor                                      $subscriptionProvider
     * @param SubscriptionPackProvider                                   $subscriptionPackProvider
     * @param EntitySaveHelper                                           $entitySaveHelper
     * @param RouteProvider                                              $routeProvider
     */
    public function __construct(
        UserExtractor $userExtractor,
        Router $router,
        LoggerInterface $logger,
        SubscriptionExtractor $subscriptionProvider,
        SubscriptionPackProvider $subscriptionPackProvider,
        EntitySaveHelper $entitySaveHelper,
        RouteProvider $routeProvider
    )
    {
        $this->userExtractor            = $userExtractor;
        $this->router                   = $router;
        $this->logger                   = $logger;
        $this->subscriptionProvider     = $subscriptionProvider;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->entitySaveHelper         = $entitySaveHelper;
        $this->routeProvider            = $routeProvider;
    }


    public function __invoke(Request $request, IdentificationData $identificationData)
    {
        $response = null;

        /** @var  User $user */
        $user = $this->userExtractor->getUserByIdentificationData($identificationData);

        $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($user);
        if (!$subscription instanceof Subscription) {
            $subscription = new Subscription(UuidGenerator::generate());
        }
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setUser($user);
        $subscription->setStatus(Subscription::IS_PENDING);
        $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
        $subscription->setCredits($subscriptionPack->isUnlimited() ? 1000 : $subscriptionPack->getCredits());
        $subscription->setRenewDate(new \DateTime('now + 1 day'));
        $subscription->setStatus(Subscription::IS_ACTIVE);

        $this->entitySaveHelper->persistAndSave($subscription);

        return new RedirectResponse($this->routeProvider->getLinkToHomepage());
    }
}