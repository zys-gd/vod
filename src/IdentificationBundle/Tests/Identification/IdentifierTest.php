<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 10:58
 */

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\Identification\Common\CommonConsentPageFlowHandler;
use IdentificationBundle\Identification\Common\CommonFlowHandler;
use IdentificationBundle\Identification\Common\HeaderEnrichmentHandler;
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\HasCustomFlow;
use IdentificationBundle\Identification\Handler\HasHeaderEnrichment;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class IdentifierTest extends TestCase
{

    /**
     * @var \Mockery\MockInterface|IdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var \Mockery\MockInterface
     */
    private $carrierRepository;
    /**
     * @var \Mockery\MockInterface
     */
    private $headerEnrichmentHandler;
    /**
     * @var \Mockery\MockInterface
     */
    private $commonFlowHandler;
    /**
     * @var \Mockery\MockInterface
     */
    private $logger;
    /**
     * @var \IdentificationBundle\Identification\Identifier
     */
    private $identifier;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    private $consentPageHandler;
    private $passthroughFlowHandler;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {


        $this->handlerProvider         = Mockery::spy(IdentificationHandlerProvider::class);
        $this->carrierRepository       = Mockery::spy(CarrierRepositoryInterface::class);
        $this->headerEnrichmentHandler = Mockery::spy(HeaderEnrichmentHandler::class);
        $this->logger                  = Mockery::spy(LoggerInterface::class);
        $this->commonFlowHandler       = Mockery::spy(CommonFlowHandler::class);

        $this->consentPageHandler = Mockery::spy(CommonConsentPageFlowHandler::class);
        $this->passthroughFlowHandler = Mockery::spy(\IdentificationBundle\Identification\Common\CommonPassthroughFlowHandler::class);
        $this->session                = new Session(new MockArraySessionStorage());


        $this->identifier = new \IdentificationBundle\Identification\Identifier(
            $this->handlerProvider,
            $this->carrierRepository,
            $this->logger,
            $this->commonFlowHandler,
            $this->headerEnrichmentHandler,
            $this->consentPageHandler,
            $this->passthroughFlowHandler
        );

        $this->carrierRepository->allows([
            'findOneByBillingId' => Mockery::spy(CarrierInterface::class)
        ]);

        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testHeaderEnrichment()
    {
        $request = new Request();

        $this->handlerProvider->allows([
            'get' => Mockery::spy(HasHeaderEnrichment::class, IdentificationHandlerInterface::class)
        ]);

        $this->identifier->identify(0, $request, 'token', Mockery::spy(DeviceData::class), $this->session);

        $this->assertTrue(true, 'smoke test is not passed');
    }

    public function testCustomFlow()
    {
        $request = new Request();

        $this->handlerProvider->allows([
            'get' => Mockery::spy(HasCustomFlow::class, IdentificationHandlerInterface::class)
        ]);

        $this->identifier->identify(0, $request, 'token', Mockery::spy(DeviceData::class), $this->session);

        $this->assertTrue(true, 'smoke test is not passed');
    }

    public function testCommonFlow()
    {
        $request = new Request();

        $this->session->set(IdentificationDataStorage::ISP_DETECTION_DATA_KEY, ['somedata' => '123']);

        $this->handlerProvider->allows([
            'get' => Mockery::spy(HasCommonFlow::class, IdentificationHandlerInterface::class)
        ]);

        $result = $this->identifier->identify(0, $request, 'token', Mockery::spy(DeviceData::class), $this->session);
        $this->assertNotNull($result->getOverridedResponse(), 'response are not propagated');


    }
}
