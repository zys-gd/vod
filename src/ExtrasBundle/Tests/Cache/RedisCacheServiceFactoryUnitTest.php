<?php
/**
 * Created by PhpStorm.
 * User: Anton
 * Date: 02.04.2018
 * Time: 12:14
 */

namespace ExtrasBundle\Tests;

use ExtrasBundle\Cache\ICacheService;
use ExtrasBundle\Cache\Redis\RedisCacheServiceFactory;
use ExtrasBundle\Cache\Redis\RedisConnectionProvider;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Redis;

class RedisCacheServiceFactoryUnitTest extends TestCase
{
    use MockeryPHPUnitIntegration;


    public function testCreateCacheServices()
    {
        $provider = \Mockery::mock(RedisConnectionProvider::class);

        $redis = \Mockery::spy(Redis::class);

        $provider->allows(['create' => $redis]);

        $factory = new RedisCacheServiceFactory($provider, 'default');

        $this->assertInstanceOf(ICacheService::class, $factory->createUserSubscriptionCacheService());
        $this->assertInstanceOf(ICacheService::class, $factory->createTranslationCacheService());
    }
}
