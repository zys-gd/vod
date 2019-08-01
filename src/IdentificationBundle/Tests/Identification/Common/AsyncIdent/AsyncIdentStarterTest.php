<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.01.19
 * Time: 16:48
 */

namespace IdentificationBundle\Tests\Identification\Common\AsyncIdent;


use IdentificationBundle\Identification\Common\Async\AsyncIdentStarter;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\Session\SessionStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AsyncIdentStarterTest extends \PHPUnit\Framework\TestCase
{
    private $session;
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;
    /**
     * @var AsyncIdentStarter
     */
    private $asyncIdentStarter;

    protected function setUp()
    {

        $this->session = new Session(new MockArraySessionStorage());

        $this->dataStorage = new IdentificationDataStorage(new SessionStorage($this->session));

        $this->asyncIdentStarter = new AsyncIdentStarter($this->dataStorage);


        parent::setUp();
    }


    public function testStart()
    {

        $result = $this->asyncIdentStarter->start(new ProcessResult(null, null, null, 'redirectUrl'), 'token');

        $this->assertEquals(
            $result->getTargetUrl(),
            'redirectUrl',
            'url is not correctly set'
        );

        $this->assertEquals(
            $this->dataStorage->getRedirectIdentToken(),
            'token',
            'token is not set'
        );


    }


}