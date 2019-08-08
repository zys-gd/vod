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
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\Service\LPDataExtractor;
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
            new TwigFunction('getCountries', [$this, 'getCountries']),
            new TwigFunction('getCarrierCountry', [$this, 'getCarrierCountry']),
        ];
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
}