<?php

namespace App\Admin\Sonata\Traits;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class InitDoctrine
 * @package AppBundle\Admin\Traits
 */
trait InitDoctrine
{
    /**
     * @var EntityManager
     */
    protected $em = null;

    /**
     * Init the Doctrine Entity Manager based on $container container.
     *
     * @param ContainerInterface $container
     * @return void
     */
    protected function initDoctrine(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
     * Should be called to make a check whether the Entity manager was init'ed
     * successfully or not.
     *
     * @throws \Exception
     * @return void
     */
    protected function isInitEm()
    {
        if (!$this->em instanceof EntityManager) {
            throw new \Exception('An error has occurred. The EntityManager failed to init.');
        }
    }
}