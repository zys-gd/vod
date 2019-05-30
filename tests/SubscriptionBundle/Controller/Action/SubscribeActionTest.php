<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.06.18
 * Time: 11:33
 */

namespace Controller\Action;

use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PiwikBundle\Service\PiwikTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender as NotificationService;
use SubscriptionBundle\BillingFramework\Process\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\SubscriptionPackDataProvider;
use SubscriptionBundle\Piwik\SubscriptionStatisticSender;
use SubscriptionBundle\Service\CampaignConfirmation\Handler\CampaignConfirmationHandlerProvider;
use SubscriptionBundle\Service\CAPTool\SubscriptionLimiter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\DataFixtures\LoadCampaignTestData;
use Tests\DataFixtures\LoadSubscriptionTestData;
use Tests\SubscriptionBundle\BillingFramework\TestBillingResponseProvider;

class SubscribeActionTest extends AbstractFunctionalTest
{

    use MockeryPHPUnitIntegration;


    /**
     * @var ClientInterface|MockInterface
     */
    private $httpClient;

    /**
     * @var SubscriptionPackDataProvider|MockInterface
     */
    private $subscriptionPackDataProvider;

    /**
     * @var RequestSender|MockInterface
     */
    private $notificationService;

    /**
     * @var SubscriptionStatisticSender|MockInterface
     */
    private $piwikStatisticSender;

    /** @var  RequestSender|MockInterface */
    private $requestSender;
    /**
     * @var MockInterface|CampaignConfirmationHandlerProvider
     */
    private $campaignConfirmationHandlerProvider;
    private $subscriptionLimiter;

    public function testSubscribeWithoutIdentWillFallIntoError()
    {
        $client = $this->makeClient();
        $client->request('GET', 'subscribe?f=1');

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'error is missing');
    }

    // public function testSubscribeActionWithRedirect()
    // {
    //     $client = $this->makeClient();
    //
    //     $this->session->set('identification_data', ['identification_token' => 'token_for_user_without_subscription']);
    //
    //     $ispDetectionData = [
    //         'isp_name'   => 'Jazz PK',
    //         'carrier_id' => 338,
    //     ];
    //     $this->session->set('isp_detection_data', $ispDetectionData);
    //
    //     $this->httpClient->allows([
    //         'request' => TestBillingResponseProvider::createSuccessfulRedirectResponse('renew', 'billing_redirect_url')
    //     ]);
    //
    //     $client->request('GET', 'subscribe');
    //     $this->assertStatusCode(302, $client);
    //     $this->assertTrue($client->getResponse()->isRedirect('billing_redirect_url'), 'redirect is missing');
    //
    // }

    public function testUserIsRedirectedOnNotAllowedResub()
    {
        $client = $this->makeClient();

        $this->session->set('identification_data', ['identification_token' => 'inactive_subscription_ident_request']);

        $ispDetectionData = [
            'isp_name'   => 'Jazz PK',
            'carrier_id' => 338,
        ];
        $this->session->set('isp_detection_data', $ispDetectionData);

        $client->request('GET', 'subscribe');

        $this->assertTrue($client->getResponse()->isRedirect('/rsna'), 'redirect is missing');

    }

    public function testResubAllowedViaCarrierFlag()
    {
        $client = $this->makeClient();

        $this->session->set('identification_data', ['identification_token' => 'inactive_subscription_ident_for_carrier_with_allowed_resub_request']);


        $ispDetectionData = [
            'isp_name'   => 'Allowed Resub Carrier',
            'carrier_id' => 10241027
        ];

        $this->session->set('isp_detection_data', $ispDetectionData);

        $this->httpClient->allows([
            'request' => TestBillingResponseProvider::createSuccessfulFinalResponse('subscribe')
        ]);

        $client->request('GET', 'subscribe');

        $this->assertTrue($client->getResponse()->isRedirect('/'), 'Failed resub');
    }

    public function testResubIsAllowedForNotFullyPaidSubscription()
    {
        $client = $this->makeClient();

        $this->session->set('identification_data', ['identification_token' => 'onhold_subscription_ident_request']);
        $ispDetectionData = [
            'isp_name'   => 'Jazz PK',
            'carrier_id' => 338,
        ];
        $this->session->set('isp_detection_data', $ispDetectionData);
        $this->httpClient->allows([
            'request' => TestBillingResponseProvider::createSuccessfulFinalResponse('subscribe')
        ]);

        $client->request('GET', 'subscribe');

        $this->assertTrue($client->getResponse()->isRedirect('/'), 'redirect is missing');
    }

    public function testPostPaidRestrictedRedirect()
    {
        $client = $this->makeClient();
        $this->session->set('storage[isPostPaidRestricted]', 1);
        $this->session->set('identification_data', ['identification_token' => 'token_for_user_without_subscription']);

        $ispDetectionData = [
            'isp_name'   => 'Jazz PK',
            'carrier_id' => 338,
        ];
        $this->session->set('isp_detection_data', $ispDetectionData);

        $client->request('GET', 'subscribe');
        $this->assertTrue($client->getResponse()->isRedirect('/?err_handle=postpaid_restricted'), 'redirect is missing');
    }

    public function testCampaignConfirmationCustomPage()
    {
        $client = $this->makeClient();
        AffiliateVisitSaver::saveCampaignId('google_campaign_token', $this->session);
        $this->session->set('identification_data', ['identification_token' => 'token_for_user_without_subscription']);

        $ispDetectionData = [
            'isp_name'   => 'Jazz PK',
            'carrier_id' => 338,
        ];
        $this->session->set('isp_detection_data', $ispDetectionData);

        $client->request('GET', 'subscribe');
        $this->assertTrue($client->getResponse()->isRedirect('/google_campaign'));
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        $container->set('SubscriptionBundle\BillingFramework\Process\SubscriptionPackDataProvider', $this->subscriptionPackDataProvider);
        $container->set('SubscriptionBundle\BillingFramework\Notification\API\RequestSender', $this->notificationService);
        $container->set('SubscriptionBundle\BillingFramework\Process\API\RequestSender', $this->requestSender);
        $container->set('talentica.piwic_statistic_sender', $this->piwikStatisticSender);
        $container->set('subscription.http.client', $this->httpClient);
        $container->set('SubscriptionBundle\Service\CAPTool\SubscriptionLimiter', $this->subscriptionLimiter);
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [
            LoadSubscriptionTestData::class,
            LoadCampaignTestData::class
        ];
    }

    protected function initializeServices(ContainerInterface $container)
    {

        $this->httpClient                   = \Mockery::spy(Client::class);
        $this->subscriptionPackDataProvider = \Mockery::spy(SubscriptionPackDataProvider::class);
        $this->notificationService          = \Mockery::spy(NotificationService::class);
        $this->subscriptionLimiter          = Mockery::spy(SubscriptionLimiter::class);
        $this->piwikStatisticSender         = Mockery::spy(SubscriptionStatisticSender::class, [
            Mockery::spy(LoggerInterface::class),
            Mockery::spy(PiwikTracker::class),
        ])->makePartial();
    }

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }

}