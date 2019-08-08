<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 27-02-19
 * Time: 12:47
 */

namespace App\Twig;


use App\Domain\Entity\Carrier;
use App\Domain\Entity\Country;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Repository\CountryRepository;
use App\Domain\Service\Translator\DataAggregator;
use App\Domain\Service\Translator\ShortcodeReplacer;
use App\Domain\Service\Translator\Translator;
use Doctrine\Common\Collections\ArrayCollection;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\Service\LPDataExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WifiFlowExtension extends AbstractExtension
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var SubscriptionPackRepository
     */
    private $subscriptionPackRepository;
    /**
     * @var CountryRepository
     */
    private $countryRepository;
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var ShortcodeReplacer
     */
    private $shortcodeReplacer;
    /**
     * @var DataAggregator
     */
    private $dataAggregator;
    /**
     * @var LocalExtractor
     */
    private $localExtractor;
    /**
     * @var LPDataExtractor
     */
    private $LPDataExtractor;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * WifiFlowExtension constructor.
     *
     * @param CarrierRepository          $carrierRepository
     * @param SubscriptionPackRepository $subscriptionPackRepository
     * @param CountryRepository          $countryRepository
     * @param Translator                 $translator
     * @param ShortcodeReplacer          $shortcodeReplacer
     * @param DataAggregator             $dataAggregator
     * @param LocalExtractor             $localExtractor
     * @param LPDataExtractor            $LPDataExtractor
     * @param SessionInterface           $session
     */
    public function __construct(
        CarrierRepository $carrierRepository,
        SubscriptionPackRepository $subscriptionPackRepository,
        CountryRepository $countryRepository,
        Translator $translator,
        ShortcodeReplacer $shortcodeReplacer,
        DataAggregator $dataAggregator,
        LocalExtractor $localExtractor,
        LPDataExtractor $LPDataExtractor,
        SessionInterface $session
    )
    {
        $this->carrierRepository = $carrierRepository;
        $this->subscriptionPackRepository = $subscriptionPackRepository;
        $this->countryRepository = $countryRepository;
        $this->translator = $translator;
        $this->shortcodeReplacer = $shortcodeReplacer;
        $this->dataAggregator = $dataAggregator;
        $this->localExtractor = $localExtractor;
        $this->LPDataExtractor = $LPDataExtractor;
        $this->session = $session;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getCountryCarrierList', [$this, 'getCountryCarrierList']),
            new TwigFunction('getCountries', [$this, 'getCountries']),
            new TwigFunction('getCarrierCountry', [$this, 'getCarrierCountry']),
            new TwigFunction('getCarrierOffer', [$this, 'getCarrierOffer'])
        ];
    }

    /**
     * @TODO Remove this method
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountryCarrierList()
    {
        $carrierInterfaces = $this->carrierRepository->findEnabledCarriers();

        /** @var SubscriptionPack[] $subpacks */
        $subpacks = $this->subscriptionPackRepository->findAll();

        $subpackCarriers = [];
        foreach ($subpacks as $subpack) {
            $subpackCarriers[] = $subpack->getCarrier()->getBillingCarrierId();
        }

        $carrierInterfaces = array_filter($carrierInterfaces, function (CarrierInterface $carrier) use ($subpackCarriers
        ) {
            return in_array($carrier->getBillingCarrierId(), $subpackCarriers);
        });


        $countriesCarriers = [];
        /** @var Carrier $carrier */
        foreach ($carrierInterfaces as $carrier) {
            $wifi_offer = $this->translator->translate(
                'wifi.offer',
                $carrier->getBillingCarrierId(),
                $this->localExtractor->getLocal());
            $wifi_button = $this->translator->translate(
                'wifi.button',
                $carrier->getBillingCarrierId(),
                $this->localExtractor->getLocal());

            $carrierData = [
                'uuid' => $carrier->getUuid(),
                'billingCarrierId' => $carrier->getBillingCarrierId(),
                'name' => $carrier->getName(),
                'wifi_offer' => $this->shortcodeReplacer->do(
                    $this->dataAggregator->getGlobalParameters($carrier->getBillingCarrierId()),
                    $wifi_offer
                ),
                'wifi_button' => $this->shortcodeReplacer->do(
                    $this->dataAggregator->getGlobalParameters($carrier->getBillingCarrierId()),
                    $wifi_button
                ),
            ];
            $countriesCarriers[$carrier->getCountryCode()][$carrier->getBillingCarrierId()] = $carrierData;
        }
        $countries = $this->countryRepository->findBy(['countryCode' => array_keys($countriesCarriers)]);
        /** @var Country $country */
        foreach ($countries as $country) {
            $countriesCarriers[$country->getCountryName()] = json_encode($countriesCarriers[$country->getCountryCode()]);
            unset($countriesCarriers[$country->getCountryCode()]);
        }

        return $countriesCarriers;
    }

    public function getCountries()
    {
        $activeCarrierCountries = $this->LPDataExtractor->getActiveCarrierCountries()->map(function(Country $country) {

            return ['code' => $country->getCountryCode(), 'name' => $country->getCountryName()];
        })->toArray();
        return $activeCarrierCountries;
    }

    public function getCarrierCountry()
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
        if(!$billingCarrierId) {
            return;
        }
        $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);

        /** @var Country $country */
        $country = $this->countryRepository->findOneBy(['countryCode' => $carrier->getCountryCode()]);

        $countryCarriers = new ArrayCollection(
            $this->carrierRepository->findBy(['published' => true, 'countryCode' => $carrier->getCountryCode()])
        );

        $resultCountryCarriersMapper = $countryCarriers->map(function (Carrier $carrier) {
            return [
                'id' => $carrier->getBillingCarrierId(),
                'name' => $carrier->getName()
            ];
        })->toArray();

        return ['code' => $country->getCountryCode(), 'name' => $country->getCountryName(), 'countryCarriers' => $resultCountryCarriersMapper];
    }

    public function getCarrierOffer()
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);

        if(!$billingCarrierId) {
            return;
        }

        $wifi_offer = $this->translator->translate(
            'wifi.offer',
            $billingCarrierId,
            $this->localExtractor->getLocal()
        );
        $wifi_offer = $this->shortcodeReplacer->do(
            $this->dataAggregator->getGlobalParameters($billingCarrierId),
            $wifi_offer
        );

        return $wifi_offer;
    }
}