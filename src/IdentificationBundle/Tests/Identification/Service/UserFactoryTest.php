<?php declare(strict_types=1);


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\Service\UserFactory;
use PHPUnit\Framework\TestCase;

class UserFactoryTest extends TestCase
{
    /** @var UserFactory */
    private $userFactory;

    protected function setUp()
    {
        $this->userFactory = new UserFactory();
    }

    public function testCreate()
    {
        $carrier = Mockery::spy(CarrierInterface::class);

        $user = $this->userFactory->create('msisdn', $carrier, '127.0.0.1', 'token', 'processId' );

        $this->assertEquals('msisdn', $user->getIdentifier());
        $this->assertEquals($carrier, $user->getCarrier());
        $this->assertEquals('127.0.0.1', $user->getIp());
        $this->assertEquals('processId', $user->getIdentificationProcessId());
    }
}
