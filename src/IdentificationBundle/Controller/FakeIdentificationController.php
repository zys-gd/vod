<?php

namespace IdentificationBundle\Controller;

use CountryCarrierDetectionBundle\Service\Interfaces\ICountryCarrierDetection;
use Doctrine\ORM\EntityManager;
use IdentificationBundle\Identification\Identifier;
use IdentificationBundle\Identification\Service\ISPResolver;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Identification\Service\UserFactory;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;

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
     * @var \IdentificationBundle\Identification\Service\ISPResolver
     */
    private $ISPResolver;
    /**
     * @var \IdentificationBundle\Identification\Service\UserFactory
     */
    private $userFactory;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * FakeIdentificationController constructor.
     *
     * @param ICountryCarrierDetection                                 $carrierDetection
     * @param CarrierRepositoryInterface                               $carrierRepository
     * @param UserRepository                                           $userRepository
     * @param \IdentificationBundle\Identification\Service\ISPResolver $ISPResolver
     * @param Identifier                                               $identifier
     * @param TokenGenerator                                           $generator
     * @param UserFactory                                              $userFactory
     * @param EntityManager                                            $entityManager
     * @param Router                                                   $router
     */
    public function __construct(ICountryCarrierDetection $carrierDetection,
        CarrierRepositoryInterface $carrierRepository,
        UserRepository $userRepository,
        ISPResolver $ISPResolver,
        Identifier $identifier,
        TokenGenerator $generator,
        UserFactory $userFactory,
        EntityManager $entityManager,
        Router $router)
    {
        $this->carrierDetection = $carrierDetection;
        $this->identifier = $identifier;
        $this->tokenGenerator = $generator;
        $this->carrierRepository = $carrierRepository;
        $this->ISPResolver = $ISPResolver;
        $this->userFactory = $userFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/fake/identify",name="fake_identify")
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function fakeIdentifyAction(Request $request)
    {
        $ipAddress = $request->get('ip', $request->getClientIp());
        $msisdn = $request->get('msisdn', 'fake');
        $force = $request->get('force', true);

        $session = $request->getSession();
        $session->clear();

        if($user = $this->userRepository->findOneBy(['ip' => $ipAddress])) {
            $session->set('identification_data', ['identification_token' => $user->getIdentificationToken()]);
            $ispDetectionData = [
                'isp_name' => $user->getCarrier()->getIsp(),
                'carrier_id' => $user->getBillingCarrierId(),
            ];
            $session->set('isp_detection_data', $ispDetectionData);
        }
        else {
            // Get carrier
            $carrierISP = $this->carrierDetection->getCarrier($ipAddress);
            $billingCarrierId = null;
            if ($carrierISP) {
                $billingCarrierId = $this->resolveISP($carrierISP);
            }
            // Set session data
            $ispDetectionData = [
                'isp_name' => $carrierISP,
                'carrier_id' => $billingCarrierId,
            ];
            $session->set('isp_detection_data', $ispDetectionData);

            $token = $this->tokenGenerator->generateToken();
            $session->set('identification_data', ['identification_token' => $token]);

            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            $user = $this->userFactory->create($msisdn, $carrier, $ipAddress, $token);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        // return new RedirectResponse($this->router->generate('homepage'));
        return new Response();
    }

    /**
     * @param $carrierISP
     *
     * @return int|null
     */
    private function resolveISP(string $carrierISP): ?int
    {
        $carriers = $this->carrierRepository->findAllCarriers();
        foreach ($carriers as $carrier) {
            if ($this->ISPResolver->isISPMatches($carrierISP, $carrier)) {
                return $carrier->getBillingCarrierId();
                break;
            }
        }
        return null;
    }
}