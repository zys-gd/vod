<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 13-11-18
 * Time: 13:19
 */

namespace SubscriptionBundle\Service\SubscriptionText\Periodicity;


interface IPeriodicity
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
    public function getInsertValues(int $billingCarrierId, int $subPackId, int $engLangId, string $creditsPhrase, string $termsPhrase, int $period): string;

    public function getPeriodicityPhrase(int $period):string;
}