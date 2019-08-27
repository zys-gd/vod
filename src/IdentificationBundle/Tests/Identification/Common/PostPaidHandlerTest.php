<?php


namespace IdentificationBundle\Tests\Identification\Common;


use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\Session\SessionStorage;
use Mockery;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\BillingOptionsProvider;
use SubscriptionBundle\BillingFramework\Process\API\Client;
use SubscriptionBundle\BillingFramework\Process\API\LinkCreator;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class PostPaidHandlerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var IdentificationDataStorage|Mockery\MockInterface
     */
    private $identificationDataStorage;
    /**
     * @var Mockery\MockInterface|LoggerInterface
     */
    private $logger;
    /**
     * @var Mockery\MockInterface|Client
     */
    private $client;
    /**
     * @var Mockery\MockInterface|LinkCreator
     */
    private $linkCreator;
    /**
     * @var PostPaidHandler
     */
    private $postPaidHandler;
    /**
     * @var Session
     */
    private $session;

    public function testIsNotPostPaidRestricted()
    {
        $this->client->allows([
            'makePostRequest' => (object)[
                'data' =>
                    [
                        'accountTypeId'    => 2,
                        'accountTypeName'  => 'Prepaid',
                        'msisdn'           => '923052266578',
                        'providerResponse' => 'success',
                    ],
            ]
        ]);

        $this->postPaidHandler->process(123, 321);


        $this->assertNotTrue($this->postPaidHandler->isPostPaidRestricted());
    }

    public function testIsPostPaidRestricted()
    {
        $this->client->allows([
            'makePostRequest' => (object)[
                'data' =>
                    [
                        'accountTypeId'    => 1,
                        'accountTypeName'  => 'Postpaid',
                        'msisdn'           => '923052266578',
                        'providerResponse' => 'success',
                    ],
            ]
        ]);

        $this->postPaidHandler->process(123, 321);


        $this->assertEquals(1, $this->identificationDataStorage->readValue(IdentificationDataStorage::POST_PAID_RESTRICTED_KEY));

        $this->assertTrue($this->postPaidHandler->isPostPaidRestricted());
    }

    protected function setUp()
    {
        $this->session                   = new Session(new MockArraySessionStorage());
        $this->logger                    = Mockery::spy(LoggerInterface::class);
        $this->client                    = Mockery::spy(Client::class);
        $this->linkCreator               = Mockery::spy(LinkCreator::class);
        $this->identificationDataStorage = new IdentificationDataStorage(new SessionStorage($this->session));

        $this->postPaidHandler = new PostPaidHandler(
            $this->logger,
            $this->client,
            $this->linkCreator,
            $this->identificationDataStorage,
            Mockery::spy(BillingOptionsProvider::class)
        );

        parent::setUp();
    }

    private function process($accountTypeId, $accountTypeName): void
    {
        $this->client->allows([
            'makePostRequest' => (object)[
                'data' =>
                    [
                        'accountTypeId'    => $accountTypeId,
                        'accountTypeName'  => $accountTypeName,
                        'msisdn'           => '923052266578',
                        'providerResponse' => 'success',
                    ],
            ]
        ]);

        $this->postPaidHandler->process(123, 321);
    }
}