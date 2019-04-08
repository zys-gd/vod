<?php


namespace SubscriptionBundle\Service\CampaignConfirmation\Handler\Google;


use SubscriptionBundle\Service\CampaignConfirmation\Handler\CampaignConfirmationInterface;
use SubscriptionBundle\Service\CampaignConfirmation\Handler\CustomPage;

class GoogleCampaignHandler implements CampaignConfirmationInterface, CustomPage
{
    /**
     * @param string $affiliateUuid
     *
     * @return bool
     */
    public function canHandle(string $affiliateUuid): bool
    {
        return $affiliateUuid == "514fe478-ebd4-11e8-95c4-02bb250f0f22";
    }

    public function getCustomPage(): bool
    {
        return '';
    }
}