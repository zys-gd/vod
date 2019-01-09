<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 12:21
 */

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Exception\RedirectRequiredException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Service\BillableUserProvider;
use SubscriptionBundle\Service\SubscriptionProvider;

class RedirectAction extends Controller
{
    /**
     * @var BillableUserProvider
     */
    private $billableUserProvider;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubscriptionProvider
     */
    private $subscriptionProvider;

    /**
     * RedirectAction constructor.
     * @param BillableUserProvider $billableUserProvider
     * @param LoggerInterface      $logger
     * @param SubscriptionProvider $subscriptionProvider
     */
    public function __construct(
        BillableUserProvider $billableUserProvider,
        LoggerInterface $logger,
        SubscriptionProvider $subscriptionProvider
    )
    {
        $this->billableUserProvider = $billableUserProvider;
        $this->logger               = $logger;
        $this->subscriptionProvider = $subscriptionProvider;
    }


    /**
     * This function is added to handle the need for redirect after getting redirect response from billing framework.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        $redirectURL = $request->getSchemeAndHttpHost();

        try {
            $billableUser = $this->billableUserProvider->getFromRequest($request);
            $subscription = $this->subscriptionProvider->getExistingSubscriptionForBillableUser($billableUser);

            if (isset($subscription) && $subscription->isRedirectRequired()) {
                $redirectURL = $subscription->getRedirectUrl();
            } else {
                throw new RedirectRequiredException($redirectURL);
            }

        } catch (RedirectRequiredException $ex) {
            $redirectURL = $ex->getRedirectUrl();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
        return $this->redirect($redirectURL);
    }
}