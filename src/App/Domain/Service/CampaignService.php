<?php

namespace App\Domain\Service;

use App\Domain\Entity\Campaign;

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
}