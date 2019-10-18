<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 10:30
 */

namespace SubscriptionBundle\Subscription\Subscribe\Controller;


use ExtrasBundle\Controller\Traits\ResponseTrait;
use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\User\Service\UserExtractor;
use SubscriptionBundle\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\CampaignConfirmation\Handler\CampaignConfirmationHandlerProvider;
use SubscriptionBundle\CampaignConfirmation\Handler\CustomPage;
use SubscriptionBundle\CAPTool\Common\CAPToolRedirectUrlResolver;
use SubscriptionBundle\CAPTool\Subscription\Exception\CapToolAccessException;
use SubscriptionBundle\CAPTool\Subscription\Exception\SubscriptionCapReachedOnAffiliate;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimiter;
use SubscriptionBundle\Piwik\DataMapper\SubscribeClickEventMapper;
use SubscriptionBundle\Piwik\DataMapper\UserInformationMapper;
use SubscriptionBundle\Subscription\Subscribe\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Subscribe\Controller\ACL\SubscribeActionACL;
use SubscriptionBundle\Subscription\Subscribe\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Subscription\Subscribe\Service\BlacklistVoter;
use SubscriptionBundle\Subscription\Subscribe\Voter\BatchSubscriptionVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * SubscribeAction constructor.
     *
     * @param \IdentificationBundle\User\Service\UserExtractor $userExtractor
     * @param CommonFlowHandler                                $commonFlowHandler
     * @param SubscriptionHandlerProvider                      $handlerProvider
     * @param SubscriptionLimiter                              $subscriptionLimiter
     * @param RouteProvider                                    $routeProvider
     * @param SubscribeActionACL                               $ACL
     */
    public function __construct(
        UserExtractor $userExtractor,
        CommonFlowHandler $commonFlowHandler,
        SubscriptionHandlerProvider $handlerProvider,
        SubscriptionLimiter $subscriptionLimiter,
        RouteProvider $routeProvider,
        SubscribeActionACL $ACL
    )
    {

        $this->userExtractor         = $userExtractor;
        $this->commonFlowHandler     = $commonFlowHandler;
        $this->handlerProvider       = $handlerProvider;
        $this->subscriptionLimiter   = $subscriptionLimiter;
        $this->routeProvider         = $routeProvider;
        $this->ACL                   = $ACL;
    }


    /**
     * @param Request            $request
     * @param IdentificationData $identificationData
     * @param ISPData            $ISPData
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound
     */
    public function __invoke(Request $request, IdentificationData $identificationData, ISPData $ISPData)
    {

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