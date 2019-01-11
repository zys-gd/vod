<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 18:45
 */

namespace IdentificationBundle\Service\Action\WifiIdentification\Common\InternalSMS;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\PinCode;

class PinCodeSaver
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * PinCodeSaver constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function savePinCode(string $pinCode): PinCode
    {
        $pinCodeEntity = new PinCode();
        $pinCodeEntity->setPin($pinCode);
        $this->entityManager->persist($pinCodeEntity);
        $this->entityManager->flush();

        return $pinCodeEntity;
    }
}