<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.10.18
 * Time: 10:41
 */

namespace SubscriptionBundle\Service\Action\Subscribe\Common;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SubscriptionBundle\Service\Action\Common\RedirectUrlNullifier;
use SubscriptionBundle\Service\SubscriptionExtractor;
use IdentificationBundle\Entity\User;

class CommonResponseCreator
{
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionProvider;
    /**
     * @var RedirectUrlNullifier
     */
    private $redirectUrlNullifier;

    /**
     * CommonResponseCreator constructor.
     *
     * @param SubscriptionExtractor $subscriptionProvider
     * @param RedirectUrlNullifier  $redirectUrlNullifier
     */
    public function __construct(SubscriptionExtractor $subscriptionProvider, RedirectUrlNullifier $redirectUrlNullifier)
    {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->redirectUrlNullifier = $redirectUrlNullifier;
    }


    /**
     * @param Request      $request
     * @param User $User
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createCommonHttpResponse(Request $request, User $User): Response
    {
        $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($User);

        if ($redirectUrl = $subscription->getRedirectUrl()) {
            $this->redirectUrlNullifier->processSubscriptionAndSave($subscription);
        } else {
            $redirectUrl = $request->get('location', '/');
        }

        $jsRequest = $request->get('is_ajax_request', null);

        if ($redirectUrl && $jsRequest) {
            /** Should be fixed soon, cause it is general case not only for Airtel */
            return new JsonResponse(['data' => ['airtel_subscribe' => true, 'url' => $redirectUrl]], 200, []);
        } else {
            return new RedirectResponse($redirectUrl);
        }
    }
}