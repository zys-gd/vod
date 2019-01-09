<?php

namespace SubscriptionBundle\Service\Action\Common;

use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\EntitySaveHelper;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.06.18
 * Time: 14:57
 */
class RedirectUrlNullifier
{


    // TODO get possible ways of handling redirect subscription urls by specific page
    /**
     * @var EntitySaveHelper
     */
    private $entitySaveHelper;

    /**
     * RedirectUrlNullifier constructor.
     */
    public function __construct(EntitySaveHelper $entitySaveHelper)
    {
        $this->entitySaveHelper = $entitySaveHelper;
    }

    public function processSubscriptionAndSave(Subscription $subscription)
    {
        $subscription->setRedirectUrl(null);
        $this->entitySaveHelper->persistAndSave($subscription);

    }
}