<?php

namespace SubscriptionBundle\Subscription\Unsubscribe\Controller;


use ExtrasBundle\Controller\Traits\ResponseTrait;
use IdentificationBundle\User\Service\UserExtractor;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\Subscription\Unsubscribe\Exception\AlreadyUnsubscribedException;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscriptionEligibilityChecker;
use Symfony\Component\HttpFoundation\Request;

class FakeUnsubscribeAction
{
    use ResponseTrait;

    /** @var \IdentificationBundle\User\Service\UserExtractor */
    private $userProvider;
    /** @var UnsubscriptionEligibilityChecker */
    private $checker;
    /** @var SubscriptionExtractor */
    private $subscriptionProvider;
    /** @var EntitySaveHelper */
    private $entitySaveHelper;

    public function __construct(
        UserExtractor $UserProvider,
        SubscriptionExtractor $subscriptionProvider,
        UnsubscriptionEligibilityChecker $checker,
        EntitySaveHelper $entitySaveHelper
    )
    {
        $this->userProvider         = $UserProvider;
        $this->checker              = $checker;
        $this->subscriptionProvider = $subscriptionProvider;
        $this->entitySaveHelper     = $entitySaveHelper;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|null
     */
    public function __invoke(Request $request)
    {
        $response = null;
        try {
            $User         = $this->userProvider->getUserFromRequest($request);
            $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($User);

            if (!is_null($subscription) && !$this->checker->isEligibleToUnsubscribe($subscription)) {
                throw new AlreadyUnsubscribedException('You have already been unsubscribed');
            }

            $subscription->setStatus(Subscription::IS_INACTIVE);
            $subscription->setCurrentStage(Subscription::ACTION_UNSUBSCRIBE);
            $subscription->setCredits(0);
            $this->entitySaveHelper->persistAndSave($subscription);

            return $this->getSimpleJsonResponse(
                'You are unsubscribed',
                200,
                [
                    'identification' => true,
                    'subscription'   => false,
                ],
                [
                    'type' => 'unsubscribe',
                ]
            );
        } catch (\Exception $ex) {
            $response = $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => true,
                'subscription'   => false,
            ]);
        }
        return $response;
    }
}