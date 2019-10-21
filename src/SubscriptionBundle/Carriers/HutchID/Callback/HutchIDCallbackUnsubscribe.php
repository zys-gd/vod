<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomFlow;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeFacade;
use Symfony\Component\HttpFoundation\Request;

class HutchIDCallbackUnsubscribe implements CarrierCallbackHandlerInterface, HasCustomFlow
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var UnsubscribeFacade
     */
    private $unsubscribeFacade;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var HutchIDSMSUnsubscriber
     */
    private $hutchIDSMSUnsubscriber;
    /**
     * @var ProcessResponseMapper
     */
    private $processResponseMapper;

    /**
     * HutchIDCallbackUnsubscribe constructor.
     *
     * @param UserRepository         $userRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param UnsubscribeFacade      $unsubscribeFacade
     * @param HutchIDSMSUnsubscriber $unsubscriber
     * @param ProcessResponseMapper  $processResponseMapper
     */
    public function __construct(
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        UnsubscribeFacade $unsubscribeFacade,
        HutchIDSMSUnsubscriber $unsubscriber,
        ProcessResponseMapper $processResponseMapper
    )
    {
        $this->userRepository         = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->unsubscribeFacade      = $unsubscribeFacade;
        $this->hutchIDSMSUnsubscriber = $unsubscriber;
        $this->processResponseMapper  = $processResponseMapper;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ID::HUTCH3_INDONESIA_DOT;
    }

    /**
     * @param Request $request
     * @param string  $type
     *
     * @return Subscription
     * @throws \Exception
     */
    public function process(Request $request, string $type)
    {
        $requestParams = (Object)$request->request->all();

        $msisdn       = $requestParams->client_user ?? $requestParams->provider_user;
        $user         = $this->userRepository->findOneByMsisdn($msisdn);
        $subscription = $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);

        if (isset($requestParams->provider_fields['source']) && $requestParams->provider_fields['source'] == 'SMS') {
            $processResponse = $this->processResponseMapper->map($type, (object)['data' => $requestParams]);
            $this->hutchIDSMSUnsubscriber->unsubscribe($subscription, $processResponse);
        }
        else {
            $this->unsubscribeFacade->doFullUnsubscribe($subscription);
        }

        return $subscription;
    }
}