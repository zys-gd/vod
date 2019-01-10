<?php

namespace SubscriptionBundle\Service\SubscriptionText\Periodicity;


class Weekly implements IPeriodicity
{
    /**
     * @param int    $billingCarrierId
     * @param int    $subPackId
     * @param int    $engLangId
     * @param string $creditsPhrase
     * @param string $termsPhrase
     * @param int    $period
     * @return string
     */
    public function getInsertValues(int $billingCarrierId, int $subPackId, int $engLangId, string $creditsPhrase, string $termsPhrase, int $period): string
    {
        return "({$billingCarrierId}, 'subpack.subscription_offer_home', 'Get {$creditsPhrase} per week for only %price% %currency%', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_home_subscribed', 'You are subscribed to Playwing Premium', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_club', 'After the offer period, get {$creditsPhrase} each week for only %price% %currency%/week.', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_club_header', '%price% %currency% / week.', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_landing', '%price% %currency% / week', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_landing_top', '%price% %currency% / week', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_landing_bottom', '%price% %currency% / week', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_slider', '%price% %currency% / week', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_buttons', '%price% %currency% / week', {$engLangId}, {$subPackId}),
            
            ({$billingCarrierId}, 'subpack.subscription_offer_terms', 'This subscription service allows you to download {$creditsPhrase} per week on your phone from Playwing catalogue for %price% %currency% (+ WAP connection charges). {$termsPhrase}. As long as you are subscribed to Playwing Premium, you will get access to {$creditsPhrase} each week. Downloaded games from Playwing store are for users to keep (not a renting model).', {$engLangId}, {$subPackId})";
    }

    public function getPeriodicityPhrase(int $period): string
    {
        return 'week';
    }
}