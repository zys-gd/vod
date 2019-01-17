<?php declare(strict_types=1);


use IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeVerifier;
use Mockery\MockInterface;

class PinCodeVerifierTest extends \PHPUnit\Framework\TestCase
{
    /** @var PinCodeVerifier */
    private $pinCodeVerifier;

    /** @var IdentificationBundle\Repository\PinCodeRepository | MockInterface */
    private $pinCodeRepository;

    /** @var \Doctrine\ORM\EntityManagerInterface | MockInterface */
    private $entityManager;


    protected static function getKernelClass()
    {
        return VODKernel::class;
    }

    public function testVerifyPinCode()
    {
        $this->pinCodeRepository->allows(['getActivePinCode' => null]);

        $result = $this->pinCodeVerifier->verifyPinCode('1234567');

        $this->assertFalse($result);
    }

    public function testIsUsedPinCodeDeleted()
    {
        $pinCode = new \IdentificationBundle\Entity\PinCode();
        $this->pinCodeRepository->allows(['getActivePinCode' => $pinCode]);
        $result = $this->pinCodeVerifier->verifyPinCode('1234567');

        $this->assertTrue($result);

        $this->entityManager->shouldHaveReceived('remove')->with($pinCode);
    }

    protected function setUp()
    {
        $this->pinCodeRepository = Mockery::spy(IdentificationBundle\Repository\PinCodeRepository::class);
        $this->entityManager     = Mockery::spy(Doctrine\ORM\EntityManagerInterface::class);
        $this->pinCodeVerifier   = new PinCodeVerifier(
            $this->pinCodeRepository,
            $this->entityManager
        );

    }


}
