<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 15-02-19
 * Time: 12:52
 */

namespace App\Domain\Service\Translator;

use App\Domain\Repository\CarrierRepository;
use ExtrasBundle\Utils\LocalExtractor;

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
     * DataAggregator constructor
     *
     * @param CarrierRepository $carrierRepository
     * @param Translator $translator
     * @param LocalExtractor $localExtractor
     */
    public function __construct(CarrierRepository $carrierRepository,Translator $translator, LocalExtractor $localExtractor)
    {
        $this->carrierRepository = $carrierRepository;
        $this->translator = $translator;
        $this->localExtractor = $localExtractor;
    }

    /**
     * @param int $billingCarrierId
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getGlobalParameters(int $billingCarrierId): array
    {
        $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
        $subscriptionPack = $this->carrierRepository->findActiveSubscriptionPack($carrier);
        $languageCode = $this->localExtractor->getLocal();

        return [
            '%price%' => $subscriptionPack->getTierPrice(),
            '%currency%' => $subscriptionPack->getFinalCurrency(),
            '%credits%' => $subscriptionPack->getCredits(),
            '%period%' => $this
                ->translator
                ->translate('period.' . $subscriptionPack->convertPeriod2Text(), $billingCarrierId, $languageCode),
            '%periodicity%' => $subscriptionPack->convertPeriodicity2Text(),
            '%country%' => $subscriptionPack->getCountry()->getCountryName()
        ];
    }
}