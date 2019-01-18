<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 12:22
 */

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Exception\RedirectRequiredException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Exception\SubscriptionException;
use SubscriptionBundle\Service\Action\Subscribe\Common\SubscriptionEligibilityChecker;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Service\Action\Unsubscribe\Unsubscriber;
use SubscriptionBundle\Service\Action\Unsubscribe\UnsubscriptionEligibilityChecker;
use SubscriptionBundle\Service\UserExtractor;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use SubscriptionBundle\Service\SubscriptionExtractor;
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
        UnsubscriptionHandlerProvider $handlerProvider

    )
    {
        $this->UserProvider     = $UserProvider;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->unsubscriber             = $unsubscriber;
        $this->checker                  = $checker;
        $this->subscriptionProvider     = $subscriptionProvider;
        $this->handlerProvider          = $handlerProvider;
    }

    public function __invoke(Request $request)
    {
        $response = null;
        try {
            $User     = $this->UserProvider->getUserFromRequest($request);
            $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($User);
            $subscription     = $this->subscriptionProvider->getExistingSubscriptionForUser($User);

            if (!is_null($subscription) && !$this->checker->isEligibleToUnsubscribe($subscription)) {
                throw new SubscriptionException('You have already been unsubscribed');
            }

            $handler  = $this->handlerProvider->getUnsubscriptionHandler($User->getCarrier());
            $response = $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);

            $handler->applyPostUnsubscribeChanges($subscription);
            if ($handler->isPiwikNeedToBeTracked($response)) {
                $this->unsubscriber->trackEventsForUnsubscribe($subscription, $response);
            }

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