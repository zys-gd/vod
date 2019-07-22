<?php


namespace App\Twig;


use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LPCampaignDataExtension extends AbstractExtension
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;
    /**
     * @var string
     */
    private $imageBaseUrl;

    /**
     * LPCampaignDataExtension constructor.
     * @param SessionInterface   $session
     * @param string             $imageBaseUrl
     * @param CampaignRepository $campaignRepository
     */
    public function __construct(
        SessionInterface $session,
        CampaignRepository $campaignRepository,
        string $imageBaseUrl
    )
    {
        $this->session = $session;
        $this->campaignRepository = $campaignRepository;
        $this->imageBaseUrl = $imageBaseUrl;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getCampaignData', [$this, 'getCampaignData'])
        ];
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