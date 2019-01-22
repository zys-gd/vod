<?php

namespace SubscriptionBundle\Controller\Actions\Fake;


use IdentificationBundle\Identification\Exception\RedirectRequiredException;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Exception\SubscriptionException;
use SubscriptionBundle\Service\Action\Unsubscribe\UnsubscriptionEligibilityChecker;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Component\HttpFoundation\Request;

class UnsubscribeAction
{
    use ResponseTrait;

    /** @var UserExtractor */
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
        $this->userProvider = $UserProvider;
        $this->checker = $checker;
        $this->subscriptionProvider = $subscriptionProvider;
        $this->entitySaveHelper = $entitySaveHelper;
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
            $User = $this->userProvider->getUserFromRequest($request);
            $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($User);

            if (!is_null($subscription) && !$this->checker->isEligibleToUnsubscribe($subscription)) {
                throw new SubscriptionException('You have already been unsubscribed');
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
                    'subscription' => false,
                ],
                [
                    'type' => 'unsubscribe',
                ]
            );
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