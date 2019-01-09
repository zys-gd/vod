<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 18.06.18
 * Time: 11:50
 */

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Exception\UndefinedIdentityException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\BillableUserProvider;
use SubscriptionBundle\Service\SubscriptionProvider;

class WaitAction
{
    /**
     *
     * /**
     * @var BillableUserProvider
     */
    private $billableUserProvider;
    /**
     * @var SubscriptionProvider
     */
    private $subscriptionProvider;
    /**
     * @var Router
     */
    private $router;


    /**
     * WaitAction constructor.
     * @param BillableUserProvider $billableUserProvider
     * @param SubscriptionProvider $subscriptionProvider
     * @param Router               $router
     *
     */
    public function __construct(
        BillableUserProvider $billableUserProvider,
        SubscriptionProvider $subscriptionProvider,
        Router $router
    )
    {
        $this->billableUserProvider = $billableUserProvider;
        $this->subscriptionProvider = $subscriptionProvider;
        $this->router               = $router;
    }

    public function __invoke(Request $request)
    {
        sleep(1);

        $redirectUrl = $request->getSession()->get('location', $this->router->generate('homepage'));
        $ajax        = $request->server->get('HTTP_X_REQUESTED_WITH', false);
        try {
            $billableUser = $this->billableUserProvider->getFromRequest($request);;
        } catch (UndefinedIdentityException $exception) {
            throw new \InvalidArgumentException('Cannot parse incoming request');
        }

        $subscriptionEntity = $this->subscriptionProvider->getExistingSubscriptionForBillableUser($billableUser);

        if ($subscriptionEntity
            && $subscriptionEntity->getStatus() === Subscription::IS_PENDING
            && $subscriptionEntity->getCurrentStage() === Subscription::ACTION_SUBSCRIBE) {

            return new RedirectResponse($this->router->generate('talentica_subscription.wait_listen'));
        }

        if (!$redirectUrl) {
            $redirectUrl = new RedirectResponse($this->router->generate('homepage'));
        }

        $request->getSession()->remove('location');

        if ($ajax) {
            $response = [
                'data' => [
                    'subtype'          => 'redirect',
                    'url'              => $redirectUrl,
                    'straightRedirect' => true
                ]
            ];
            return new JsonResponse($response);
        }

        return new RedirectResponse($redirectUrl);
    }


}