<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 12:22
 */

namespace SubscriptionBundle\Subscription\Unsubscribe\Controller;


use ExtrasBundle\Controller\Traits\ResponseTrait;
use IdentificationBundle\User\Service\UserExtractor;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\Subscription\Subscribe\Common\SubscriptionEligibilityChecker;
use SubscriptionBundle\Subscription\Unsubscribe\Exception\AlreadyUnsubscribedException;
use SubscriptionBundle\Subscription\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Subscription\Unsubscribe\Unsubscriber;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscriptionEligibilityChecker;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//TODO:REFACTOR UserProvider
class UnsubscribeAction extends Controller
{
    use ResponseTrait;

    /**
     * @var \IdentificationBundle\User\Service\UserExtractor
     */
    private $UserProvider;

    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var Unsubscriber
     */
    private $unsubscriber;
    /**
     * @var SubscriptionEligibilityChecker
     */
    private $checker;
    /**
     * @var \SubscriptionBundle\Subscription\Common\SubscriptionExtractor
     */
    private $subscriptionProvider;
    /**
     * @var UnsubscriptionHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionAPI;


    /**
     * UnsubscribeAction constructor.
     *
     * @param \IdentificationBundle\User\Service\UserExtractor              $UserProvider
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionExtractor $subscriptionProvider
     * @param SubscriptionPackProvider                                      $subscriptionPackProvider
     * @param Unsubscriber                                                  $unsubscriber
     * @param UnsubscriptionEligibilityChecker                              $checker
     * @param UnsubscriptionHandlerProvider                                 $handlerProvider
     * @param ApiConnector                                                  $crossSubscriptionAPI
     */
    public function __construct(
        UserExtractor $UserProvider,
        SubscriptionExtractor $subscriptionProvider,
        SubscriptionPackProvider $subscriptionPackProvider,
        Unsubscriber $unsubscriber,
        UnsubscriptionEligibilityChecker $checker,
        UnsubscriptionHandlerProvider $handlerProvider,
        ApiConnector $crossSubscriptionAPI

    )
    {
        $this->UserProvider             = $UserProvider;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->unsubscriber             = $unsubscriber;
        $this->checker                  = $checker;
        $this->subscriptionProvider     = $subscriptionProvider;
        $this->handlerProvider          = $handlerProvider;
        $this->crossSubscriptionAPI     = $crossSubscriptionAPI;
    }

    public function __invoke(Request $request)
    {
        $response = null;
        try {
            $user             = $this->UserProvider->getUserFromRequest($request);
            $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);
            $subscription     = $this->subscriptionProvider->getExistingSubscriptionForUser($user);

            if (!is_null($subscription) && !$this->checker->isEligibleToUnsubscribe($subscription)) {
                throw new AlreadyUnsubscribedException('You have already been unsubscribed');
            }

            $handler              = $this->handlerProvider->getUnsubscriptionHandler($user->getCarrier());
            $additionalParameters = $handler->getAdditionalUnsubscribeParameters();

            $response = $this->unsubscriber->unsubscribe($subscription, $subscriptionPack, $additionalParameters);
            $handler->applyPostUnsubscribeChanges($subscription);

            if ($handler->isPiwikNeedToBeTracked($response)) {
                $this->unsubscriber->trackEventsForUnsubscribe($subscription, $response);
            }

            $this->crossSubscriptionAPI->deregisterSubscription($user->getIdentifier(), $user->getBillingCarrierId());


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