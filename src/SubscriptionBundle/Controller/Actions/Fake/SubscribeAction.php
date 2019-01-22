<?php

namespace SubscriptionBundle\Controller\Actions\Fake;


use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManager;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\Exception\RedirectRequiredException;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Exception\SubscriptionException;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class SubscribeAction
{
    use ResponseTrait;

    /**
     * @var UserExtractor
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
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var EntityManager
     */
    private $entitySaveHelper;

    /**
     * SubscribeAction constructor.
     *
     * @param UserExtractor            $userExtractor
     * @param Router                   $router
     * @param LoggerInterface          $logger
     * @param SubscriptionExtractor    $subscriptionProvider
     * @param SubscriptionPackProvider $subscriptionPackProvider
     * @param EntitySaveHelper         $entitySaveHelper
     */
    public function __construct(
        UserExtractor $userExtractor,
        Router $router,
        LoggerInterface $logger,
        SubscriptionExtractor $subscriptionProvider,
        SubscriptionPackProvider $subscriptionPackProvider,
        EntitySaveHelper $entitySaveHelper
    )
    {
        $this->userExtractor = $userExtractor;
        $this->router = $router;
        $this->logger = $logger;
        $this->subscriptionProvider = $subscriptionProvider;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->entitySaveHelper = $entitySaveHelper;
    }


    public function __invoke(Request $request, IdentificationData $identificationData)
    {
        $response = null;
        try {
            /** @var  User $user */
            $user = $this->userExtractor->getUserByIdentificationData($identificationData);

            $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($user);
            if (!$subscription instanceof Subscription) $subscription = new Subscription(UuidGenerator::generate());
            $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

            $subscription->setSubscriptionPack($subscriptionPack);
            $subscription->setUser($user);
            $subscription->setStatus(Subscription::IS_PENDING);
            $subscription->setCurrentStage(Subscription::ACTION_SUBSCRIBE);
            $subscription->setCredits($subscriptionPack->isUnlimited() ? 1000 : $subscriptionPack->getCredits());
            $subscription->setRenewDate(new \DateTime('now + 1 day'));
            $subscription->setStatus(Subscription::IS_ACTIVE);

            $this->entitySaveHelper->persistAndSave($subscription);

            $response = $this->getSimpleJsonResponse('', 200, ['message' => 'You created a new subscription']);


        } catch (RedirectRequiredException $ex) {
            $response = $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => false,
                'subscription' => false,
                'redirectUrl' => $ex->getRedirectUrl(),
            ]);

        } catch (SubscriptionException $ex) {
            $response = $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => true,
                'subscription' => false,
            ]);
        } catch (\Exception $ex) {
            $response = $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => true,
                'subscription' => false,
            ]);
        }
        return $response;
    }
}