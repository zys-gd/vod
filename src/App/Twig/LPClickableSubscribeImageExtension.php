<?php


namespace App\Twig;


use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LPClickableSubscribeImageExtension extends AbstractExtension
{

    /** @var SessionInterface */
    private $session;

    /** @var CarrierRepositoryInterface */
    private $carrierRepository;

    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * LPClickableSubscribeImageExtension constructor.
     * @param SessionInterface            $session
     * @param CarrierRepositoryInterface  $carrierRepository
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        SessionInterface $session,
        CarrierRepositoryInterface $carrierRepository,
        CampaignRepositoryInterface $campaignRepository
    )
    {
        $this->session = $session;
        $this->carrierRepository = $carrierRepository;
        $this->campaignRepository = $campaignRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('isClickableSubImage', [$this, 'isClickableSubImage'])
        ];
    }

    /**
     * is_clickable_sub_image has default value - true
     *
     * @return bool
     */
    public function isClickableSubImage(): bool
    {
        $campaignToken = AffiliateVisitSaver::extractCampaignToken($this->session);
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        if(isset($ispDetectionData['carrier_id']) && !empty($ispDetectionData['carrier_id'])) {
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);
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
}