<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 10:58
 */

use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Common\CommonFlowHandler;
use IdentificationBundle\Identification\Common\HeaderEnrichmentHandler;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\HasCustomFlow;
use IdentificationBundle\Identification\Handler\HasHeaderEnrichment;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
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

    private $dataStorage;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {


        $this->handlerProvider         = Mockery::spy(IdentificationHandlerProvider::class);
        $this->carrierRepository       = Mockery::spy(CarrierRepositoryInterface::class);
        $this->headerEnrichmentHandler = Mockery::spy(HeaderEnrichmentHandler::class);
        $this->logger                  = Mockery::spy(LoggerInterface::class);
        $this->commonFlowHandler       = Mockery::spy(CommonFlowHandler::class);

        $this->session = new Session(new MockArraySessionStorage());

        $this->dataStorage = new \IdentificationBundle\Identification\Service\IdentificationDataStorage($this->session);


        $this->identifier = new \IdentificationBundle\Identification\Identifier(
            $this->handlerProvider,
            $this->carrierRepository,
            $this->logger,
            $this->commonFlowHandler,
            $this->headerEnrichmentHandler,
            $this->dataStorage
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

        $this->identifier->identify(0, $request, 'token', $this->session);

        $this->assertTrue(true, 'smoke test is not passed');
    }

    public function testCustomFlow()
    {
        $request = new Request();

        $this->handlerProvider->allows([
            'get' => Mockery::spy(HasCustomFlow::class, IdentificationHandlerInterface::class)
        ]);

        $this->identifier->identify(0, $request, 'token', $this->session);

        $this->assertTrue(true, 'smoke test is not passed');
    }

    public function testCommonFlow()
    {
        $request = new Request();

        $this->session->set('isp_detection_data', ['somedata' => '123']);

        $this->handlerProvider->allows([
            'get' => Mockery::spy(HasCommonFlow::class, IdentificationHandlerInterface::class)
        ]);

        $result = $this->identifier->identify(0, $request, 'token', $this->session);
        $this->assertNotNull($result->getOverridedResponse(), 'response are not propagated');


    }
}