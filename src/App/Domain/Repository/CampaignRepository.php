<?php

namespace App\Domain\Repository;

use Doctrine\ORM\EntityRepository;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;

class CampaignRepository extends EntityRepository implements CampaignRepositoryInterface
{
    public function findOneByCampaignToken(string $token): ?CampaignInterface
    {
        return $this->findOneBy(['campaignToken' => $token]);
    }
}