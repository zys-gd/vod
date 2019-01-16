<?php

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\RenewProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Renew\OnRenewUpdater;
use SubscriptionBundle\Service\EntitySaveHelper;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.07.18
 * Time: 16:03
 */
class RenewerTest extends \PHPUnit\Framework\TestCase
{

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var \SubscriptionBundle\Service\Action\Renew\Renewer
     */
    private $renewer;

    /**
     * @var \SubscriptionBundle\BillingFramework\Process\RenewProcess|\Mockery\MockInterface
     */
    private $renewProcess;


    public function testRenew()
    {

        $subscription = new Subscription(\App\Utils\UuidGenerator::generate());

        $this->renewProcess->allows(['doRenew' => new ProcessResult()]);
        $this->renewer->renew($subscription);
        $this->renewProcess->shouldHaveReceived('doRenew')->once();

    }

    /**
     *
     */
    protected function setUp()
    {

        $this->renewProcess = Mockery::spy(RenewProcess::class);
        $this->renewer      = new \SubscriptionBundle\Service\Action\Renew\Renewer(
            Mockery::spy(LoggerInterface::class),
            Mockery::spy(EventDispatcherInterface::class),
            Mockery::spy(EntitySaveHelper::class),
            $this->renewProcess,
            Mockery::spy(OnRenewUpdater::class),
            Mockery::spy(\SubscriptionBundle\Service\Action\Renew\RenewParametersProvider::class)

        );

    }
}