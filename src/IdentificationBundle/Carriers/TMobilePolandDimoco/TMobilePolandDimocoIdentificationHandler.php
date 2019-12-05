<?php

namespace IdentificationBundle\Carriers\TMobilePolandDimoco;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TMobilePolandDimocoIdentificationHandler
 */
class TMobilePolandDimocoIdentificationHandler implements IdentificationHandlerInterface, HasCommonFlow
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * TMobilePolandDimocoIdentificationHandler constructor
     *
     * @param UserRepository  $userRepository
     * @param RouterInterface $router
     */
    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TMOBILE_POLAND_DIMOCO;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request): array
    {
        $successUrl = $this->router->generate('subscription.subscribe', [], RouterInterface::ABSOLUTE_URL);

        return [
            'redirect_url' => $this
                ->router
                ->generate('wait_for_callback', ['successUrl' => $successUrl], RouterInterface::ABSOLUTE_URL)
        ];
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function getExistingUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }
}