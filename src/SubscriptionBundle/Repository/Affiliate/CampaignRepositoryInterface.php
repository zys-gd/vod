<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 14:22
 */

namespace SubscriptionBundle\Repository\Affiliate;


use SubscriptionBundle\Entity\Affiliate\CampaignInterface;

interface CampaignRepositoryInterface
{

    public function findOneByCampaignToken(string $cid): ?CampaignInterface;
}