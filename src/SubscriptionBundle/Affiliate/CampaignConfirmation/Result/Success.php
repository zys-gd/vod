<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 17:58
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Result;


class Success extends AbstractResult
{

    public function getResultId()
    {
        return 'success';
    }
}