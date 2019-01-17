<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 10:55
 */

namespace SubscriptionBundle\Blacklist;


use App\Domain\Entity\BlackList;
use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\User;

class BlacklistSaver
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BlacklistSaver constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param User $billableUser
     * @throws \Exception
     */
    public function addUserToBlackList(User $billableUser): void
    {
        $blackList = new BlackList(UuidGenerator::generate());
        $blackList->setAlias($billableUser->getIdentifier());
        $blackList->setBillingCarrierId($billableUser->getBillingCarrierId());
        $blackList->setIsBlockedManually(false);

        $this->entityManager->persist($blackList);
        $this->entityManager->flush();
    }
}