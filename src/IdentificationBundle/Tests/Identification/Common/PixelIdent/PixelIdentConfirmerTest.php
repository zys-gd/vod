<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 17:21
 */

namespace IdentificationBundle\Tests\Identification\Common\PixelIdent;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\BillingFramework\Data\DataProvider;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Common\Pixel\PixelIdentConfirmer;
use IdentificationBundle\Identification\Common\PostPaidHandler;
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Handler\CommonFlow\HasCustomPixelIdent;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Identification\Service\Session\SessionStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Mockery;
use PHPUnit\Framework\TestCase;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class PixelIdentConfirmerTest extends TestCase
{
    /**
     * @var PixelIdentConfirmer
     */
    private $pixelIdentConfirmer;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    private $billingDataProvider;
    private $carrierRepository;
    private $tokenGenerator;
    private $userRepository;
    private $identificationHandlerProvider;
    private $identificationHandler;

    protected function setUp()
    {
        $this->session                       = new Session(new MockArraySessionStorage());
        $sessionStorage                      = new SessionStorage($this->session);
        $this->dataStorage                   = new IdentificationDataStorage($sessionStorage);
        $this->billingDataProvider           = Mockery::spy(DataProvider::class);
        $this->carrierRepository             = Mockery::spy(CarrierRepositoryInterface::class);
        $this->tokenGenerator                = Mockery::spy(TokenGenerator::class);
        $this->userRepository                = Mockery::spy(UserRepository::class);
        $this->identificationHandlerProvider = Mockery::spy(IdentificationHandlerProvider::class);
        $this->identificationHandler         = Mockery::spy(
            IdentificationHandlerInterface::class,
            HasCommonFlow::class,
            HasCustomPixelIdent::class
        );
        $this->pixelIdentConfirmer           = new PixelIdentConfirmer(
            Mockery::spy(EntityManagerInterface::class),
            Mockery::spy(UserFactory::class),
            $this->carrierRepository,
            $this->billingDataProvider,
            $this->identificationHandlerProvider,
            new IdentificationStatus($this->dataStorage, new WifiIdentificationDataStorage($sessionStorage)),
            $this->tokenGenerator,
            $this->userRepository,
            Mockery::spy(PostPaidHandler::class)
        );

        parent::setUp();
    }

    public function testDataIsSetWhenNoUser()
    {
        $processResult = Mockery::spy(ProcessResult::class);

        $processResult->allows([
            'isSuccessful'    => true,
            'getClientFields' => ['user_ip' => '10.0.0.1'],
            'getProviderUser' => 1234567
        ]);

        $this->billingDataProvider->allows([
            'getProcessData' => $processResult
        ]);

        $this->carrierRepository->allows([
            'findOneByBillingId' => Mockery::spy(CarrierInterface::class)
        ]);

        $this->identificationHandlerProvider->allows(['get' => $this->identificationHandler]);
        $this->identificationHandler->allows(['getExistingUser' => null]);
        $this->tokenGenerator->allows(['generateToken' => 'token']);


        $this->pixelIdentConfirmer->confirmIdent('123456', 0, Mockery::spy(DeviceData::class));

        $this->assertArraySubset(
            ['identification_token' => 'token'],
            $this->dataStorage->getIdentificationData(),
            'ident are not finished'
        );
    }

    public function testDataIsSetWhenUserExists()
    {
        $processResult = Mockery::spy(ProcessResult::class);

        $processResult->allows([
            'isSuccessful'    => true,
            'getClientFields' => ['user_ip' => '10.0.0.1'],
            'getProviderUser' => 1234567
        ]);

        $this->billingDataProvider->allows([
            'getProcessData' => $processResult
        ]);

        $this->carrierRepository->allows([
            'findOneByBillingId' => Mockery::spy(CarrierInterface::class)
        ]);


        $user = Mockery::spy(User::class);

        $user->allows(['getIdentificationToken' => 555555]);

        $this->identificationHandlerProvider->allows(['get' => $this->identificationHandler]);
        $this->identificationHandler->allows(['getExistingUser' => $user]);

        $this->pixelIdentConfirmer->confirmIdent('123456', 0, Mockery::spy(DeviceData::class));

        $this->assertArraySubset(
            ['identification_token' => '555555'],
            $this->dataStorage->getIdentificationData(),
            'ident are not finished'
        );
    }

}