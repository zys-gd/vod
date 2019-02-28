<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 15-02-19
 * Time: 12:52
 */

namespace App\Domain\Service\Translator;

use App\Domain\Repository\CarrierRepository;

class DataAggregator
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    public function __construct(CarrierRepository $carrierRepository)
    {
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * @param int $billingCarrierId
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getGlobalParameters(int $billingCarrierId): array
    {
        $subscriptionPack = $this->carrierRepository->findActiveSubscriptionPack($billingCarrierId);
        return [
            '%price%' => $subscriptionPack->getPriceFromTier(),
            '%currency%' => $subscriptionPack->getCurrencyFromTier(),
            '%credits%' => $subscriptionPack->getCredits(),
            '%period%' => $subscriptionPack->convertPeriodicity2Text(),
            '%country%' => $subscriptionPack->getCountry()->getCountryName()
        ];
    }
}