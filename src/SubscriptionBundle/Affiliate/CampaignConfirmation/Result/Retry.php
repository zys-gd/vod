<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 18:01
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Result;


class Retry extends AbstractResult
{
    public function getResultId()
    {
        return 'retry';
    }
}