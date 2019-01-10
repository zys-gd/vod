<?php

namespace SubscriptionBundle\Service\SubscriptionText\Periodicity;


class Custom implements IPeriodicity
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
        $periodPhrase = $this->getPeriodicityPhrase($period);

        return "({$billingCarrierId}, 'subpack.subscription_offer_home', 'Get {$creditsPhrase} per {$periodPhrase} for only %price% %currency%', {$engLangId}, {$subPackId}),
                ({$billingCarrierId}, 'subpack.subscription_offer_home_subscribed', 'You are subscribed to Playwing Premium', {$engLangId}, {$subPackId}),
                ({$billingCarrierId}, 'subpack.subscription_offer_club', 'After the offer period, get {$creditsPhrase} each {$periodPhrase} for only %price% %currency% / {$periodPhrase}.', {$engLangId}, {$subPackId}),                
                ({$billingCarrierId}, 'subpack.subscription_offer_club_header', '%price% %currency% / {$periodPhrase}.', {$engLangId}, {$subPackId}),
                ({$billingCarrierId}, 'subpack.subscription_offer_landing', '%price% %currency% / {$periodPhrase}', {$engLangId}, {$subPackId}),
                ({$billingCarrierId}, 'subpack.subscription_offer_landing_top', '%price% %currency% / {$periodPhrase}', {$engLangId}, {$subPackId}),                
                ({$billingCarrierId}, 'subpack.subscription_offer_landing_bottom', '%price% %currency% / {$periodPhrase}', {$engLangId}, {$subPackId}),                
                ({$billingCarrierId}, 'subpack.subscription_offer_slider', '%price% %currency% / {$periodPhrase}', {$engLangId}, {$subPackId}),                
                ({$billingCarrierId}, 'subpack.subscription_offer_buttons', '%price% %currency% / {$periodPhrase}', {$engLangId}, {$subPackId}),                
                ({$billingCarrierId}, 'subpack.subscription_offer_terms', 'This subscription service allows you to download {$creditsPhrase} per {$periodPhrase} on your phone from Playwing catalogue for %price% %currency% (+ WAP connection charges). {$termsPhrase}. As long as you are subscribed to Playwing Premium, you will get access to {$creditsPhrase} each {$periodPhrase}. Downloaded games from Playwing store are for users to keep (not a renting model).', {$engLangId}, {$subPackId})";
    }

    public function getPeriodicityPhrase(int $period): string
    {
        $periodDeclension = $period == 1
            ? 'day'
            : 'days';
        return "{$period} {$periodDeclension}";
    }
}