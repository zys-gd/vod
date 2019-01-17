<?php declare(strict_types=1);


use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class IdentificationDataStorageTest extends TestCase
{
    /** @var IdentificationDataStorage */
    private $identificationDataStorage;

    /** @var SessionInterface | MockInterface */
    private $session;

    protected function setUp()
    {
        $this->session                   = new Session(new MockArraySessionStorage());
        $this->identificationDataStorage = new IdentificationDataStorage(
            $this->session
        );
    }

    public function testStoreOperationResult()
    {
        $this->identificationDataStorage->storeOperationResult('key', 'value');

        $this->assertEquals(serialize('value'), $this->session->get('results[key]'));

    }

    public function testReadPreviousOperationResult()
    {

        $this->session->set('results[key]', serialize('value'));

        $value = $this->identificationDataStorage->readPreviousOperationResult('key');

        $this->assertEquals($value, 'value');
    }

    public function testStoreIdentificationToken()
    {
        $this->identificationDataStorage->storeIdentificationToken('token');

        $data = $this->session->get('identification_data');

        $this->assertArraySubset(['identification_token' => 'token'], $data);

    }

    public function testReadIdentificationData()
    {
        $this->session->set('identification_data', ['identification_token' => 'token']);

        $result = $this->identificationDataStorage->readIdentificationData('token');

        $this->assertArraySubset(['identification_token' => 'token'], $result);


    }


}
