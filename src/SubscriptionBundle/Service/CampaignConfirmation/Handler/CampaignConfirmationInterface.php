<?php


namespace SubscriptionBundle\Service\CampaignConfirmation\Handler;


interface CampaignConfirmationInterface
{
    /**
     * @param string $affiliateUuid
     *
     * @return bool
     */
    public function canHandle(string $affiliateUuid): bool;
}