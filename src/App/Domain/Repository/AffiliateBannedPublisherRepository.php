<?php


namespace App\Domain\Repository;


use App\Domain\Entity\AffiliateBannedPublisher;
use App\Domain\Entity\Carrier;
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
    public function findTotallyBannedPublisher(AffiliateInterface $affiliate, string $publisherId): ?AffiliateBannedPublisher
    {
        return $this->findOneBy([
            'carrier' => null,
            'affiliate' => $affiliate,
            'publisherId' => $publisherId,
        ]);
    }

    /**
     * @param AffiliateInterface $affiliate
     * @param string             $publisherId
     * @param Carrier            $carrier
     *
     * @return AffiliateBannedPublisher|null
     */
    public function findBannedPublisher4Carrier(AffiliateInterface $affiliate, string $publisherId, Carrier $carrier): ?AffiliateBannedPublisher
    {
        return $this->findOneBy([
            'carrier' => $carrier,
            'affiliate' => $affiliate,
            'publisherId' => $publisherId,
        ]);
    }

    /**
     * @param $affiliate
     *
     * @return array|null
     */
    public function findBannedPublishersAsArray($affiliate): ?array
    {
        $queryBuilder = $this->createQueryBuilder('abp');

        $query = $queryBuilder
            ->select()
            ->where('abp.affiliate = :affiliate')
            ->setParameter('affiliate', $affiliate)
            ->getQuery();

        return $query->getArrayResult();
    }
}