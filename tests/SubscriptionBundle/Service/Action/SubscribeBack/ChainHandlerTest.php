<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.05.18
 * Time: 11:04
 */

namespace SubscriptionBundle\Service\Action\SubscribeBack;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SubscriptionBundle\Service\Action\SubscribeBack\AbstractSubscribeBackHandler;
use SubscriptionBundle\Service\Action\SubscribeBack\SubscribeBackHandlerProvider;

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
