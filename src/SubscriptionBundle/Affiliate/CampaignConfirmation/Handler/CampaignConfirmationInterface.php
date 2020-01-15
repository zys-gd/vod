<?php


namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Handler;


interface CampaignConfirmationInterface
{
    /**
     * @param string $affiliateUuid
     *
     * @return bool
     */
    public function isAffiliateSupported(string $affiliateUuid): bool;

    public function getHandlerId(): string;
}