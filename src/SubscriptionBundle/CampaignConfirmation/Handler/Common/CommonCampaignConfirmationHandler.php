<?php


namespace SubscriptionBundle\CampaignConfirmation\Handler\Common;


use SubscriptionBundle\CampaignConfirmation\Handler\CampaignConfirmationInterface;

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