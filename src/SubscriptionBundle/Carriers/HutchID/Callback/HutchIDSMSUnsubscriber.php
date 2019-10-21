<?php


namespace SubscriptionBundle\Carriers\HutchID\Callback;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Callback\Common\Handler\UnsubscriptionCallbackHandler;

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

    public function __construct(
        UnsubscriptionCallbackHandler $unsubscriptionCallbackHandler,
        EntitySaveHelper $entitySaveHelper
    )
    {
        $this->unsubscriptionCallbackHandler = $unsubscriptionCallbackHandler;
        $this->entitySaveHelper              = $entitySaveHelper;
    }

    public function unsubscribe(Subscription $subscription, ProcessResult $response)
    {
        $this->unsubscriptionCallbackHandler->doProcess($subscription, $response);
        $this->entitySaveHelper->persistAndSave($subscription);
    }
}