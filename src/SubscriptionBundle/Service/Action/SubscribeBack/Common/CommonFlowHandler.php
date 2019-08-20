<?php


namespace SubscriptionBundle\Service\Action\SubscribeBack\Common;


use ExtrasBundle\Utils\UrlParamAppender;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\DeviceDataProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Service\Action\Subscribe\Common\BlacklistVoter;
use SubscriptionBundle\Service\Action\SubscribeBack\Subscriber;
use SubscriptionBundle\Service\Action\SubscribeBack\Handler\SubscribeBackHandlerInterface;
use SubscriptionBundle\Service\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CommonFlowHandler
{
    /**
     * @var CommonFlowHandler
     */
    private $subscriber;

    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;
    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistAttemptRegistrator;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var DeviceDataProvider
     */
    private $deviceDataProvider;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * CommonFlowHandler constructor.
     *
     * @param Subscriber                  $subscriber
     * @param UserFactory                 $userFactory
     * @param BlacklistVoter              $blacklistVoter
     * @param BlacklistAttemptRegistrator $blacklistAttemptRegistrator
     * @param RouterInterface             $router
     * @param DeviceDataProvider          $deviceDataProvider
     * @param IdentificationDataStorage   $identificationDataStorage
     * @param SubscriptionPackProvider    $subscriptionPackProvider
     * @param SubscriptionExtractor       $subscriptionExtractor
     * @param UrlParamAppender            $urlParamAppender
     * @param UserRepository              $userRepository
     */
    public function __construct(
        Subscriber $subscriber,
        UserFactory $userFactory,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator,
        RouterInterface $router,
        DeviceDataProvider $deviceDataProvider,
        IdentificationDataStorage $identificationDataStorage,
        SubscriptionPackProvider $subscriptionPackProvider,
        SubscriptionExtractor $subscriptionExtractor,
        UrlParamAppender $urlParamAppender,
        UserRepository $userRepository
    )
    {
        $this->subscriber                  = $subscriber;
        $this->blacklistVoter              = $blacklistVoter;
        $this->blacklistAttemptRegistrator = $blacklistAttemptRegistrator;
        $this->router                      = $router;
        $this->userFactory                 = $userFactory;
        $this->deviceDataProvider          = $deviceDataProvider;
        $this->identificationDataStorage   = $identificationDataStorage;
        $this->subscriptionPackProvider    = $subscriptionPackProvider;
        $this->subscriptionExtractor       = $subscriptionExtractor;
        $this->urlParamAppender            = $urlParamAppender;
        $this->userRepository              = $userRepository;
    }

    /**
     * @param Request                       $request
     * @param CarrierInterface              $carrier
     * @param SubscribeBackHandlerInterface $handler
     *
     * @return RedirectResponse|void
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function process(
        Request $request,
        CarrierInterface $carrier,
        SubscribeBackHandlerInterface $handler
    ): Response
    {
        $msisdn           = $request->get('msisdn');
        $billingProcessId = $request->get('bf_process_id');

        $redirect_url = $this->router->generate('index');

        if (!($user = $this->userRepository->findOneByMsisdn($msisdn))) {
            // finish identification
            $identificationToken = $this->identificationDataStorage->getIdentificationToken();

            $user = $this->userFactory->create(
                $msisdn,
                $carrier,
                $request->getClientIp(),
                $identificationToken,
                $billingProcessId,
                $this->deviceDataProvider->get()
            );

            $this->identificationDataStorage->setIdentificationToken($identificationToken);
        }

        $subscription = $this->subscriptionExtractor->getExistingSubscriptionForUser($user);
        if ($subscription) {
            $updatedUrl = $this->urlParamAppender->appendUrl($redirect_url, [
                'err_handle' => 'already_subscribed'
            ]);

            return new RedirectResponse($updatedUrl);
        }

        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        if ($this->blacklistVoter->isPhoneNumberBlacklisted($msisdn)) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }

        try {
            $this->subscriber->subscribe($user, $subscriptionPack, $billingProcessId);
            return new RedirectResponse($redirect_url);
        } catch (\Exception $exception) {
            return new RedirectResponse($this->router->generate('whoops'));
        }
    }
}