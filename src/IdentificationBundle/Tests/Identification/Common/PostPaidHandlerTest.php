<?php


namespace IdentificationBundle\Tests\Identification\Common;


use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\Session\SessionStorage;
use Mockery;
use Psr\Log\LoggerInterface;
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

    public function testProcessWhenNotPostPaid()
    {
        $this->process(2, 'Prepaid');

        $this->postPaidHandler->process(123, 321);

        $this->assertEquals(2, $this->identificationDataStorage->isPostPaidRestricted());
    }

    public function testProcessWhenPostPaid()
    {
        $this->process(1, 'Postpaid');

        $this->assertEquals(1, $this->identificationDataStorage->isPostPaidRestricted());
    }

    public function testIsNotPostPaidRestricted()
    {
        $this->testProcessWhenNotPostPaid();
        $this->assertNotTrue($this->postPaidHandler->isPostPaidRestricted());
    }

    public function testIsPostPaidRestricted()
    {
        $this->testProcessWhenPostPaid();
        $this->assertTrue($this->postPaidHandler->isPostPaidRestricted());
    }

    protected function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->logger = Mockery::spy(LoggerInterface::class);
        $this->client = Mockery::spy(Client::class);
        $this->linkCreator = Mockery::spy(LinkCreator::class);
        $this->identificationDataStorage = new IdentificationDataStorage(new SessionStorage($this->session));

        $this->postPaidHandler = new PostPaidHandler(
            $this->logger,
            $this->client,
            $this->linkCreator,
            $this->identificationDataStorage
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