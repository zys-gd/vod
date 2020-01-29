<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 10:30
 */

namespace SubscriptionBundle\Subscription\Subscribe\Controller;


use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\User\Service\UserExtractor;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimiter;
use SubscriptionBundle\Subscription\Subscribe\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Subscribe\Controller\ACL\SubscribeActionACL;
use SubscriptionBundle\Subscription\Subscribe\Controller\Event\SubscribeClickEventTracker;
use SubscriptionBundle\Subscription\Subscribe\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscribeAction extends AbstractController
{
    /**
     * @var UserExtractor
     */
    private $userExtractor;
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;
    /**
     * @var SubscriptionHandlerProvider
     */
    private $handlerProvider;

    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;

    /**
     * @var RouteProvider
     */
    private $routeProvider;

    /**
     * @var SubscribeActionACL
     */
    private $ACL;
    /**
     * @var SubscribeClickEventTracker
     */
    private $clickEventTracker;

    /**
     * SubscribeAction constructor.
     *
     * @param \IdentificationBundle\User\Service\UserExtractor $userExtractor
     * @param CommonFlowHandler                                $commonFlowHandler
     * @param SubscriptionHandlerProvider                      $handlerProvider
     * @param SubscriptionLimiter                              $subscriptionLimiter
     * @param RouteProvider                                    $routeProvider
     * @param SubscribeActionACL                               $ACL
     * @param SubscribeClickEventTracker                       $clickEventTracker
     */
    public function __construct(
        UserExtractor $userExtractor,
        CommonFlowHandler $commonFlowHandler,
        SubscriptionHandlerProvider $handlerProvider,
        SubscriptionLimiter $subscriptionLimiter,
        RouteProvider $routeProvider,
        SubscribeActionACL $ACL,
        SubscribeClickEventTracker $clickEventTracker
    )
    {

        $this->userExtractor       = $userExtractor;
        $this->commonFlowHandler   = $commonFlowHandler;
        $this->handlerProvider     = $handlerProvider;
        $this->subscriptionLimiter = $subscriptionLimiter;
        $this->routeProvider       = $routeProvider;
        $this->ACL                 = $ACL;
        $this->clickEventTracker   = $clickEventTracker;
    }


    /**
     * @param Request            $request
     * @param IdentificationData $identificationData
     * @param ISPData            $ISPData
     *
     * @return RedirectResponse|Response|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function __invoke(Request $request, IdentificationData $identificationData, ISPData $ISPData)
    {
        $this->clickEventTracker->trackEvent($request);

        if ($aclOverride = $this->ACL->checkIfActionIsAllowed($request, $ISPData, $identificationData)) {
            return $aclOverride;
        }

        $user = $this->userExtractor->getUserByIdentificationData($identificationData);
        if ($this->subscriptionLimiter->need2BeLimited($user)) {
            $this->subscriptionLimiter->reserveSlotForSubscription($request->getSession());
        }

        try {
            $subscriber = $this->handlerProvider->getSubscriber($user->getCarrier());
            if ($subscriber instanceof HasCustomFlow) {
                return $subscriber->process($request, $request->getSession(), $user);
            } else {
                return $this->commonFlowHandler->process($request, $user);
            }
        } catch (ExistingSubscriptionException $exception) {
            return new RedirectResponse($this->routeProvider->getLinkToHomepage(['err_handle' => 'already_subscribed']));
        }
    }


}