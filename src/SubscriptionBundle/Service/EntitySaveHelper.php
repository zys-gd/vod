<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.18
 * Time: 11:41
 */

namespace SubscriptionBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class EntitySaveHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $defaultEntityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EntitySaveHelper constructor.
     * @param EntityManagerInterface $defaultEntityManager
     */
    public function __construct(EntityManagerInterface $defaultEntityManager, LoggerInterface $logger)
    {
        $this->defaultEntityManager = $defaultEntityManager;
        $this->logger               = $logger;
    }

    public function saveAll()
    {
        $this->logger->debug('Saving all');
        $this->defaultEntityManager->flush();
    }

    /**
     * Persist and flush
     * @param $entity
     */
    public function persistAndSave($entity)
    {
        $this->logger->debug('Persisting and saving entity', ['class' => get_class($entity)]);

        $this->defaultEntityManager->persist($entity);
        $this->defaultEntityManager->flush();
    }


    public function persistAndSaveByCustomEM($entity, EntityManagerInterface $entityManager)
    {
        $entityManager->persist($entity);
        $entityManager->flush();
    }


}