<?php

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Service\UserExtractor;
use SubscriptionBundle\Service\SubscriptionExtractor;

class GetSubscriptionAction
{

    use ResponseTrait;
    /**
     * @var UserExtractor
     */
    private $userExtractor;


    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionProvider;


    /**
     * GetSubscriptionAction constructor.
     *
     * @param UserExtractor         $userExtractor
     * @param SubscriptionExtractor $provider
     */
    public function __construct(UserExtractor $userExtractor, SubscriptionExtractor $provider)
    {
        $this->userExtractor = $userExtractor;
        $this->subscriptionProvider = $provider;
    }

    public function __invoke(Request $request)
    {

        try {
            /** @var User $user */
            $user = $this->userExtractor->getUserFromRequest($request);
        } catch (\Exception $ex) {
            return $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => false,
                'subscription' => false,
            ]);
        }

        try {
            $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($user);
            if (!$subscription) {
                return $this->getSimpleJsonResponse(
                    sprintf('Can not find subscription for msisdn %s', $user->getIdentifier()),
                    400,
                    [
                        'user' => true,
                        'subscription' => false,
                    ]
                );
            }
        } catch (\Exception $ex) {
            return $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'user' => true,
                'subscription' => false,
            ]);
        }

        return new JsonResponse(['id' => $subscription->getUuid()]);
    }

}