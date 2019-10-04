<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.10.19
 * Time: 17:47
 */

namespace SubscriptionBundle\Tests\CAPTool;


use ExtrasBundle\Cache\Redis\MockeryRedisDummyTrait;
use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use IdentificationBundle\BillingFramework\ID;
use Mockery;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\CAPTool\Subscription\Limiter\LimiterStorage;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitNotifier;
use SubscriptionBundle\DataFixtures\ORM\LoadExchangeRatesData;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\DataFixtures\LoadAffiliateConstraintTestData;
use Tests\DataFixtures\LoadSubscriptionTestData;

class CAPToolOnSubscribeTest extends AbstractFunctionalTest
{
    use MockeryRedisDummyTrait;

    /**
     * @var SubscriptionLimitNotifier|Mockery\MockInterface
     */
    private $subscriptionLimitNotifier;

    /**
     * @var LimiterStorage|Mockery\MockInterface
     */
    private $limiterStorage;

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }

    public function testIsSubscriptionNotAllowedWhenLimitByAffConstraint()
    {
        $client = $this->makeClient();

        /** @var ConstraintByAffiliate $constraint */
        $constraint = $this->getObjectFromFixture('test_subscription_cap_constraint');

        $this->performFixtureChange(function () use ($constraint) {
            $constraint->setNumberOfActions(4);
        });
        $this->limiterStorage->allows([
            'getPendingSubscriptionAmount'  => 2,
            'getFinishedSubscriptionAmount' => 2,
            'isSubscriptionAlreadyPending'  => false
        ]);

        AffiliateVisitSaver::saveCampaignId('test_campaign', $this->session);

        $this->session->set('identification_data', [
            'identification_token' => 'token_for_user_without_subscription'
        ]);
        $this->session->set('isp_detection_data', [
            'isp_name'   => 'Jazz PK',
            'carrier_id' => ID::MOBILINK_PAKISTAN
        ]);

        $client->request('GET', '/subscribe', ['f' => 1]);

        $this->assertStatusCode(302, $client);
        $this->assertTrue($client->getResponse()->isRedirect('test_cap_redirect_url'), 'redirect is missing');

        $this->subscriptionLimitNotifier->shouldHaveReceived('notifyLimitReachedByAffiliate');


    }

    protected function initializeServices(ContainerInterface $container)
    {
        $this->subscriptionLimitNotifier = Mockery::spy(SubscriptionLimitNotifier::class);
        $this->limiterStorage            = Mockery::mock(LimiterStorage::class);
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [
            LoadSubscriptionTestData::class,
            LoadExchangeRatesData::class,
            LoadAffiliateConstraintTestData::class
        ];
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        $container->set('SubscriptionBundle\CAPTool\Subscription\SubscriptionLimitNotifier', $this->subscriptionLimitNotifier);
        $container->set('SubscriptionBundle\CAPTool\Subscription\Limiter\LimiterStorage', $this->limiterStorage);
    }
}