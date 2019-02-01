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
use IdentificationBundle\Identification\Common\Pixel\PixelIdentConfirmer;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
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

    protected function setUp()
    {

        $this->session             = new Session(new MockArraySessionStorage());
        $this->dataStorage         = new IdentificationDataStorage($this->session);
        $this->billingDataProvider = Mockery::spy(DataProvider::class);
        $this->carrierRepository   = Mockery::spy(CarrierRepositoryInterface::class);
        $this->tokenGenerator      = Mockery::spy(TokenGenerator::class);
        $this->pixelIdentConfirmer = new PixelIdentConfirmer(
            Mockery::spy(EntityManagerInterface::class),
            Mockery::spy(UserFactory::class),
            $this->carrierRepository,
            $this->billingDataProvider,
            Mockery::spy(IdentificationHandlerProvider::class),
            new IdentificationStatus($this->dataStorage),
            $this->tokenGenerator,
            Mockery::spy(UserRepository::class)
        );

        parent::setUp();
    }

    public function testDataIsSet()
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


        $this->tokenGenerator->allows(['generateToken' => 'token']);

        $this->pixelIdentConfirmer->confirmIdent('123456', 0);

        $this->assertArraySubset(
            ['identification_token' => 'token'],
            $this->dataStorage->readIdentificationData(),
            'ident are not finished'
        );
    }

}