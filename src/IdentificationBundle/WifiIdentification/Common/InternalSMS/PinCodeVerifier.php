<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 18:45
 */

namespace IdentificationBundle\WifiIdentification\Common\InternalSMS;


use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Repository\PinCodeRepository;

class PinCodeVerifier
{
    /**
     * @var PinCodeRepository
     */
    private $pinCodeRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * PinCodeVerifier constructor.
     * @param PinCodeRepository $pinCodeRepository
     */
    public function __construct(PinCodeRepository $pinCodeRepository, EntityManagerInterface $entityManager)
    {
        $this->pinCodeRepository = $pinCodeRepository;
        $this->entityManager     = $entityManager;
    }

    public function verifyPinCode(string $pinCode): bool
    {
        $storedPinCode = $this->pinCodeRepository->getActivePinCode($pinCode);
        if (!empty($storedPinCode)) {
            $this->entityManager->remove($storedPinCode);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }
}