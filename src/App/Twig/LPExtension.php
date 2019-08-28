<?php


namespace App\Twig;


use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use CommonDataBundle\Entity\Country;
use CommonDataBundle\Repository\Interfaces\CountryRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\WifiIdentification\WifiPhoneOptionsProvider;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LPExtension extends AbstractExtension
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;
    /**
     * @var string
     */
    private $imageBaseUrl;
    /**
     * @var WifiPhoneOptionsProvider
     */
    private $wifiPhoneOptionsProvider;
    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * LPExtension constructor.
     * @param SessionInterface            $session
     * @param TemplateConfigurator        $templateConfigurator
     * @param CarrierRepositoryInterface  $carrierRepository
     * @param CampaignRepositoryInterface $campaignRepository
     * @param CountryRepositoryInterface  $countryRepository
     * @param WifiPhoneOptionsProvider    $wifiPhoneOptionsProvider
     * @param string                      $imageBaseUrl
     */
    public function __construct(
        SessionInterface $session,
        TemplateConfigurator $templateConfigurator,
        CarrierRepositoryInterface $carrierRepository,
        CampaignRepositoryInterface $campaignRepository,
        CountryRepositoryInterface $countryRepository,
        WifiPhoneOptionsProvider $wifiPhoneOptionsProvider,
        string $imageBaseUrl
    )
    {
        $this->session = $session;
        $this->templateConfigurator = $templateConfigurator;
        $this->carrierRepository = $carrierRepository;
        $this->campaignRepository = $campaignRepository;
        $this->wifiPhoneOptionsProvider = $wifiPhoneOptionsProvider;
        $this->imageBaseUrl = $imageBaseUrl;
        $this->countryRepository = $countryRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getCountries', [$this, 'getCountries']),
            new TwigFunction('getCarrierCountry', [$this, 'getCarrierCountry']),
            new TwigFunction('isClickableSubImage', [$this, 'isClickableSubImage']),
            new TwigFunction('LPImporter', [$this, 'getLPImportPath']),
            new TwigFunction('getCampaignData', [$this, 'getCampaignData']),
            new TwigFunction('getPhoneValidationOptions', [$this, 'getPhoneValidationOptions']),
            new TwigFunction('getPinValidationOptions', [$this, 'getPinValidationOptions'])
        ];
    }

    public function getCountries()
    {
        $activeCarrierCountries = new ArrayCollection($this->carrierRepository->findEnabledCarriersCountries());

        /** @var Country $country */
        $activeCarrierCountries = $activeCarrierCountries->map(function ($country) {
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


    /**
     * is_clickable_sub_image has default value - true
     *
     * @return bool
     */
    public function isClickableSubImage(): bool
    {
        $campaignToken = AffiliateVisitSaver::extractCampaignToken($this->session);
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
        if(!empty($billingCarrierId)) {
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            //1.Highest priority in carrier that has value not equal the default
            if($carrier && !$carrier->isClickableSubImage()) {
                return false;
            }

            /** @var Campaign $campaign */
            $campaign = $campaignToken ? $this->campaignRepository->findOneByCampaignToken($campaignToken) : null;
            //2.Next if the campaign has value not equal the default
            if($campaign && !$campaign->isClickableSubImage()) {
                return false;
            }
        }
        return true;
    }

    public function getLPImportPath(string $baseFilePath): string
    {
        $billingCarrierId = (int)IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
        $baseFilePath = str_replace('.html.twig', '', $baseFilePath);
        return $this->templateConfigurator->getTemplate($baseFilePath, $billingCarrierId);
    }

    /**
     * @param string $key
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getCampaignData(string $key)
    {
        $campaignData = $this->extractCampaignData();

        if (!array_key_exists($key, $campaignData)) {
            throw new \InvalidArgumentException('Wrong parameter');
        }
        return $campaignData[$key];
    }

    public function getPhoneValidationOptions()
    {
        $billingCarrierId = (int)IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
        $phoneValidationOptions = $this->wifiPhoneOptionsProvider->getPhoneValidationOptions($billingCarrierId);

        return [
            'placeholder' => $phoneValidationOptions->getPhonePlaceholder(),
            'pattern' => $phoneValidationOptions->getPhoneRegexPattern()
        ];
    }

    public function getPinValidationOptions()
    {
        $billingCarrierId = (int)IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
        $phoneValidationOptions = $this->wifiPhoneOptionsProvider->getPhoneValidationOptions($billingCarrierId);

        return [
            'placeholder' => $phoneValidationOptions->getPinPlaceholder(),
            'pattern' => $phoneValidationOptions->getPinRegexPattern()
        ];
    }

    private function extractCampaignData()
    {
        $campaignBanner = null;
        $background = null;

        $cid = $this->session->get('campaign_id', '');
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->findOneBy([
            'campaignToken' => $cid,
            'isPause' => false
        ]);
        if ($campaign) {
            $campaignBanner = $this->imageBaseUrl . '/' . $campaign->getImagePath();
            $background = $campaign->getBgColor();
        }

        return [
            'campaign_banner' => $campaignBanner,
            'background' => $background
        ];
    }
}