<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.10.19
 * Time: 17:27
 */

namespace SubscriptionBundle\CAPTool\Common;


use SubscriptionBundle\CAPTool\Subscription\Exception\CapToolAccessException;
use SubscriptionBundle\CAPTool\Subscription\Exception\SubscriptionCapReachedOnAffiliate;
use SubscriptionBundle\CAPTool\Subscription\Exception\SubscriptionCapReachedOnCarrier;
use SubscriptionBundle\CAPTool\Subscription\Exception\VisitCapReached;
use SubscriptionBundle\Subscription\Common\RouteProvider;

class CAPToolRedirectUrlResolver
{
    /**
     * @var RouteProvider
     */
    private $subscriptionRouteProvider;


    /**
     * CAPToolRedirectUrlResolver constructor.
     * @param RouteProvider $subscriptionRouteProvider
     */
    public function __construct(RouteProvider $subscriptionRouteProvider)
    {
        $this->subscriptionRouteProvider = $subscriptionRouteProvider;
    }

    public function resolveUrl(CapToolAccessException $exception): string
    {
        $defaultUrl = $this->subscriptionRouteProvider->getActionIsNotAllowedUrl();

        // God Bless `instanceof`
        if ($exception instanceof SubscriptionCapReachedOnAffiliate) {
            if ($newUrl = $exception->getConstraint()->getRedirectUrl()) {
                return $newUrl;
            }
        }

        if ($exception instanceof SubscriptionCapReachedOnCarrier) {
            if ($newUrl = $exception->getCarrier()->getRedirectUrl()) {
                return $newUrl;
            }
        }

        if ($exception instanceof VisitCapReached) {
            if ($newUrl = $exception->getConstraint()->getRedirectUrl()) {
                return $newUrl;
            }
        }

        return $defaultUrl;

    }
}