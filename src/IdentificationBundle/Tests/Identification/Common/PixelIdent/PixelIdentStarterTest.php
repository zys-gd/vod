<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 17:05
 */

namespace IdentificationBundle\Tests\Identification\Common\PixelIdent;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Common\Pixel\PixelIdentStarter;
use IdentificationBundle\Identification\Service\RouteProvider;
use Mockery;
use PHPUnit\Framework\TestCase;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class PixelIdentStarterTest extends TestCase
{
    private $session;
    /**
     * @var PixelIdentStarter
     */
    private $pixelIdentStarter;
    /**
     * @var Mockery\MockInterface
     */
    private $router;
    private $routeProvider;

    protected function setUp()
    {
        $this->session           = new Session(new MockArraySessionStorage());
        $this->router            = Mockery::spy(RouterInterface::class);
        $this->routeProvider     = Mockery::spy(RouteProvider::class);
        $this->pixelIdentStarter = new PixelIdentStarter(
            $this->router,
            $this->routeProvider
        );
        parent::setUp();
    }

    public function testStartIsWorkingOk()
    {
        $this->router->allows(['generate' => 'superUrl']);

        $result = $this->pixelIdentStarter->start(
            new Request(),
            new ProcessResult(),
            Mockery::spy(CarrierInterface::class)
        );

        $this->assertEquals(
            'superUrl',
            $result->getTargetUrl()
        );
    }

    public function testeExceptionIsHandled()
    {
        $this->router
            ->shouldReceive('generate')
            ->andReturnValues([
                new RouteNotFoundException(),
                'pixelPageLink'
            ]);


        $this->routeProvider->allows(['getLinkToHomepage' => 'homepage']);

        $result = $this->pixelIdentStarter->start(
            new Request(),
            new ProcessResult(),
            Mockery::spy(CarrierInterface::class)
        );

        $this->assertEquals(
            'pixelPageLink',
            $result->getTargetUrl()
        );
    }


}