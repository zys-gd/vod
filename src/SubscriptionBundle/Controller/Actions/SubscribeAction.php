<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 10:30
 */

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Identification\DTO\IdentificationData;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Service\Action\Subscribe\Common\BlacklistVoter;
use SubscriptionBundle\Service\Action\Subscribe\Common\CommonFlowHandler;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Service\UserExtractor;
use ExtrasBundle\Utils\UrlParamAppender;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class SubscribeAction extends Controller
{
    use ResponseTrait;

    /**
     * @var UserExtractor
     */
    private $userExtractor;
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;
    /**
     * @var SubscriptionHandlerProvider
     */
    private $handlerProvider;


    /**
     * SubscribeAction constructor.
     *
     * @param UserExtractor               $userExtractor
     * @param CommonFlowHandler           $commonFlowHandler
     * @param Router                      $router
     * @param LoggerInterface             $logger
     * @param UrlParamAppender            $urlParamAppender
     * @param SubscriptionHandlerProvider $handlerProvider
     * @param BlacklistVoter              $blacklistVoter
     */
    public function __construct(
        UserExtractor $userExtractor,
        CommonFlowHandler $commonFlowHandler,
        Router $router,
        LoggerInterface $logger,
        UrlParamAppender $urlParamAppender,
        SubscriptionHandlerProvider $handlerProvider,
        BlacklistVoter $blacklistVoter
    )
    {
        $this->userExtractor     = $userExtractor;
        $this->commonFlowHandler = $commonFlowHandler;
        $this->router            = $router;
        $this->logger            = $logger;
        $this->urlParamAppender  = $urlParamAppender;
        $this->handlerProvider   = $handlerProvider;
        $this->blacklistVoter    = $blacklistVoter;
    }

    /**
     * @param Request            $request
     * @param IdentificationData $identificationData
     * @return \Symfony\Component\HttpFoundation\JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws ExistingSubscriptionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     */
    public function __invoke(Request $request, IdentificationData $identificationData)
    {


        /*if ($result = $this->handleRequestByLegacyService($request)) {
            return $result;
        }*/

        if ($result = $this->blacklistVoter->checkIfSubscriptionRestricted($request)) {
            return $result;
        }

        $user = $this->userExtractor->getUserByIdentificationData($identificationData);


        $subscriber = $this->handlerProvider->getSubscriber($user->getCarrier());
        if ($subscriber instanceof HasCustomFlow) {
            return $subscriber->process($request, $user, $request->getSession());
        } else {
            return $this->commonFlowHandler->process($request, $user);
        }


    }

    /*private function handleRequestByLegacyService(Request $request)
    {
        if ($this->mobimindService->isMobimind($request)) {
            return $this->mobimindService->processRequest($request);
        }

        if ($this->mobilifeService->isMobilife($request)) {
            return $this->mobilifeService->processRequest($request);
        }

        $carrier = $request->get('carrier');
        if ($this->megasystCarrierChecker->isMegasystCarrierId($carrier)) {

            if ($phone = $request->get('phone')) {
                return new JsonResponse($this->megasystPhoneChecker->checkPhone($phone, $carrier));
            } else {
                return new Response(200);
            }
        }


    }*/


}