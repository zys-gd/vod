<?php


namespace App\Domain\Repository;


use App\Domain\Entity\AffiliateBannedPublisher;
use Doctrine\ORM\EntityRepository;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;

class AffiliateBannedPublisherRepository extends EntityRepository
{
    /**
     * @param AffiliateInterface $affiliate
     * @param string             $publisherId
     *
     * @return AffiliateBannedPublisher|null
     */
    public function findBannedPublisher(AffiliateInterface $affiliate, string $publisherId): ?AffiliateBannedPublisher
    {
        return $this->findOneBy(['affiliate' => $affiliate, 'publisherId' => $publisherId]);
    }
}