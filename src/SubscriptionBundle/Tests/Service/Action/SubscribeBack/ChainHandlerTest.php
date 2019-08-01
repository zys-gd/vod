<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.05.18
 * Time: 11:04
 */

namespace SubscriptionBundle\Subscription\SubscribeBack;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SubscriptionBundle\Subscription\SubscribeBack\AbstractSubscribeBackHandler;
use SubscriptionBundle\Subscription\SubscribeBack\SubscribeBackHandlerProvider;

class ChainHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testConstructor()
    {
        new SubscribeBackHandlerProvider(
            \Mockery::mock(AbstractSubscribeBackHandler::class)
        );
        $this->assertTrue(true, 'does not accepted valid class');

        try {
            new SubscribeBackHandlerProvider(
                \Mockery::mock(\stdClass::class)
            );
            $this->assertTrue(false, 'exception has not been thrown');
        } catch (\InvalidArgumentException $exception) {
            $this->assertTrue(true);
        }

    }
}
