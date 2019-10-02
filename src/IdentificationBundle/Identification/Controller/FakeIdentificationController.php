<?php

namespace IdentificationBundle\Identification\Controller;

use CountryCarrierDetectionBundle\Service\Interfaces\ICountryCarrierDetection;
use Doctrine\ORM\EntityManager;
use IdentificationBundle\Entity\TestUser;
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Exception\MissingCarrierException;
use IdentificationBundle\Identification\Identifier;
use IdentificationBundle\Identification\Service\ISPResolver;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\TestUserRepository;
use IdentificationBundle\User\Service\UserFactory;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FakeIdentificationController
 */
class FakeIdentificationController extends AbstractController
{
    /**
     * @var Identifier
     */
    private $identifier;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var ICountryCarrierDetection
     */
    private $carrierDetection;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var ISPResolver
     */
    private $ISPResolver;
    /**
     * @var \IdentificationBundle\User\Service\UserFactory
     */
    private $userFactory;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var TestUserRepository
     */
    private $testUserRepository;

    /**
     * FakeIdentificationController constructor.
     *
     * @param ICountryCarrierDetection                       $carrierDetection
     * @param CarrierRepositoryInterface                     $carrierRepository
     * @param UserRepository                                 $userRepository
     * @param ISPResolver                                    $ISPResolver
     * @param Identifier                                     $identifier
     * @param TokenGenerator                                 $generator
     * @param \IdentificationBundle\User\Service\UserFactory $userFactory
     * @param EntityManager                                  $entityManager
     * @param RouteProvider                                  $routeProvider
     * @param IdentificationDataStorage                      $identificationDataStorage
     * @param TestUserRepository                             $testUserRepository
     */
    public function __construct(
        ICountryCarrierDetection $carrierDetection,
        CarrierRepositoryInterface $carrierRepository,
        UserRepository $userRepository,
        ISPResolver $ISPResolver,
        Identifier $identifier,
        TokenGenerator $generator,
        UserFactory $userFactory,
        EntityManager $entityManager,
        RouteProvider $routeProvider,
        IdentificationDataStorage $identificationDataStorage,
        TestUserRepository $testUserRepository
    )
    {
        $this->carrierDetection          = $carrierDetection;
        $this->identifier                = $identifier;
        $this->tokenGenerator            = $generator;
        $this->carrierRepository         = $carrierRepository;
        $this->ISPResolver               = $ISPResolver;
        $this->userFactory               = $userFactory;
        $this->entityManager             = $entityManager;
        $this->userRepository            = $userRepository;
        $this->routeProvider             = $routeProvider;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->testUserRepository        = $testUserRepository;
    }

    /**
     * @Route("/identify/fake",name="fake_identify")
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function fakeIdentifyAction(Request $request, DeviceData $deviceData)
    {
        $ipAddress        = $request->get('ip', $request->getClientIp());
        $msisdn           = $request->get('msisdn', 'fake');
        $billingCarrierId = $request->get('carrierId', null);

        $session = $request->getSession();
        $session->clear();

        $user = $this->userRepository->findOneByMsisdn($msisdn);

        if ($user) {
            $this->identificationDataStorage->setIdentificationToken($user->getIdentificationToken());
            $this->identificationDataStorage->setCarrierId($user->getBillingCarrierId());
        } else {

            if (!$billingCarrierId) {
                $carrierISP       = $this->carrierDetection->getCarrier($ipAddress);
                $billingCarrierId = null;

                if ($carrierISP) {
                    try {
                        $billingCarrierId = $this->resolveISP($carrierISP);
                    } catch (MissingCarrierException $exception) {
                        throw $exception;
                    }
                }
            }

            $token = $this->tokenGenerator->generateToken();

            $this->identificationDataStorage->setCarrierId($billingCarrierId);
            $this->identificationDataStorage->setIdentificationToken($token);

            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            $user    = $this->userFactory->create($msisdn, $carrier, $ipAddress, $token, null, $deviceData);

            $this->entityManager->persist($user);


            /** @var TestUser $testUser */
            $testUser = $this->testUserRepository->findOneBy([
                'userIdentifier' => $msisdn,
                'carrier'        => $carrier
            ]);
            if ($testUser) {
                $testUser->setLastTimeUsedAt(new \DateTimeImmutable());
            }

            $this->entityManager->flush();
        }

        return new RedirectResponse($this->routeProvider->getLinkToHomepage());
    }

    /**
     * @Route("/identify/fake/reset",name="reset_fake_identify")
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function resetFakeIdentifyAction(Request $request)
    {
        $session = $request->getSession();
        $session->clear();

        return new RedirectResponse($this->routeProvider->getLinkToHomepage());
    }

    /**
     * @param $carrierISP
     *
     * @return int|null
     */
    private function resolveISP(string $carrierISP): ?int
    {
        $carriers = $this->carrierRepository->findEnabledCarriers();

        foreach ($carriers as $carrier) {
            if ($this->ISPResolver->isISPMatches($carrierISP, $carrier)) {
                return $carrier->getBillingCarrierId();
                break;
            }
        }

        throw new MissingCarrierException('Carrier not found');
    }
}