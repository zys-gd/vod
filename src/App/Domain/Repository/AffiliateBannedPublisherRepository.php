<?php


namespace App\Domain\Repository;


use App\Domain\Entity\AffiliateBannedPublisher;
use App\Utils\UuidGenerator;
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

    /**
     * @param $affiliateId
     * @param $publisherId
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function banPublisher($affiliateId, $publisherId)
    {
        $em = $this->getEntityManager();

        $affiliate = $em->getRepository('App\Domain\Entity\Affiliate')
            ->createQueryBuilder('af')
            ->where('af.uuid = :affiliate')
            ->setParameter('affiliate', $affiliateId)
            ->getQuery()->getSingleResult();

        $affiliateBannedPublisher = new AffiliateBannedPublisher(UuidGenerator::generate());
        $affiliateBannedPublisher->setAffiliate($affiliate);
        $affiliateBannedPublisher->setPublisherId($publisherId);

        $em->persist($affiliateBannedPublisher);
        $em->flush();
    }

    /**
     * @param string $affiliateId
     * @param string $publisherId
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function unbanPublisher(string $affiliateId, string $publisherId)
    {
        $em = $this->getEntityManager();

        $affiliateBannedPublisher = $this->findOneBy(['affiliate' => $affiliateId, 'publisherId' => $publisherId]);
        $em->remove($affiliateBannedPublisher);
        $em->flush();
    }
}