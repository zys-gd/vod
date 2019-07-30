<?php


namespace App\Domain\Repository;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\AffiliateBannedPublisher;
use Doctrine\ORM\EntityRepository;
use ExtrasBundle\Utils\UuidGenerator;
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