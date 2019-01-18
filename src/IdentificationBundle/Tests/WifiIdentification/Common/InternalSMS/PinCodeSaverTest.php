<?php declare(strict_types=1);


use ExtrasBundle\Testing\Core\AbstractFunctionalTest;
use IdentificationBundle\WifiIdentification\Common\InternalSMS\PinCodeSaver;
use Mockery\MockInterface;

class PinCodeSaverTest extends AbstractFunctionalTest
{
    /** @var PinCodeSaver */
    private $pinCodeSaver;

    /** @var Doctrine\ORM\EntityManagerInterface | MockInterface */
    private $entityManager;

    protected static function getKernelClass()
    {
        return VODKernel::class;
    }

    protected function initializeServices(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->pinCodeSaver  = new PinCodeSaver(
            $this->entityManager
        );
    }

    public function testPinCodeSaved()
    {
        $pinCode = $this->pinCodeSaver->savePinCode('1234567');

        $updatedPinCode = $this->entityManager->find(\IdentificationBundle\Entity\PinCode::class, $pinCode->getId());

        $this->assertEquals('1234567', $updatedPinCode->getPin());


    }

    protected function getFixturesListLoadedForEachTest(): array
    {
        return [];
    }

    protected function configureWebClientClientContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        // TODO: Implement configureWebClientClientContainer() method.
    }
}
