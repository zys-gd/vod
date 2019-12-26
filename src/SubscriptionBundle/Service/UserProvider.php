<?php


namespace SubscriptionBundle\Service;


use App\Domain\Entity\Carrier;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Service\TokenGenerator;
use IdentificationBundle\Repository\UserRepository;
use IdentificationBundle\User\Service\UserFactory;

class UserProvider
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var TokenGenerator
     */
    private $generator;

    /**
     * UserProvider constructor.
     *
     * @param UserRepository $userRepository
     * @param UserFactory    $userFactory
     * @param TokenGenerator $generator
     */
    public function __construct(UserRepository $userRepository, UserFactory $userFactory, TokenGenerator $generator)
    {
        $this->userRepository = $userRepository;
        $this->userFactory    = $userFactory;
        $this->generator      = $generator;
    }

    /**
     * @param string          $msisdn
     * @param Carrier         $carrier
     * @param int|null        $billingProcessId
     * @param string          $ip
     * @param DeviceData|null $deviceDataProvider
     *
     * @return User
     * @throws \Exception
     */
    public function obtainUser(
        string $msisdn,
        Carrier $carrier,
        int $billingProcessId = null,
        string $ip = '',
        DeviceData $deviceDataProvider = null
    ): User
    {
        $user = $this->userRepository->findOneByMsisdn($msisdn);
        if (!$user) {
            $newToken = $this->generator->generateToken();
            $user     = $this->userFactory->create(
                $msisdn,
                $carrier,
                $ip,
                $newToken,
                $billingProcessId,
                $deviceDataProvider
            );
        }

        return $user;
    }
}