<?php

use App\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateSender;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Piwik\SubscriptionStatisticSender;
use SubscriptionBundle\Service\Action\Common\FakeResponseProvider;
use SubscriptionBundle\Service\Action\Common\PromotionalResponseChecker;
use SubscriptionBundle\Service\Action\Subscribe\OnSubscribeUpdater;
use SubscriptionBundle\Service\Action\Subscribe\SubscribeParametersProvider;
use SubscriptionBundle\Service\CapConstraint\SubscriptionCounterUpdater;
use SubscriptionBundle\Service\EntitySaveHelper;
use SubscriptionBundle\Service\Notification\Notifier;
use SubscriptionBundle\Service\SubscriptionCreator;
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
     * @var SubscriptionStatisticSender|\Mockery\MockInterface
     */
    private $piwikSender;

    /**
     * @var \SubscriptionBundle\Service\Action\Subscribe\Subscriber
     */
    private $subscriber;

    /**
     * @var \SubscriptionBundle\BillingFramework\Process\SubscribeProcess|\Mockery\MockInterface
     */
    private $subscribeProcess;
    /**
     * @var SubscriptionCreator|\Mockery\MockInterface
     */
    private $subscriptionCreator;


    public function testSubscribe()
    {

        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setProviderManagedSubscriptions(true);
        $user         = new User(UuidGenerator::generate());
        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);
        $subscription->setUser($user);

        $this->subscriptionCreator->allows([
            'create' => $subscription
        ]);


        $this->subscribeProcess->allows([
            'doSubscribe' => new ProcessResult()
        ]);


        $this->subscriber->subscribe($user, $subscriptionPack);

        $this->subscribeProcess->shouldHaveReceived('doSubscribe')->once();
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
        $existingSubscription->setSubscriptionPack($subscriptionPack);

        $this->subscriber->resubscribe($existingSubscription, $subscriptionPack);

        $this->subscribeProcess->shouldHaveReceived('doSubscribe')->once();
        /*$this->affiliateService->shouldHaveReceived('checkAffiliateEligibilityAndSendEvent')->once();
        $this->piwikSender->shouldHaveReceived('trackResubscribe')->once();*/

    }

    /**
     *
     */
    protected function setUp()
    {

        $this->piwikSender         = Mockery::spy(SubscriptionStatisticSender::class);
        $this->subscriptionCreator = Mockery::spy(SubscriptionCreator::class);
        $this->affiliateService    = Mockery::spy(AffiliateSender::class);
        $this->subscribeProcess    = Mockery::spy(SubscribeProcess::class);


        $this->subscriber = new \SubscriptionBundle\Service\Action\Subscribe\Subscriber(
            Mockery::spy(LoggerInterface::class),
            Mockery::spy(EntitySaveHelper::class),
            Mockery::spy(SessionInterface::class),
            $this->subscriptionCreator,
            Mockery::spy(PromotionalResponseChecker::class),
            Mockery::spy(FakeResponseProvider::class),
            Mockery::spy(Notifier::class),
            $this->subscribeProcess,
            Mockery::spy(OnSubscribeUpdater::class),
            Mockery::spy(SubscribeParametersProvider::class),
            Mockery::spy(SubscriptionCounterUpdater::class)
        );

    }
}