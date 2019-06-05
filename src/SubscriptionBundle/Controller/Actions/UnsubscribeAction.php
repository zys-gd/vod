<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 12:22
 */

namespace SubscriptionBundle\Controller\Actions;


use App\Domain\Service\CrossSubscriptionAPI\ApiConnector;
use IdentificationBundle\Exception\RedirectRequiredException;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Exception\SubscriptionException;
use SubscriptionBundle\Service\Action\Subscribe\Common\SubscriptionEligibilityChecker;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Service\Action\Unsubscribe\Unsubscriber;
use SubscriptionBundle\Service\Action\Unsubscribe\UnsubscriptionEligibilityChecker;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//TODO:REFACTOR UserProvider
class UnsubscribeAction extends Controller
{
    use ResponseTrait;

    /**
     * @var UserExtractor
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
     * @var SubscriptionExtractor
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
     * @param UserExtractor                    $UserProvider
     * @param SubscriptionExtractor            $subscriptionProvider
     * @param SubscriptionPackProvider         $subscriptionPackProvider
     * @param Unsubscriber                     $unsubscriber
     * @param UnsubscriptionEligibilityChecker $checker
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
                throw new SubscriptionException('You have already been unsubscribed');
            }

            $handler  = $this->handlerProvider->getUnsubscriptionHandler($user->getCarrier());
            $response = $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);

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


        } catch (RedirectRequiredException $ex) {
            $response = $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => false,
                'subscription'   => false,
                'redirectUrl'    => $ex->getRedirectUrl(),
            ]);
        } catch (SubscriptionException $ex) {
            $response = $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => true,
                'subscription'   => false,
            ]);
        } catch (\Exception $ex) {
            $response = $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => true,
                'subscription'   => false,
            ]);
        }
        return $response;
    }


}