<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 17:57
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Result;


class Failure extends AbstractResult
{

    public function getResultId()
    {
        return 'failure';
    }
}