<?php


namespace SubscriptionBundle\CampaignConfirmation\Handler;


interface CampaignConfirmationInterface
{
    /**
     * @param string $affiliateUuid
     *
     * @return bool
     */
    public function canHandle(string $affiliateUuid): bool;
}