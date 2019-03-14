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
use PiwikBundle\Service\NewTracker;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Piwik\SubscriptionStatisticSender;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\BillingFramework\Process\SubscriptionPackDataProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    public function testSubscribeWithoutIdentWillRedirectToSbna()
    {
        $client = $this->makeClient();
        $client->request('GET', 'subscribe?f=1');

        $this->assertTrue($client->getResponse()->isRedirect('/sbna'), 'redirect is missing');
    }

    public function testSubscribeActionWithRedirect()
    {
        $client = $this->makeClient();

        $this->session->set('identification_token', 'token_for_user_without_subscription');
        $this->httpClient->allows([
            'request' => TestBillingResponseProvider::createSuccessfulRedirectResponse('renew', 'billing_redirect_url')
        ]);

        $client->request('GET', 'subscribe?f=1');

        // Set session data
        $ispDetectionData = [
            'isp_name'   => $carrier->getName(),
            'carrier_id' => $carrier->getBillingCarrierId(),
        ];
        $this->session->set('isp_detection_data', $ispDetectionData);

        $this->session->set('identification_data', ['identification_token' => $identificationRequestToken]);

        $this->assertStatusCode(302, $client);
        $this->assertTrue($client->getResponse()->isRedirect('billing_redirect_url'), 'redirect is missing');

    }

    public function testUserIsRedirectedOnNotAllowedResub()
    {
        $client = $this->makeClient();

        $this->session->set('identification_token', 'inactive_subscription_ident_request');

        $client->request('GET', 'subscribe');

        $this->assertTrue($client->getResponse()->isRedirect('/rsna'), 'redirect is missing');

    }

    public function testResubIsAllowedForOnHoldSubscription()
    {
        $client = $this->makeClient();

        $this->session->set('identification_token', 'onhold_subscription_ident_request');
        $this->httpClient->allows([
            'request' => TestBillingResponseProvider::createSuccessfulFinalResponse('subscribe')
        ]);

        $client->request('GET', 'subscribe');

        $this->assertTrue($client->getResponse()->isRedirect('/'), 'redirect is missing');
    }


    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        // $container->set('subscription.http.client', $this->httpClient);
        $container->set('talentica.billing_framework_subscription_data_provider', $this->subscriptionPackDataProvider);
        $container->set('talentica.notification.service', $this->notificationService);
        $container->set('talentica.piwic_statistic_sender', $this->piwikStatisticSender);

    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [
            LoadSubscriptionTestData::class
        ];
    }

    protected function initializeServices(ContainerInterface $container)
    {

        $this->httpClient                   = \Mockery::spy(Client::class);
        $this->subscriptionPackDataProvider = \Mockery::spy(SubscriptionPackDataProvider::class);
        $this->notificationService          = \Mockery::spy(RequestSender::class);

        $this->piwikStatisticSender = Mockery::spy(SubscriptionStatisticSender::class, [
            Mockery::spy(LoggerInterface::class),
            Mockery::spy(NewTracker::class),
        ])->makePartial();
    }

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }

}