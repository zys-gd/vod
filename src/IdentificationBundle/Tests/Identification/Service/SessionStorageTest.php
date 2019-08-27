<?php declare(strict_types=1);

use IdentificationBundle\Identification\Service\Session\SessionStorage;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SessionStorageTest extends TestCase
{
    /**
     * @var SessionStorage
     */
    private $sessionStorage;

    /** @var SessionInterface | MockInterface */
    private $session;

    protected function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->sessionStorage = new SessionStorage($this->session);
    }

    public function testStoreOperationResult()
    {
        $this->sessionStorage->storeOperationResult('key', 'value');
        $this->assertEquals(serialize('value'), $this->session->get(SessionStorage::RESULTS_KEY . '[key]'));
    }

    public function testReadPreviousOperationResult()
    {
        $this->session->set(SessionStorage::RESULTS_KEY . '[key]', serialize('value'));

        $value = $this->sessionStorage->readOperationResult('key');

        $this->assertEquals($value, 'value');
    }

    public function testStoreStorageValue()
    {
        $this->sessionStorage->storeValue('key', 'value');

        $this->assertEquals('value', $this->session->get('key'));
    }

    public function testReadStorageValue()
    {
        $this->session->set('key', 'value');

        $value = $this->sessionStorage->readValue('key');

        $this->assertEquals($value, 'value');
    }
}
