<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\ProcessResponseMapper;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Callback\Common\SubscriptionPreparer;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomConversionTrackingRules;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomFlow;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Subscribe\Common\SubscriptionEventTracker;
use SubscriptionBundle\Subscription\Subscribe\Exception\SubscriptionFlowException;
use Symfony\Component\HttpFoundation\Request;

class HutchIDCallbackSubscribe implements CarrierCallbackHandlerInterface, HasCustomFlow
{
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var TokenGenerator
     */
    private $generator;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var ProcessResponseMapper
     */
    private $processResponseMapper;
    /**
     * @var HutchIDSMSSubscriber
     */
    private $hutchIDSMSSubscriber;

    /**
     * @var Notifier
     */
    private $notifier;
    /**
     * @var SubscriptionEventTracker
     */
    private $subscriptionEventTracker;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProcessResultSuccessChecker
     */
    private $resultSuccessChecker;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;
    /**
     * @var SubscriptionPreparer
     */
    private $subscriptionPreparer;

    /**
     * HutchIDCallbackSubscribe constructor.
     *
     * @param CommonFlowHandler           $commonFlowHandler
     * @param EntitySaveHelper            $entitySaveHelper
     * @param TokenGenerator              $generator
     * @param IdentificationDataStorage   $identificationDataStorage
     * @param ProcessResponseMapper       $processResponseMapper
     * @param HutchIDSMSSubscriber        $hutchIDSMSSubscriber
     * @param Notifier                    $notifier
     * @param SubscriptionEventTracker    $subscriptionEventTracker
     * @param LoggerInterface             $logger
     * @param ProcessResultSuccessChecker $processResultSuccessChecker
     * @param ApiConnector                $apiConnector
     * @param SubscriptionPreparer        $subscriptionPreparer
     */
    public function __construct(
        CommonFlowHandler $commonFlowHandler,
        EntitySaveHelper $entitySaveHelper,
        TokenGenerator $generator,
        IdentificationDataStorage $identificationDataStorage,
        ProcessResponseMapper $processResponseMapper,
        HutchIDSMSSubscriber $hutchIDSMSSubscriber,
        Notifier $notifier,
        SubscriptionEventTracker $subscriptionEventTracker,
        LoggerInterface $logger,
        ProcessResultSuccessChecker $processResultSuccessChecker,
        ApiConnector $apiConnector,
        SubscriptionPreparer $subscriptionPreparer
    )
    {
        $this->commonFlowHandler         = $commonFlowHandler;
        $this->entitySaveHelper          = $entitySaveHelper;
        $this->generator                 = $generator;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->processResponseMapper     = $processResponseMapper;
        $this->hutchIDSMSSubscriber      = $hutchIDSMSSubscriber;
        $this->notifier                  = $notifier;
        $this->subscriptionEventTracker  = $subscriptionEventTracker;
        $this->logger                    = $logger;
        $this->resultSuccessChecker      = $processResultSuccessChecker;
        $this->crossSubscriptionApi      = $apiConnector;
        $this->subscriptionPreparer      = $subscriptionPreparer;
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
        $requestParams   = (Object)$request->request->all();
        $processResponse = $this->processResponseMapper->map($type, (object)['data' => $requestParams]);

        try {
            if ($requestParams->provider_fields['source'] != 'SMS') {
                $this->logger->info('Hutch ID listen callback source is not SMS');
                throw new SubscriptionFlowException();
            }

            $this->logger->debug('Callback listen process debug', [$processResponse, $requestParams]);

            [$carrier, $user, $subscription] = $this->subscriptionPreparer->makeUserWithSubscription($processResponse);

            $this->hutchIDSMSSubscriber->subscribe($subscription, $processResponse);
            $this->entitySaveHelper->persistAndSave($subscription);

            // send SMS
            if ($subscription->isSubscribed() && $this->resultSuccessChecker->isSuccessful($processResponse)) {
                $this->logger->debug('Hutch ID listen callback created successful subscription. Start tracking');
                // subscribe sms
                // $this->notifier->sendNotification(
                //     SubscribeProcess::PROCESS_METHOD_SUBSCRIBE,
                //     $subscription,
                //     $subscription->getSubscriptionPack(),
                //     $carrier
                // );

                $this->notifier->sendNotification(
                    'notify_renew',
                    $subscription,
                    $subscription->getSubscriptionPack(),
                    $carrier
                );

                // track event
                $isNeedToBeTracked = true;
                if ($this instanceof HasCustomConversionTrackingRules) {
                    $isNeedToBeTracked = $this->isConversionNeedToBeTracked($processResponse);
                }
                if ($isNeedToBeTracked) {
                    $this->subscriptionEventTracker->trackSubscribe($subscription, $processResponse);
                }

                $this->crossSubscriptionApi->registerSubscription($user->getIdentifier(), $user->getBillingCarrierId());
            }

            $this->logger->debug('Hutch ID listen callback created subscription', [$subscription]);
            return $subscription;

        } catch (SubscriptionFlowException $e) {
            $this->logger->info('Hutch ID listen callback through common flow');
            return $this->commonFlowHandler->process($request, ID::HUTCH3_INDONESIA_DOT, $type);
        }
    }
}