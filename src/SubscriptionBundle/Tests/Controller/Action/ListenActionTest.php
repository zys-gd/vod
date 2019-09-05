<?php

use App\Domain\Repository\CampaignRepository;
use GuzzleHttp\Client;
use IdentificationBundle\BillingFramework\ID;
use Mockery\Mock;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\Affiliate\AffiliateLogRepository;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerProvider;
use SubscriptionBundle\Subscription\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Subscription\Callback\Impl\HasCustomTrackingRules;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\DataFixtures\LoadSubscriptionTestData;

/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 22.05.18
 * Time: 18:34
 */
class ListenActionTest extends \ExtrasBundle\Testing\Core\AbstractFunctionalTest
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    const EXAMPLE_AFFILIATE_SESSION_KEY = 'affiliate_key';
    const COMMON_CARRIER_ID = 10241024;

    private $entityManager;

    /**
     * @var \SubscriptionBundle\Repository\SubscriptionRepository
     */
    private $subscriptionRepo;


    /**
     * @var PiwikTracker|Mock
     */
    private $piwikTracker;


    /**
     * @var Client|Mock
     */
    private $guzzleClient;

    /**
     * @var AffiliateLogRepository|Mock
     */
    private $affiliateLogRepo;


    /**
     * @var CampaignRepository|Mock
     */
    private $campaignRepo;

    /**
     * @var CarrierCallbackHandlerProvider|Mock
     */
    private $carrierCallbackHandlerProvider;


    /**
     * @var \IdentificationBundle\Repository\UserRepository|Mock
     */
    private $billableUserRepo;


    /**
     * @var \SubscriptionBundle\Piwik\EventPublisher|Mock
     */
    private $eventPublisher;


    public function carrierIdProvider()
    {
        return [
//            'GENERIC_CARRIER'   => [LoadSubscriptionTestData::GENERIC_CARRIER],
            'ZONG_PAKISTAN' => [ID::MOBILINK_PAKISTAN],
//            'ETISALAT_EGYPT'    => [ID::ETISALAT_EGYPT],
            'TELENOR_PAKISTAN'  => [ID::TELENOR_PAKISTAN_DOT],
//            'ORANGE_TUNISIA'    => [ID::ORANGE_TUNISIA],
//            'ORANGE_EGYPT'      => [ID::ORANGE_EGYPT],
//            'TELENOR_MYANMAR'   => [ID::TELENOR_MYANMAR],
//            'OOREDOO_ALGERIA'   => [ID::OOREDOO_ALGERIA],
//            'CELLCARD_KAMBODIA' => [ID::CELLCARD_CAMBODIA],
//            'OOREDOO_KUWAIT'    => [ID::OOREDOO_KUWAIT],
//            'VODAFONE_EGYPT'    => [ID::VODAFONE_EGYPT],
//            'TIGO_HONDURAS'     => [ID::TIGO_HONDURAS],

            /*[Carrier::ZAIN_IRAQ],*/
            /*[Carrier::MOBILINK_PAKISTAN],
            [Carrier::MTN_SUDAN],
            [Carrier::SMARTFEN_INDONESIA],
            [Carrier::AIRTEL_INDIA],
            [Carrier::DIALOG_SRILANKA],
            [Carrier::JAWWAL_PALESTINE],
            [Carrier::TELKOM_KENYA],
            [Carrier::TELE2_RUSSIA],
            [Carrier::GLOBE_PHILIPPINES],
            [Carrier::ZAIN],
            [Carrier::KCELL_KZ],
            [Carrier::ROBY_BD],
            [Carrier::OOREDOO_KW],*/
        ];
    }

    /**
     * @dataProvider carrierIdProvider()
     */
    public function testCreditsIsAddedOnSuccessfulSubscribe($carrierId)
    {
        /** @var Subscription $subscription */
        $subscription = $this->getObjectFromFixture('subscription_for_default_billable_user');
        /** @var SubscriptionPack $subscriptionPack */
        $subscriptionPack = $this->getObjectFromFixture(sprintf('subscription_pack_for_carrier_%s', $carrierId));
        $expectedCredits  = 5;

        $this->performFixtureChange(function () use ($subscription, $expectedCredits, $subscriptionPack) {
            $subscription->setCredits(15);
            $subscription->setSubscriptionPack($subscriptionPack);
            $subscriptionPack->setCredits($expectedCredits);
        });


        $client = $this->makeClient();
        $client->request('POST', '/v2/callback/listen', array(
            'client_id' => $subscription->getUuid(),
            'type'      => 'renew',
            'subtype'   => 'final',
            'status'    => 'successful',
            'carrier'   => $carrierId
        ));


        $modifiedSubscription = $this->subscriptionRepo->find($subscription->getUuid());
        $this->assertStatusCode(200, $client);
        $this->assertEquals($subscriptionPack->isUnlimited() ? 1000 : $expectedCredits, $modifiedSubscription->getCredits(), 'amount of credits is not valid');

    }


    public function testCallbackSuccessfulRenewAndPiwikTracked()
    {
        /** @var Subscription $subscription */
        $subscription     = $this->getObjectFromFixture('subscription_for_default_billable_user');
        $subscriptionPack = $this->getObjectFromFixture('generic_subscription_pack');

        $this->performFixtureChange(function () use ($subscription, $subscriptionPack) {
            $subscription->setSubscriptionPack($subscriptionPack);
        });

        $handler = Mockery::spy(
            HasCommonFlow::class
        );
        $this->carrierCallbackHandlerProvider->allows([
            'getHandler' => $handler
        ]);

        $client = $this->makeClient();
        $client->request('POST', '/v2/callback/listen', array(
            'client_id' => $subscription->getUuid(),
            'type'      => 'renew',
            'subtype'   => 'final',
            'status'    => 'successful',
            'carrier'   => LoadSubscriptionTestData::GENERIC_CARRIER

        ));


        $modifiedSubscription = $this->subscriptionRepo->find($subscription->getUuid());
        $this->assertStatusCode(200, $client);
        $this->assertTrue($modifiedSubscription->getCurrentStage() === Subscription::ACTION_SUBSCRIBE, 'subscription stage is not properly changed');
        $this->assertTrue($modifiedSubscription->getStatus() === Subscription::IS_ACTIVE, 'status is not properly changed');

        $this->eventPublisher->shouldHaveReceived('publish')->withArgs(function (...$args) {
            /** @var \SubscriptionBundle\Piwik\DTO\ConversionEvent $event */
            $event = $args[0];
            return $event->getOrderInformation()->getAction() == 'trackRenew';
        });
    }


    /**
     * @dataProvider carrierIdProvider
     */
    public function testCallbackFailedRenewAndPiwikTracked()
    {
        /** @var Subscription $subscription */
        $subscription     = $this->getObjectFromFixture('subscription_for_default_billable_user');
        $subscriptionPack = $this->getObjectFromFixture('generic_subscription_pack');

        $this->performFixtureChange(function () use ($subscription, $subscriptionPack) {
            $subscription->setSubscriptionPack($subscriptionPack);
        });


        $client = $this->makeClient();
        $client->request('POST', '/v2/callback/listen', array(
            'client_id' => $subscription->getUuid(),
            'type'      => 'renew',
            'subtype'   => 'final',
            'status'    => 'failed',
            'error'     => 'someerror',
            'carrier'   => LoadSubscriptionTestData::GENERIC_CARRIER

        ));

        $modifiedSubscription = $this->subscriptionRepo->find($subscription->getUuid());
        $this->assertStatusCode(200, $client);
        $this->assertTrue($modifiedSubscription->getCurrentStage() === Subscription::ACTION_RENEW, 'subscription stage is not properly changed');
        $this->assertTrue($modifiedSubscription->getStatus() === Subscription::IS_ERROR, 'status is not properly changed');
        $this->assertTrue($modifiedSubscription->getError() === 'someerror', 'error property is not properly set');

        $this->eventPublisher->shouldHaveReceived('publish')->withArgs(function (...$args) {
            /** @var \SubscriptionBundle\Piwik\DTO\ConversionEvent $event */
            $event = $args[0];
            return $event->getOrderInformation()->getAction() == 'trackRenew';
        });


    }

    public function testCallbackRenewWithRedirect()
    {
        /** @var Subscription $subscription */
        $subscription     = $this->getObjectFromFixture('subscription_for_default_billable_user');
        $subscriptionPack = $this->getObjectFromFixture('generic_subscription_pack');
        $this->performFixtureChange(function () use ($subscription, $subscriptionPack) {
            $subscription->setSubscriptionPack($subscriptionPack);
        });

        $oldStatus = $subscription->getStatus();
        $client    = $this->makeClient();

        $client->request('POST', '/v2/callback/listen', [
            'client_id' => $subscription->getUuid(),
            'type'      => 'renew',
            'subtype'   => 'redirect',
            'status'    => 'success',
            'url'       => 'redirectUrl',
            'carrier'   => LoadSubscriptionTestData::GENERIC_CARRIER

        ]);

        $updatedSubscription = $this->subscriptionRepo->find($subscription->getUuid());
        $this->assertStatusCode(200, $client);
        $this->assertEquals($oldStatus, $updatedSubscription->getStatus(), 'status is changed');
        $this->assertTrue($updatedSubscription->getCurrentStage() === Subscription::ACTION_SUBSCRIBE, 'subscription stage is not properly changed');
        $this->assertEquals(null, $updatedSubscription->getRedirectUrl(), 'redirect url is not ignored');
    }



    public function testCallbackForFailedSubscribeWithPiwik()
    {
        /** @var Subscription $subscription */
        $subscription     = $this->getObjectFromFixture('subscription_for_default_billable_user');
        $subscriptionPack = $this->getObjectFromFixture('generic_subscription_pack');
        $this->performFixtureChange(function () use ($subscription, $subscriptionPack) {
            $subscription->setSubscriptionPack($subscriptionPack);
        });


        $client = $this->makeClient();
        $client->request('POST', '/v2/callback/listen', array(
            'client_id' => $subscription->getUuid(),
            'type'      => 'subscribe',
            'subtype'   => 'final',
            'status'    => 'failed',
            'error'     => 'someerror',
            'carrier'   => LoadSubscriptionTestData::GENERIC_CARRIER
        ));

        $updatedSubscription = $this->subscriptionRepo->find($subscription->getUuid());

        $this->assertStatusCode(200, $client);
        $this->assertTrue($updatedSubscription->getCurrentStage() === Subscription::ACTION_SUBSCRIBE, 'subscription stage is not properly changed');
        $this->assertTrue($updatedSubscription->getStatus() === Subscription::IS_ERROR, 'status is not properly changed');
        $this->assertTrue($updatedSubscription->getError() === 'someerror', 'error property is not properly set');

        $this->eventPublisher->shouldNotHaveReceived('publish');
    }

    public function testCallbackForSuccessfulSubscribeWithPiwik()
    {
        /** @var Subscription $subscription */
        $subscription     = $this->getObjectFromFixture('subscription_for_default_billable_user');
        $subscriptionPack = $this->getObjectFromFixture('generic_subscription_pack');
        $this->performFixtureChange(function () use ($subscription, $subscriptionPack) {
            $subscription->setSubscriptionPack($subscriptionPack);
        });


        $client = $this->makeClient();
        $client->request('POST', '/v2/callback/listen', array(
            'client_id' => $subscription->getUuid(),
            'type'      => 'subscribe',
            'subtype'   => 'final',
            'status'    => 'failed',
            'error'     => 'someerror',
            'carrier'   => LoadSubscriptionTestData::GENERIC_CARRIER

        ));

        $updatedSubscription = $this->subscriptionRepo->find($subscription->getUuid());

        $this->assertStatusCode(200, $client);
        $this->assertTrue($updatedSubscription->getCurrentStage() === Subscription::ACTION_SUBSCRIBE, 'subscription stage is not properly changed');
        $this->assertTrue($updatedSubscription->getStatus() === Subscription::IS_ERROR, 'status is not properly changed');
        $this->assertTrue($updatedSubscription->getError() === 'someerror', 'error property is not properly set');

        $this->eventPublisher->shouldNotHaveReceived('publish');
    }

    public function testCallbackForFailedUnsubscribeWithPiwik()
    {
        /** @var Subscription $subscription */
        $subscription     = $this->getObjectFromFixture('subscription_for_default_billable_user');
        $subscriptionPack = $this->getObjectFromFixture('generic_subscription_pack');
        $this->performFixtureChange(function () use ($subscription, $subscriptionPack) {
            $subscription->setSubscriptionPack($subscriptionPack);
        });


        $client = $this->makeClient();
        $client->request('POST', '/v2/callback/listen', array(
            'client_id' => $subscription->getUuid(),
            'type'      => 'unsubscribe',
            'subtype'   => 'final',
            'status'    => 'failed',
            'error'     => 'someerror',
            'carrier'   => LoadSubscriptionTestData::GENERIC_CARRIER

        ));

        $updatedSubscription = $this->subscriptionRepo->find($subscription->getUuid());

        $this->assertStatusCode(200, $client);
        $this->assertTrue($updatedSubscription->getCurrentStage() === Subscription::ACTION_UNSUBSCRIBE, 'subscription stage is not properly changed');
        $this->assertTrue($updatedSubscription->getStatus() === Subscription::IS_ERROR, 'status is not properly changed');
        $this->assertTrue($updatedSubscription->getError() === 'someerror', 'error property is not properly set');

        $this->eventPublisher->shouldHaveReceived('publish')->withArgs(function (...$args) {
            /** @var \SubscriptionBundle\Piwik\DTO\ConversionEvent $event */
            $event = $args[0];
            return $event->getOrderInformation()->getAction() == 'trackUnsubscribe';
        });
    }


    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @throws Exception
     */
    protected function initializeServices(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $subPackProvider = Mockery::mock(SubscriptionPackProvider::class);
        $subPackProvider->allows([
            'getActiveSubscriptionPack' => new SubscriptionPack(\ExtrasBundle\Utils\UuidGenerator::generate())
        ]);

        $this->guzzleClient                   = Mockery::mock(Client::class)->shouldIgnoreMissing();
        $this->campaignRepo                   = Mockery::spy(CampaignRepository::class);
        $this->carrierCallbackHandlerProvider = Mockery::spy(CarrierCallbackHandlerProvider::class)->shouldIgnoreMissing();
        $this->eventPublisher                 = Mockery::spy(\SubscriptionBundle\Piwik\EventPublisher::class);

        $carrierHandler = Mockery::spy(
            HasCustomTrackingRules::class,
            CarrierCallbackHandlerInterface::class,
            HasCommonFlow::class
        );

        $carrierHandler->allows([
            'isNeedToBeTracked' => true,
        ]);

        $this->carrierCallbackHandlerProvider->allows([
            'getHandler' => $carrierHandler
        ]);

        $this->subscriptionRepo = $container->get('SubscriptionBundle\Repository\SubscriptionRepository');
        $this->billableUserRepo = $container->get('IdentificationBundle\Repository\UserRepository');
        $this->affiliateLogRepo = $container->get('SubscriptionBundle\Repository\Affiliate\AffiliateLogRepository');
        $this->entityManager    = $container->get('doctrine.orm.entity_manager');
    }

    protected function getPrequisiteFixturesList(): array
    {
        return [];
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [
            LoadSubscriptionTestData::class,
            \SubscriptionBundle\DataFixtures\ORM\LoadExchangeRatesData::class
        ];
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        $container->set('SubscriptionBundle\Piwik\EventPublisher', $this->eventPublisher);
    }

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }
}
