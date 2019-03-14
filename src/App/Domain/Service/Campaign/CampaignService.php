<?php

namespace App\Domain\Service\Campaign;

use App\Domain\Entity\Campaign;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CampaignService
 */
class CampaignService
{
    /**
     *@param Campaign $campaign
     */
    public function generateTestLink($campaign){
        $affiliate = $campaign->getAffiliate();
        $affiliateParams = $affiliate->getInputParamsList();
        $result = '';

        if (isset($affiliateParams) && !empty($affiliateParams)){
            foreach ($affiliateParams as $parameter){
                $result .= "&{$parameter}=testValue";
            }
        }

        $campaign->setTestUrl(
            "/lp?cid={$campaign->getCampaignToken()}&pk_campaign={$affiliate->getUuid()}&pk_kwd={$campaign->getUuid()}$result"
        );
    }

    /**
     * @return array
     */
    public function getCampaignDataFromSession()
    {
        $session = new Session();
        $campaignData = json_decode($session->get('campaignData'), true);

        return empty($campaignData) ? [] : $campaignData;
    }



}