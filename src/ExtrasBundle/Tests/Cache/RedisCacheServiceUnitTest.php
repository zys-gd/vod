<?php
/**
 * Created by PhpStorm.
 * User: Anton
 * Date: 04.04.2018
 * Time: 14:58
 */

namespace ExtrasBundle\Tests;

use ExtrasBundle\Cache\Redis\RedisCacheService;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;

class RedisCacheServiceUnitTest extends TestCase
{
    public function testGetKeyReturnsCacheItemInterfaceInstance()
    {
        $adapterMock = $this->createMock(RedisAdapter::class);
        $cacheItem = new CacheItem();
        $adapterMock->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem);

        $service = new RedisCacheService($adapterMock);
        $this->assertInstanceOf(CacheItemInterface::class, $service->getKey('test'));
    }

    public function testSaveCache()
    {
        $adapterMock = $this->createMock(RedisAdapter::class);
        $cacheItem = new CacheItem();
        $adapterMock->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem);
        $adapterMock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $service = new RedisCacheService($adapterMock);

        $service->saveCache('test_key', 'test_val', 1);
    }

    public function testDeleteCache()
    {
        $adapterMock = $this->createMock(RedisAdapter::class);
        $adapterMock->expects($this->once())
            ->method('clear')
            ->willReturn(true);

        $service = new RedisCacheService($adapterMock);

        $service->deleteCache();
    }

    public function testHasCacheReturnTrue()
    {
        $adapterMock = $this->createMock(RedisAdapter::class);
        $adapterMock->expects($this->once())
            ->method('hasItem')
            ->willReturn(true);

        $service = new RedisCacheService($adapterMock);

        $this->assertTrue($service->hasCache('test'));
    }

    public function testHasCacheReturnFalse()
    {
        $adapterMock = $this->createMock(RedisAdapter::class);
        $adapterMock->expects($this->once())
            ->method('hasItem')
            ->willReturn(false);

        $service = new RedisCacheService($adapterMock);

        $this->assertFalse($service->hasCache('test'));
    }

    public function testGetValueIfValueExists()
    {
        $adapterMock = $this->createMock(RedisAdapter::class);
        $adapterMock->expects($this->once())
            ->method('hasItem')
            ->willReturn(true);
        $cacheItem = new CacheItem();
        $cacheItem->set('test_value');
        $adapterMock->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem);

        $service = new RedisCacheService($adapterMock);

        $this->assertEquals($service->getValue('test_key'), 'test_value');
    }

    public function testGetValueIfValueDoNotExists()
    {
        $adapterMock = $this->createMock(RedisAdapter::class);
        $adapterMock->expects($this->once())
            ->method('hasItem')
            ->willReturn(false);

        $service = new RedisCacheService($adapterMock);

        $this->assertNull($service->getValue('test_key'));
    }
}
