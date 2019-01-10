<?php

namespace SubscriptionBundle\Service\SubscriptionText;


use SubscriptionBundle\Service\SubscriptionText\Periodicity\IPeriodicity;

class QueryManager
{
    /**
     * @var DeclensionHelper
     */
    private $declensionHelper;

    /**
     * QueryManager constructor.
     * @param DeclensionHelper $declensionHelper
     */
    public function __construct(DeclensionHelper $declensionHelper)
    {
        $this->declensionHelper = $declensionHelper;
    }

    /**
     * @param int          $billingCarrierId
     * @param int          $subPackId
     * @param int          $engLangId
     * @param IPeriodicity $oPeriodicity
     * @param bool         $isTrial
     * @param              $period
     * @param              $credits
     * @return string
     */
    public function buildQuery(
        int $billingCarrierId,
        int $subPackId,
        int $engLangId,
        IPeriodicity $oPeriodicity,
        bool $isTrial,
        $period,
        $credits
    )
    {
        $creditsPhrase      = $this->getCreditsPhrase($credits);
        $termsPhrase        = $this->getTermsTrialPhrase($isTrial, $oPeriodicity, $period);
        $insertValues       = $oPeriodicity->getInsertValues($billingCarrierId, $subPackId, $engLangId, $creditsPhrase, $termsPhrase, $period);
        $commonInsertValues = $this->getCommonInsertValues($billingCarrierId, $subPackId, $engLangId, $isTrial);
        return "{$this->getHeader()} {$insertValues}, {$commonInsertValues};";
    }

    /**
     * @return string
     */
    private function getHeader(): string
    {
        return "INSERT INTO `placeholder_to_operator` (`carrier_id`, `placeholder_id`, `specific_value`, `language_id`, `subscription_pack_id`) VALUES ";
    }

    /**
     * @param int $credits
     * @return string
     */
    private function getCreditsPhrase(int $credits)
    {
        $creditDeclension = $this->declensionHelper->getCreditsDeclension($credits);
        return $credits == 1000 ? "unlimited games" : "%credits% {$creditDeclension}";
    }

    /**
     * @param bool         $isTrial
     * @param IPeriodicity $oPeriodicity
     * @param int          $period
     * @return string
     */
    private function getTermsTrialPhrase(bool $isTrial, IPeriodicity $oPeriodicity, int $period): string
    {
        $periodicity = $oPeriodicity->getPeriodicityPhrase($period);

        return $isTrial ? "New subscribers will enjoy a free trial period: 1st {$periodicity} for free. Promotional offer valid one time only." : "";
    }

    /**
     * For adding new common text state
     * (think about commas!)
     *
     * @param int  $billingCarrierId
     * @param int  $subPackId
     * @param int  $engLangId
     * @param bool $isTrial
     * @return string
     */
    private function getCommonInsertValues(int $billingCarrierId, int $subPackId, int $engLangId, bool $isTrial)
    {
        $commonInsertValues = "";

        $commonInsertValues .= "({$billingCarrierId}, 'subpack.subscription_offer_slider_unsubscribed', '', {$engLangId}, {$subPackId}),";
        $commonInsertValues .= "({$billingCarrierId}, 'subpack.subscription_offer_buttons_unsubscribed', '', {$engLangId}, {$subPackId})";

        if ($isTrial) {
            $commonInsertValues .= ",({$billingCarrierId}, 'subpack.subscription_home_promotional_1', '', {$engLangId}, {$subPackId}),
                ({$billingCarrierId}, 'subpack.subscription_club_promotional_1', '', {$engLangId}, {$subPackId}),
                ({$billingCarrierId}, 'subpack.subscription_club_header_promotional_1', '', {$engLangId}, {$subPackId}),
                ({$billingCarrierId}, 'subpack.subscription_club_header_promotional_1_unsubscribed', '', {$engLangId}, {$subPackId}),
                ({$billingCarrierId}, 'subpack.subscription_landing_promotional_1', '', {$engLangId}, {$subPackId})";
        }
        return $commonInsertValues;
    }
}