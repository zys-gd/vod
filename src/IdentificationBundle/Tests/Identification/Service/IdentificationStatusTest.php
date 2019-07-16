<?php declare(strict_types=1);


use IdentificationBundle\Identification\Service\IdentificationStatus;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class IdentificationStatusTest extends TestCase
{
    /** @var IdentificationStatus */
    private $identificationStatus;

    /** @var IdentificationBundle\Identification\Service\IdentificationDataStorage | MockInterface */
    private $dataStorage;
    private $session;

    protected function setUp()
    {

        $this->session              = new Session(new MockArraySessionStorage());
        $this->dataStorage          = new \IdentificationBundle\Identification\Service\IdentificationDataStorage($this->session);
        $this->identificationStatus = new IdentificationStatus(
            $this->dataStorage
        );
    }

    public function testFinishIdent()
    {
        $this->identificationStatus->finishIdent('token', Mockery::spy(\IdentificationBundle\Entity\User::class));

        $this->assertArraySubset(['identification_token' => 'token'],$this->dataStorage->readIdentificationData());

        $this->assertFalse($this->dataStorage->isWifiFlow());

    }


    public function testIsIdentified()
    {
        $this->session->set('identification_data',['identification_token' => '123token']);

        $this->assertTrue($this->identificationStatus->isIdentified());
    }

    public function testIsWifiFlowStarted()
    {
        $this->dataStorage->setWifiFlow(true);

        $this->assertTrue($this->identificationStatus->isWifiFlowStarted());

    }
}
