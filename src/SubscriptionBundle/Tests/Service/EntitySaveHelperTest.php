<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 22.05.18
 * Time: 14:02
 */

namespace SubscriptionBundle\Tests\Service;

use Doctrine\ORM\EntityManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Service\EntitySaveHelper;

class EntitySaveHelperTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testPersistAndSaveByCustomEM()
    {
        $defaultEm = \Mockery::spy(EntityManager::class);
        $customEm  = \Mockery::spy(EntityManager::class);
        $helper    = new EntitySaveHelper($defaultEm,\Mockery::spy(LoggerInterface::class));
        $entity    = new \stdClass();

        $helper->persistAndSaveByCustomEM($entity, $customEm);

        $defaultEm->shouldNotHaveReceived('persist');
        $defaultEm->shouldNotHaveReceived('flush');
        $customEm->shouldHaveReceived('persist');
        $customEm->shouldHaveReceived('flush');

    }

    public function testPersistAndSave()
    {

        $defaultEm = \Mockery::spy(EntityManager::class);
        $helper    = new EntitySaveHelper($defaultEm, \Mockery::spy(LoggerInterface::class));
        $entity    = new \stdClass();

        $helper->persistAndSave($entity);

        $defaultEm->shouldHaveReceived('persist');
        $defaultEm->shouldHaveReceived('flush');
    }
}
