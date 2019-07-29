<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 16:55
 */

namespace IdentificationBundle\Tests\Identification\Common\AsyncIdent;

use IdentificationBundle\BillingFramework\Data\DataProvider;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Common\Async\AsyncIdentFinisher;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Identification\Service\Session\SessionStorage;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Mockery;
use PHPUnit\Framework\TestCase;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AsyncIdentFinisherTest extends TestCase
{
    private $session;
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var AsyncIdentFinisher
     */
    private $asyncIdentStarter;
    private $userRepository;
    private $billingDataProvider;


    protected function setUp()
    {


        $this->session             = new Session(new MockArraySessionStorage());
        $this->dataStorage         = new IdentificationDataStorage(new SessionStorage($this->session));
        $this->userRepository      = Mockery::spy(UserRepository::class);
        $this->billingDataProvider = Mockery::spy(DataProvider::class);
        $this->asyncIdentStarter   = new AsyncIdentFinisher(
            $this->dataStorage,
            $this->userRepository,
            new IdentificationStatus($this->dataStorage, new WifiIdentificationDataStorage(new SessionStorage($this->session))),
            Mockery::spy(IdentificationHandlerProvider::class),
            $this->billingDataProvider
        );

        parent::setUp();
    }


    public function testTokenIsStored()
    {
        $this->dataStorage->setRedirectIdentToken('token');

        $user = Mockery::spy(User::class);
        $user->allows([
            'getBillingCarrierId' => 0
        ]);

        $this->userRepository->allows([
            'findOneByIdentificationToken' => $user
        ]);

        $this->billingDataProvider->allows([
            'getProcessData' => new ProcessResult(null, null, null, null, ProcessResult::STATUS_SUCCESSFUL)
        ]);

        $this->asyncIdentStarter->finish();


        $this->assertArraySubset(
            $this->dataStorage->getIdentificationData(),
            ['identification_token' => 'token'],
            'token is not set'
        );


    }
}
