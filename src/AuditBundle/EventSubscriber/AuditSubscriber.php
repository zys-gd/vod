<?php

namespace AuditBundle\EventSubscriber;

use DataDog\AuditBundle\EventSubscriber\AuditSubscriber as BaseSubscriber;
use Doctrine\ORM\EntityManager;

class AuditSubscriber extends BaseSubscriber
{

    /**
     * @param EntityManager $em
     * @param $entity
     * @param array $ch
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function update(EntityManager $em, $entity, array $ch)
    {
        if (!$this->blame($em)) {
            return;
        }
        parent::update($em, $entity, $ch);
    }

    /**
     * @param EntityManager $em
     * @param $entity
     * @param $id
     */
    protected function remove(EntityManager $em, $entity, $id)
    {
        if (!$this->blame($em)) {
            return;
        }
        parent::remove($em, $entity, $id);
    }

    /**
     * @param EntityManager $em
     * @param $entity
     * @param array $ch
     */
    protected function insert(EntityManager $em, $entity, array $ch)
    {
        if (!$this->blame($em)) {
            return;
        }
        parent::insert($em, $entity, $ch);
    }
}
