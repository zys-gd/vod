<?php


namespace SubscriptionBundle\Service\Action\SubscribeBack\Common;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\DeviceDataProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\UserFactory;
use SubscriptionBundle\Service\Action\Subscribe\Common\BlacklistVoter;
use SubscriptionBundle\Service\Action\Subscribe\Common\CommonFlowHandler as Subscriber;
use SubscriptionBundle\Service\Action\SubscribeBack\Handler\SubscribeBackHandlerInterface;
use SubscriptionBundle\Service\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * CommonFlowHandler constructor.
     *
     * @param Subscriber                  $subscriber
     * @param UserFactory                 $userFactory
     * @param BlacklistVoter              $blacklistVoter
     * @param BlacklistAttemptRegistrator $blacklistAttemptRegistrator
     * @param RouterInterface             $router
     * @param DeviceDataProvider          $deviceDataProvider
     * @param IdentificationDataStorage   $identificationDataStorage
     */
    public function __construct(
        Subscriber $subscriber,
        UserFactory $userFactory,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator,
        RouterInterface $router,
        DeviceDataProvider $deviceDataProvider,
        IdentificationDataStorage $identificationDataStorage
    )
    {
        $this->subscriber                  = $subscriber;
        $this->blacklistVoter              = $blacklistVoter;
        $this->blacklistAttemptRegistrator = $blacklistAttemptRegistrator;
        $this->router                      = $router;
        $this->userFactory                 = $userFactory;
        $this->deviceDataProvider          = $deviceDataProvider;
        $this->identificationDataStorage   = $identificationDataStorage;
    }

    public function process(
        Request $request,
        CarrierInterface $carrier,
        SubscribeBackHandlerInterface $handler
    )
    {
        $msisdn    = $request->get('msisdn');
        $carrierId = $request->get('carrier');
        $status    = $request->get('status');
        $processId = $request->get('bf_process_id');

        /** @var User $user */
        $user = $this->userFactory->create(
            $msisdn,
            $carrier,
            $request->getClientIp(),
            $this->identificationDataStorage->getIdentificationToken(),
            $processId,
            $this->deviceDataProvider->get()
        );

        if ($this->blacklistVoter->isUserBlacklisted($request->getSession())
            || !$this->blacklistAttemptRegistrator->registerSubscriptionAttempt($user->getIdentificationToken(), (int)$carrierId)
        ) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }

        try {
            return $this->subscriber->process($request, $user);
        } /*catch (SubscribingProcessException $exception) {
            return $handler->getSubscriptionErrorResponse($exception);
        }*/ catch (\Exception $exception) {
            return new RedirectResponse($this->router->generate('whoops'));
        }
    }
}