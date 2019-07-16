<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 15:43
 */

use App\Domain\Entity\Carrier;
use App\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Piwik\SubscriptionStatisticSender;
use SubscriptionBundle\Service\Action\Common\FakeResponseProvider;
use SubscriptionBundle\Service\Action\Unsubscribe\OnUnsubscribeUpdater;
use SubscriptionBundle\Service\Action\Unsubscribe\Unsubscriber;
use SubscriptionBundle\Service\CarrierTrackingTypeChecker;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\Notification\Notifier;

class UnsubscriberTest extends TestCase
{

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;


    /**
     * @var SubscriptionStatisticSender|\Mockery\MockInterface
     */
    private $piwikSender;


    /**
     * @var CarrierTrackingTypeChecker|\Mockery\MockInterface
     */
    private $carrierTrackingTypeChecker;

    /**
     * @var \SubscriptionBundle\BillingFramework\Process\UnsubscribeProcess|\Mockery\MockInterface
     */
    private $unsubscribeProcess;

    /**
     * @var Unsubscriber|\Mockery\MockInterface
     */
    private $unsubscriber;

    public function testUnsubscribeForCallbackTrackedCarrier()
    {

        $user     = new User(UuidGenerator::generate());
        $user->setIdentifier('ident');
        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setProviderManagedSubscriptions(true);
        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setUser($user);
        $carrier = new Carrier(UuidGenerator::generate());
        $carrier->setBillingCarrierId(0);
        $user->setCarrier($carrier);

        $this->carrierTrackingTypeChecker->allows([
            'isShouldBeTrackedOnCallbackForUnsubscribe' => true
        ]);

        $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);

        $this->unsubscribeProcess->shouldHaveReceived('doUnsubscribe')->once();

    }

    public function testUnsubscribeForNonCallbackTrackedCarrier()
    {

        $user     = new User(UuidGenerator::generate());
        $user->setIdentifier('ident');
        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setProviderManagedSubscriptions(true);
        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setUser($user);
        $carrier = new Carrier(UuidGenerator::generate());
        $carrier->setBillingCarrierId(0);
        $user->setCarrier($carrier);

        $this->carrierTrackingTypeChecker->allows([
            'isShouldBeTrackedOnCallback' => false
        ]);

        $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);

        $this->unsubscribeProcess->shouldHaveReceived('doUnsubscribe')->once();

    }

    protected function setUp()
    {

        $this->piwikSender                = Mockery::spy(SubscriptionStatisticSender::class);
        $this->unsubscribeProcess         = Mockery::spy(UnsubscribeProcess::class);
        $this->carrierTrackingTypeChecker = Mockery::spy(CarrierTrackingTypeChecker::class);


        $this->unsubscriber = new Unsubscriber(
            Mockery::spy(LoggerInterface::class),
            Mockery::spy(EntitySaveHelper::class),
            Mockery::spy(FakeResponseProvider::class),
            Mockery::spy(Notifier::class),
            $this->unsubscribeProcess,
            Mockery::spy(OnUnsubscribeUpdater::class),
            $this->piwikSender,
            Mockery::spy(\SubscriptionBundle\Service\Action\Unsubscribe\UnsubscribeParametersProvider::class),
            Mockery::spy(\Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector::class)
        );

    }

}
