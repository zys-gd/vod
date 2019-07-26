<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 15-02-19
 * Time: 12:52
 */

namespace App\Domain\Service\Translator;

use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use ExtrasBundle\Cache\ArrayCache\ArrayCacheService;
use ExtrasBundle\Utils\LocalExtractor;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;

class DataAggregator
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var LocalExtractor
     */
    private $localExtractor;
    /**
     * @var ArrayCacheService
     */
    private $arrayCacheService;
    /**
     * @var \SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider
     */
    private $subscriptionPackProvider;

    /**
     * DataAggregator constructor
     *
     * @param CarrierRepository                                             $carrierRepository
     * @param Translator                                                    $translator
     * @param LocalExtractor                                                $localExtractor
     * @param ArrayCacheService                                             $arrayCacheService
     * @param \SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider $subscriptionPackProvider
     */
    public function __construct(
        CarrierRepository $carrierRepository,
        Translator $translator,
        LocalExtractor $localExtractor,
        ArrayCacheService $arrayCacheService,
        SubscriptionPackProvider $subscriptionPackProvider
    )
    {
        $this->carrierRepository        = $carrierRepository;
        $this->translator               = $translator;
        $this->localExtractor           = $localExtractor;
        $this->arrayCacheService        = $arrayCacheService;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
    }

    /**
     * @param int $billingCarrierId
     *
     * @return array
     */
    public function getGlobalParameters(int $billingCarrierId): array
    {

        $carrier          = $this->getPreparedCarrier($billingCarrierId);
        $subscriptionPack = $this->getPreparedSubscriptionPack($carrier);
        $languageCode     = $this->localExtractor->getLocal();

        return [
            '%price%'       => $subscriptionPack->getTierPrice(),
            '%currency%'    => $subscriptionPack->getFinalCurrency(),
            '%credits%'     => $subscriptionPack->getCredits(),
            '%period%'      => $this
                ->translator
                ->translate('period.' . $subscriptionPack->convertPeriod2Text(), $billingCarrierId, $languageCode),
            '%periodicity%' => $subscriptionPack->convertPeriodicity2Text(),
            '%country%'     => $subscriptionPack->getCountry()->getCountryName()
        ];
    }

    private function getPreparedCarrier(int $billingCarrierId): Carrier
    {
        $key = sprintf('%s_%s', $billingCarrierId, 'carrier');

        if ($this->arrayCacheService->hasCache($key)) {
            $carrier = $this->arrayCacheService->getValue($key);
        } else {
            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);;
            $this->arrayCacheService->saveCache($key, $carrier, 96000);
        }
        return $carrier;
    }

    private function getPreparedSubscriptionPack(Carrier $carrier): SubscriptionPack
    {
        $key = sprintf('%s_%s', $carrier->getBillingCarrierId(), 'subscription_pack');

        if ($this->arrayCacheService->hasCache($key)) {
            $pack = $this->arrayCacheService->getValue($key);
        } else {
            $pack = $this->subscriptionPackProvider->getActiveSubscriptionPackFromCarrier($carrier);;
            $this->arrayCacheService->saveCache($key, $pack, 96000);
        }
        return $pack;
    }
}