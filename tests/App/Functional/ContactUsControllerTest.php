<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.02.19
 * Time: 12:19
 */

namespace App\Functional;


use DataFixtures\LoadCarriersData;
use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use SubscriptionBundle\DataFixtures\ORM\LoadSubscriptionPackData;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContactUsControllerTest extends AbstractFunctionalTest
{

    protected static function getKernelClass()
    {
        return \VODKernel::class;
    }

    public function testContactUsActionIsOk()
    {
        $client = $this->makeClient();
        $client->request('get', '/contact-us', ['f' => 1]);


        $location = $client->getResponse()->headers->get('Location');
        $this->assertContains('lp', $location, 'redirect is missing');
    }

    public function testEmailIsSentProperly()
    {

        $this->session->set('identification_data', ['identification_token' => 'xd']);

        $client = $this->makeClient();

        $client->enableProfiler();

        $token = $client
            ->getContainer()
            ->get('security.csrf.token_manager')
            ->getToken('contact-us')
            ->getValue();


        $client->request('POST', '/contact-us', [
            'f'          => 1,
            'contact_us' => [
                '_csrf_token' => $token,
                'email'       => 'v@gmail.com',
                'comment'     => 'Test Comment',
            ]
        ],
            [],
            ['REMOTE_ADDR' => '119.160.116.250']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());

    }

    protected function initializeServices(ContainerInterface $container)
    {
        // TODO: Implement initializeServices() method.
    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [
            LoadCarriersData::class,
            LoadSubscriptionPackData::class
        ];
    }

    protected function configureWebClientClientContainer(ContainerInterface $container)
    {
        // TODO: Implement configureWebClientClientContainer() method.
    }
}