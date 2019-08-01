<?php


namespace App\Domain\Service\AffiliateBannedPublisher;


use App\Domain\Entity\AffiliateBannedPublisher;
use Doctrine\ORM\EntityManagerInterface;

class AffiliateBannedPublisherRemover
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
     * @param AffiliateBannedPublisher $affiliateBannedPublisher
     */
    public function unbanPublisher(AffiliateBannedPublisher $affiliateBannedPublisher)
    {
        $this->entityManager->remove($affiliateBannedPublisher);
        $this->entityManager->flush();
    }
}