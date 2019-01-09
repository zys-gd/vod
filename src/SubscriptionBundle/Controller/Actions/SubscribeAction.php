<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 10:30
 */

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Exception\PendingIdentificationException;
use IdentificationBundle\Exception\RedirectRequiredException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Exception\ExistingSubscriptionException;
use SubscriptionBundle\Exception\SubscriptionException;
use SubscriptionBundle\Service\Action\Subscribe\Common\BlacklistVoter;
use SubscriptionBundle\Service\Action\Subscribe\Common\CommonFlowHandler;
use SubscriptionBundle\Service\Action\Subscribe\Exception\ResubscriptionIsNotAllowedException;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomFlow;
use SubscriptionBundle\Carriers\Megasyst\Subscribe\MegasystCarrierChecker;
use SubscriptionBundle\Carriers\Megasyst\Subscribe\MegasystPhoneChecker;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Service\BillableUserProvider;
use SubscriptionBundle\Service\Legacy\MobilifeSubscriberService;
use SubscriptionBundle\Service\Legacy\MobimindSubscriberService;
use SubscriptionBundle\Utils\UrlParamAppender;

class SubscribeAction extends Controller
{
    use ResponseTrait;

    /**
     * @var BillableUserProvider
     */
    private $billableUserProvider;
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
     * @var MobimindSubscriberService
     */
    private $mobimindService;
    /**
     * @var \SubscriptionBundle\Carriers\Megasyst\Subscribe\MegasystCarrierChecker
     */
    private $megasystCarrierChecker;
    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var \SubscriptionBundle\Carriers\Megasyst\\SubscriptionBundle\Carriers\Megasyst\Subscribe\MegasystPhoneChecker
     */
    private $megasystPhoneChecker;
    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;
    /**
     * @var MobilifeSubscriberService
     */
    private $mobilifeService;
    /**
     * @var SubscriptionHandlerProvider
     */
    private $handlerProvider;


    /**
     * SubscribeAction constructor.
     * @param BillableUserProvider                                                             $billableUserProvider
     * @param CommonFlowHandler                                                                $commonFlowHandler
     * @param Router                                                                           $router
     * @param LoggerInterface                                                                  $logger
     * @param MobimindSubscriberService                                                        $mobimindService
     * @param \SubscriptionBundle\Carriers\Megasyst\Subscribe\MegasystCarrierChecker $megasystCarrierChecker
     * @param BlacklistVoter                                                                   $blacklistVoter
     * @param \SubscriptionBundle\Carriers\Megasyst\Subscribe\MegasystPhoneChecker   $megasystPhoneChecker
     * @param UrlParamAppender                                                                 $urlParamAppender
     * @param MobilifeSubscriberService                                                        $mobilifeService
     * @param SubscriptionHandlerProvider                                                      $handlerProvider
     */
    public function __construct(
        BillableUserProvider $billableUserProvider,
        CommonFlowHandler $commonFlowHandler,
        Router $router,
        LoggerInterface $logger,
        MobimindSubscriberService $mobimindService,
        MegasystCarrierChecker $megasystCarrierChecker,
        BlacklistVoter $blacklistVoter,
        MegasystPhoneChecker $megasystPhoneChecker,
        UrlParamAppender $urlParamAppender,
        MobilifeSubscriberService $mobilifeService,
        SubscriptionHandlerProvider $handlerProvider
    )
    {
        $this->billableUserProvider   = $billableUserProvider;
        $this->commonFlowHandler      = $commonFlowHandler;
        $this->router                 = $router;
        $this->logger                 = $logger;
        $this->mobimindService        = $mobimindService;
        $this->megasystCarrierChecker = $megasystCarrierChecker;
        $this->blacklistVoter         = $blacklistVoter;
        $this->megasystPhoneChecker   = $megasystPhoneChecker;
        $this->urlParamAppender       = $urlParamAppender;
        $this->mobilifeService        = $mobilifeService;
        $this->handlerProvider        = $handlerProvider;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws ExistingSubscriptionException
     * @throws RedirectRequiredException
     * @throws ResubscriptionIsNotAllowedException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \IdentificationBundle\Exception\UndefinedIdentityException
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     * @throws \SubscriptionBundle\Exception\PendingSubscriptionException
     */
    public function __invoke(Request $request)
    {


        if ($result = $this->handleRequestByLegacyService($request)) {
            return $result;
        }

        if ($result = $this->blacklistVoter->checkIfSubscriptionRestricted($request)) {
            return $result;
        }

        try {
            $billableUser = $this->billableUserProvider->getFromRequest($request);
        } catch (PendingIdentificationException $e) {
            $billableUser = $this->tryToRetrieveUserWithDelay($request);
        } catch (RedirectRequiredException $ex) {

            $this->logger->debug('Redirect Required', [
                'url'     => $ex->getRedirectUrl(),
                'message' => $ex->getMessage()
            ]);


            return $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => false,
                'subscription'   => false,
                'redirectUrl'    => $ex->getRedirectUrl(),
            ]);
        }

        $subscriber = $this->handlerProvider->getSubscriber($billableUser->getCarrier());
        if ($subscriber instanceof HasCustomFlow) {
            return $subscriber->process($request, $billableUser, $request->getSession());
        } else {
            return $this->commonFlowHandler->process($request, $billableUser);
        }


    }

    private function handleRequestByLegacyService(Request $request)
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


    }

    /**
     * @param Request $request
     * @return \UserBundle\Entity\BillableUser
     * @throws RedirectRequiredException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \IdentificationBundle\Exception\UndefinedIdentityException
     */
    private function tryToRetrieveUserWithDelay(Request $request): \UserBundle\Entity\BillableUser
    {
        for ($i = 0; $i < 3; $i++) {
            try {
                $this->logger->debug('Trying to retrieve user with delay', ['attempt' => $i]);
                $billableUser = $this->billableUserProvider->getFromRequest($request);
                $this->logger->debug('Attempt was successful', ['attempt' => $i]);
                break;
            } catch (PendingIdentificationException $e) {
                $this->logger->debug('Failed', ['attempt' => $i]);
            }
            sleep(1);
        }
        return $billableUser;
    }


}