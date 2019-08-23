<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 15:43
 */

use App\Domain\Entity\Carrier;
use ExtrasBundle\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\FakeResponseProvider;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Unsubscribe\OnUnsubscribeUpdater;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeEventChecker;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeEventTracker;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeParametersProvider;
use SubscriptionBundle\Subscription\Unsubscribe\Unsubscriber;

class UnsubscriberTest extends TestCase
{

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;


    /**
     * @var \SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess|\Mockery\MockInterface
     */
    private $unsubscribeProcess;

    /**
     * @var Unsubscriber|\Mockery\MockInterface
     */
    private $unsubscriber;

    /**
     * @var UnsubscribeEventChecker|\Mockery\MockInterface
     */
    private $unsubscribeEventChecker;

    /**
     * @var UnsubscribeEventTracker|\Mockery\MockInterface
     */
    private $unsubscribeEventTracker;

    public function testUnsubscribeForCallbackTrackedCarrier()
    {

        $user = new User(UuidGenerator::generate());
        $user->setIdentifier('ident');
        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setProviderManagedSubscriptions(true);
        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setUser($user);
        $carrier = new Carrier(UuidGenerator::generate());
        $carrier->setBillingCarrierId(0);
        $user->setCarrier($carrier);

        $this->unsubscribeEventChecker->allows([
            'isNeedToBeTracked' => true
        ]);

        $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);
        $this->unsubscriber->trackEventsForUnsubscribe($subscription, Mockery::spy(ProcessResult::class));


        $this->unsubscribeProcess->shouldHaveReceived('doUnsubscribe')->once();
        $this->unsubscribeEventTracker->shouldHaveReceived('trackUnsubscribe')->once();

    }

    public function testUnsubscribeForNonCallbackTrackedCarrier()
    {

        $user = new User(UuidGenerator::generate());
        $user->setIdentifier('ident');
        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setProviderManagedSubscriptions(true);
        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setUser($user);
        $carrier = new Carrier(UuidGenerator::generate());
        $carrier->setBillingCarrierId(0);
        $user->setCarrier($carrier);

        $this->unsubscribeEventChecker->allows([
            'isNeedToBeTracked' => false
        ]);

        $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);
        $this->unsubscriber->trackEventsForUnsubscribe($subscription, Mockery::spy(ProcessResult::class));

        $this->unsubscribeProcess->shouldHaveReceived('doUnsubscribe')->once();
        $this->unsubscribeEventTracker->shouldNotHaveReceived('trackUnsubscribe');

    }

    protected function setUp()
    {

        $this->unsubscribeProcess      = Mockery::spy(UnsubscribeProcess::class);
        $this->unsubscribeEventChecker = Mockery::spy(UnsubscribeEventChecker::class);
        $this->unsubscribeEventTracker = Mockery::spy(UnsubscribeEventTracker::class);

        $this->unsubscriber = new Unsubscriber(
            Mockery::spy(EntitySaveHelper::class),
            Mockery::spy(FakeResponseProvider::class),
            Mockery::spy(Notifier::class),
            $this->unsubscribeProcess,
            Mockery::spy(OnUnsubscribeUpdater::class),
            Mockery::spy(UnsubscribeParametersProvider::class),
            $this->unsubscribeEventChecker,
            $this->unsubscribeEventTracker,
            Mockery::spy(LoggerInterface::class)
        );

    }

}
