<?php declare(strict_types=1);


use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\WifiIdentification\Handler\HasCustomPinRequestRules;
use IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerInterface;
use IdentificationBundle\WifiIdentification\WifiIdentSMSSender;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class WifiIdentSMSSenderTest extends TestCase
{
    /** @var WifiIdentSMSSender */
    private $wifiIdentSMSSender;

    /** @var IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerProvider | MockInterface */
    private $handlerProvider;

    /** @var IdentificationBundle\Repository\CarrierRepositoryInterface | MockInterface */
    private $carrierRepository;

    /** @var IdentificationBundle\BillingFramework\Process\PinRequestProcess | MockInterface */
    private $pinRequestProcess;

    /** @var IdentificationBundle\WifiIdentification\Service\MessageComposer | MockInterface */
    private $messageComposer;

    /** @var IdentificationBundle\WifiIdentification\Service\MsisdnCleaner | MockInterface */
    private $cleaner;

    /** @var IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeSaver | MockInterface */
    private $pinCodeSaver;

    /** @var IdentificationBundle\WifiIdentification\Common\RequestProvider | MockInterface */
    private $requestProvider;

    /** @var IdentificationBundle\Identification\Service\IdentificationDataStorage | MockInterface */
    private $dataStorage;

    /** @var IdentificationBundle\Repository\UserRepository | MockInterface */
    private $userRepository;
    private $session;
    private $identificationHandler;

    protected function setUp()
    {
        $this->session               = new Session(new MockArraySessionStorage());
        $this->handlerProvider       = Mockery::spy(IdentificationBundle\WifiIdentification\Handler\WifiIdentificationHandlerProvider::class);
        $this->carrierRepository     = Mockery::spy(IdentificationBundle\Repository\CarrierRepositoryInterface::class);
        $this->pinRequestProcess     = Mockery::spy(IdentificationBundle\BillingFramework\Process\PinRequestProcess::class);
        $this->messageComposer       = Mockery::spy(IdentificationBundle\WifiIdentification\Service\MessageComposer::class);
        $this->cleaner               = Mockery::spy(IdentificationBundle\WifiIdentification\Service\MsisdnCleaner::class);
        $this->pinCodeSaver          = Mockery::spy(IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeSaver::class);
        $this->requestProvider       = Mockery::spy(IdentificationBundle\WifiIdentification\Common\RequestProvider::class);
        $this->dataStorage           = new IdentificationDataStorage($this->session);
        $this->userRepository        = Mockery::spy(IdentificationBundle\Repository\UserRepository::class);
        $this->identificationHandler = Mockery::spy(WifiIdentificationHandlerInterface::class);

        $this->wifiIdentSMSSender = new WifiIdentSMSSender(
            $this->handlerProvider,
            $this->carrierRepository,
            $this->pinRequestProcess,
            $this->messageComposer,
            $this->cleaner,
            $this->pinCodeSaver,
            $this->requestProvider,
            $this->dataStorage,
            $this->userRepository
        );
    }

    public function testExceptionThrownWhenUserExists()
    {
        $this->carrierRepository->allows([
            'findOneByBillingId' => Mockery::spy(\CommonDataBundle\Entity\Interfaces\CarrierInterface::class)
        ]);
        $this->handlerProvider->allows([
            'get' => $this->identificationHandler
        ]);
        $this->identificationHandler->allows([
            'getExistingUser' => Mockery::spy(\IdentificationBundle\Entity\User::class)
        ]);

        $this->expectException(\IdentificationBundle\Identification\Exception\AlreadyIdentifiedException::class);

        $this->wifiIdentSMSSender->sendSMS(0, '1234567890');
    }

    public function testPinRequestIsSend()
    {
        $this->carrierRepository->allows([
            'findOneByBillingId' => Mockery::spy(\CommonDataBundle\Entity\Interfaces\CarrierInterface::class)
        ]);
        $wifiIdentificationHandler = Mockery::spy(
            WifiIdentificationHandlerInterface::class,
            HasCustomPinRequestRules::class
        );
        $this->handlerProvider->allows([
            'get' => $wifiIdentificationHandler
        ]);
        $wifiIdentificationHandler->allows([
            'isSmsSentByBilling' => true
        ]);
        $pinCode = Mockery::spy(\IdentificationBundle\Entity\PinCode::class);
        $this->pinCodeSaver->allows([
            'savePinCode' => $pinCode
        ]);
        $pinCode->allows(['getPin' => 1234567]);

        $this->pinRequestProcess->allows([
            'doPinRequest' => Mockery::spy(\IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult::class)
        ]);

        $this->wifiIdentSMSSender->sendSMS(0, '1234567890');

        $this->assertNotEmpty($this->dataStorage->readPreviousOperationResult('pinRequest'));

        $this->pinRequestProcess->shouldHaveReceived('doPinRequest')->once();
        $wifiIdentificationHandler->shouldHaveReceived('afterSuccessfulPinRequest')->once();
        $wifiIdentificationHandler->shouldHaveReceived('getAdditionalPinRequestParams')->once();

    }

}
