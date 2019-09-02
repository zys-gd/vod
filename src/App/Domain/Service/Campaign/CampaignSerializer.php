<?php

namespace App\Domain\Service\Campaign;

use SubscriptionBundle\Entity\Affiliate\CampaignInterface;

/**
 * Class CampaignSerializer
 */
class CampaignSerializer
{
    /**
     * @var string
     */
    private $imageUrl;

    /**
     * CampaignSerializer constructor.
     *
     * @param string $imageUrl
     */
    public function __construct(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @param CampaignInterface $campaign
     *
     * @return array
     */
    public function serialize(CampaignInterface $campaign): array
    {
        return [
            'category' => $campaign->getMainCategory(),
            'banner' => $this->imageUrl . '/' . $campaign->getImagePath(),
            'background' => $campaign->getBgColor()
        ];
    }
}