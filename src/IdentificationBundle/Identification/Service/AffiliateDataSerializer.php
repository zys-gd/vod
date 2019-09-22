<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.09.19
 * Time: 16:33
 */

namespace IdentificationBundle\Identification\Service;


use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AffiliateDataSerializer
{
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;


    /**
     * AffiliateDataSerializer constructor.
     * @param CampaignExtractor $campaignExtractor
     */
    public function __construct(CampaignExtractor $campaignExtractor)
    {
        $this->campaignExtractor = $campaignExtractor;
    }


    public function serialize(SessionInterface $session): array
    {
        return [
            'aff_data' => AffiliateVisitSaver::extractPageVisitData($session)
        ];
    }
}