<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.08.19
 * Time: 11:47
 */

namespace SubscriptionBundle\Piwik\Service;


use SubscriptionBundle\Entity\Subscription;

class AdditionalDataProvider
{
    /**
     * @param Subscription $subscription
     * @param string|null  $bfProvider
     * @param bool|null    $conversionMode
     *
     * @return array
     */
    public function getAdditionalData(
        Subscription $subscription,
        string $bfProvider = null,
        bool $conversionMode = null
    ): array
    {
        $oSubPack = $subscription->getSubscriptionPack();

        $additionData = [
            'currency' => $oSubPack->getFinalCurrency(),
            'provider' => $bfProvider
        ];

        if ($conversionMode) {
            $additionData['conversion_mode'] = $conversionMode;
        }

        return $additionData;
    }
}