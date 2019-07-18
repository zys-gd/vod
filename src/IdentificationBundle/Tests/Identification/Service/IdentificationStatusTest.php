<?php declare(strict_types=1);


use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Identification\Service\Session\SessionStorage;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class IdentificationStatusTest extends TestCase
{
    /** @var IdentificationStatus */
    private $identificationStatus;

    /** @var IdentificationDataStorage | MockInterface */
    private $dataStorage;

    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;

    /**
     * @var SessionInterface
     */
    private $session;

    protected function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());
        $sessionStorage = new SessionStorage($this->session);
        $this->dataStorage = new IdentificationDataStorage($sessionStorage);
        $this->wifiIdentificationDataStorage = new WifiIdentificationDataStorage($sessionStorage);
        $this->identificationStatus = new IdentificationStatus($this->dataStorage, $this->wifiIdentificationDataStorage);
    }

    public function testFinishIdent()
    {
        $this->identificationStatus->finishIdent('token', Mockery::spy(\IdentificationBundle\Entity\User::class));

        $this->assertArraySubset(['identification_token' => 'token'],$this->dataStorage->getIdentificationData());

        $this->assertFalse($this->wifiIdentificationDataStorage->isWifiFlow());

    }


    public function testIsIdentified()
    {
        $this->session->set('identification_data',['identification_token' => '123token']);

        $this->assertTrue($this->identificationStatus->isIdentified());
    }

    public function testIsWifiFlowStarted()
    {
        $this->wifiIdentificationDataStorage->setWifiFlow(true);

        $this->assertTrue($this->identificationStatus->isWifiFlowStarted());

    }
}
