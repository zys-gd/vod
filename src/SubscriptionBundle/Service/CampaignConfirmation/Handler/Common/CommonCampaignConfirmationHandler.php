<?php


namespace SubscriptionBundle\Service\CampaignConfirmation\Handler\Common;


use SubscriptionBundle\Service\CampaignConfirmation\Handler\CampaignConfirmationInterface;

class CommonCampaignConfirmationHandler implements CampaignConfirmationInterface
{
    /**
     * @param string $affiliateUuid
     *
     * @return bool
     */
    public function canHandle(string $affiliateUuid): bool
    {
        return true;
    }
}