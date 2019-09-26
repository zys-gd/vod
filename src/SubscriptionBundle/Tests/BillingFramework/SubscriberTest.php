<?php

use ExtrasBundle\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use Playwing\CrossSubscriptionAPIBundle\Connector\ApiConnector;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitCompleter;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Piwik\SubscriptionStatisticSender;
use SubscriptionBundle\Service\CapConstraint\SubscriptionCounterUpdater;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Subscription\Common\ProcessResultSuccessChecker;
use SubscriptionBundle\Subscription\Common\SendNotificationChecker;
use SubscriptionBundle\Subscription\Common\SubscriptionFactory;
use SubscriptionBundle\Subscription\Subscribe\OnSubscribeUpdater;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\Common\SubscribePerformer;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\Common\SendNotificationPerformer;
use SubscriptionBundle\Subscription\Subscribe\ProcessStarter\SubscribeProcessStarterProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 15:36
 */
class SubscriberTest extends \PHPUnit\Framework\TestCase
{

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;


    /**
     * @var \SubscriptionBundle\Subscription\Subscribe\Subscriber
     */
    private $subscriber;

    /**
     * @var \SubscriptionBundle\BillingFramework\Process\SubscribeProcess|\Mockery\MockInterface
     */
    private $subscribeProcess;
    /**
     * @var SubscriptionFactory|\Mockery\MockInterface
     */
    private $subscriptionCreator;
    private $subscribePromotionalPerformer;
    private $subscribePerformer;
    private $subscribeProcessStarterProvider;
    private $commonStarter;


    public function testSubscribe()
    {
        $carrier = new \App\Domain\Entity\Carrier(UuidGenerator::generate());
        $carrier->setBillingCarrierId(1);

        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setProviderManagedSubscriptions(true);
        $subscriptionPack->setCarrier(Mockery::spy(\CommonDataBundle\Entity\Interfaces\CarrierInterface::class));
        $user = new User(UuidGenerator::generate());
        $user->setCarrier($carrier);
        $user->setIdentifier('test');

        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setUser($user);

        $this->subscriptionCreator->allows([
            'create' => $subscription
        ]);


        $this->subscribeProcess->allows([
            'doSubscribe' => new ProcessResult()
        ]);


        $this->subscriber->subscribe($subscription);

        $this->subscribePerformer->shouldHaveReceived('doSubscribe')->once();
        /*$this->affiliateService->shouldHaveReceived('checkAffiliateEligibilityAndSendEvent')->once();*/
        /*$this->piwikSender->shouldHaveReceived('trackSubscribe')->once();*/

    }

    public function testResubscribe()
    {

        $user                 = new User(UuidGenerator::generate());
        $existingSubscription = new Subscription(UuidGenerator::generate());
        $existingSubscription->setUser($user);
        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setProviderManagedSubscriptions(true);
        $subscriptionPack->setCarrier(Mockery::spy(\CommonDataBundle\Entity\Interfaces\CarrierInterface::class));
        $existingSubscription->setSubscriptionPack($subscriptionPack);

        $this->subscriber->resubscribe($existingSubscription, $subscriptionPack);

        $this->subscribePerformer->shouldHaveReceived('doSubscribe')->once();
        /*$this->affiliateService->shouldHaveReceived('checkAffiliateEligibilityAndSendEvent')->once();
        $this->piwikSender->shouldHaveReceived('trackResubscribe')->once();*/

    }

    /**
     *
     */
    protected function setUp()
    {

        $this->subscriptionCreator           = Mockery::spy(SubscriptionFactory::class);
        $this->subscribeProcess              = Mockery::spy(SubscribeProcess::class);
        $this->subscribePromotionalPerformer = Mockery::spy(SendNotificationPerformer::class);
        $this->subscribePerformer            = Mockery::spy(SubscribePerformer::class);
        $this->commonStarter                 = new \SubscriptionBundle\Subscription\Subscribe\ProcessStarter\CommonStarter(
            Mockery::spy(ProcessResultSuccessChecker::class),
            $this->subscribePerformer,
            $this->subscribePromotionalPerformer,
            Mockery::spy(SendNotificationChecker::class),
            Mockery::spy(CampaignExtractor::class)
        );

        $this->subscribeProcessStarterProvider = new SubscribeProcessStarterProvider($this->commonStarter);
        $this->subscriber                      = new \SubscriptionBundle\Subscription\Subscribe\Subscriber(
            Mockery::spy(EntitySaveHelper::class),
            Mockery::spy(OnSubscribeUpdater::class),
            Mockery::spy(SubscriptionLimitCompleter::class),
            Mockery::spy(ApiConnector::class),
            Mockery::spy(ProcessResultSuccessChecker::class),
            $this->subscribeProcessStarterProvider
        );

    }
}