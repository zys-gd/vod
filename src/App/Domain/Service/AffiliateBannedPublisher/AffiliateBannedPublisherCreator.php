<?php


namespace App\Domain\Service\AffiliateBannedPublisher;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\AffiliateBannedPublisher;
use Doctrine\ORM\EntityManagerInterface;
use ExtrasBundle\Utils\UuidGenerator;

class AffiliateBannedPublisherCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AffiliateBannedPublisherRemover constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Affiliate $affiliate
     * @param string    $publisherId
     *
     * @throws \Exception
     */
    public function banPublisher(Affiliate $affiliate, string $publisherId)
    {
        $affiliateBannedPublisher = new AffiliateBannedPublisher(UuidGenerator::generate());
        $affiliateBannedPublisher->setAffiliate($affiliate);
        $affiliateBannedPublisher->setPublisherId($publisherId);

        $this->entityManager->persist($affiliateBannedPublisher);
        $this->entityManager->flush();
    }
}