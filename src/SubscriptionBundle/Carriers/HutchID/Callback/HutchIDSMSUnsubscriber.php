<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Common\Handler\UnsubscriptionCallbackHandler;
use SubscriptionBundle\Subscription\Unsubscribe\Unsubscriber;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;

class HutchIDSMSUnsubscriber
{
    /**
     * @var UnsubscriptionCallbackHandler
     */
    private $unsubscriptionCallbackHandler;
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;
    /**
     * @var Unsubscriber
     */
    private $unsubscriber;
    /**
     * @var ApiConnector
     */
    private $crossSubscriptionApi;

    /**
     * HutchIDSMSUnsubscriber constructor.
     *
     * @param UnsubscriptionCallbackHandler $unsubscriptionCallbackHandler
     * @param EntitySaveHelper              $entitySaveHelper
     * @param Unsubscriber                  $unsubscriber
     * @param ApiConnector                  $apiConnector
     */
    public function __construct(
        UnsubscriptionCallbackHandler $unsubscriptionCallbackHandler,
        EntitySaveHelper $entitySaveHelper,
        Unsubscriber $unsubscriber,
        ApiConnector $apiConnector
    )
    {
        $this->unsubscriptionCallbackHandler = $unsubscriptionCallbackHandler;
        $this->entitySaveHelper              = $entitySaveHelper;
        $this->unsubscriber                  = $unsubscriber;
        $this->crossSubscriptionApi          = $apiConnector;
    }

    public function unsubscribe(Subscription $subscription, ProcessResult $response)
    {
        $this->unsubscriptionCallbackHandler->doProcess($subscription, $response);
        $this->entitySaveHelper->persistAndSave($subscription);

        if ($response->isSuccessful()) {
            $user = $subscription->getUser();
            $this->unsubscriber->trackEventsForUnsubscribe($subscription, $response);
            $this->crossSubscriptionApi->deregisterSubscription($user->getIdentifier(), $user->getBillingCarrierId());
        }
    }
}