<?php

namespace IdentificationBundle\Tests\Identification\Service;

use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\Session\SessionStorage;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Class IdentificationDataStorageTest
 */
class IdentificationDataStorageTest extends TestCase
{
    /** @var IdentificationDataStorage */
    private $identificationDataStorage;

    /** @var SessionInterface | MockInterface */
    private $session;

    protected function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->identificationDataStorage = new IdentificationDataStorage(new SessionStorage($this->session));
    }

    public function testStoreIdentificationToken()
    {
        $this->identificationDataStorage->setIdentificationToken('token');

        $data = $this->session->get(IdentificationDataStorage::IDENTIFICATION_DATA_KEY);

        $this->assertArraySubset(['identification_token' => 'token'], $data);
    }

    public function testReadIdentificationData()
    {
        $this->session->set(
            IdentificationDataStorage::IDENTIFICATION_DATA_KEY,
            ['identification_token' => 'token']
        );

        $result = $this->identificationDataStorage->getIdentificationData();

        $this->assertArraySubset(['identification_token' => 'token'], $result);
    }

    public function testStoreCarrierId()
    {
        $this->identificationDataStorage->setCarrierId(135);

        $data = $this->session->get(IdentificationDataStorage::ISP_DETECTION_DATA_KEY);

        $this->assertArraySubset(['carrier_id' => '135'], $data);
    }
}