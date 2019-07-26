<?php

namespace IdentificationBundle\Identification\Service;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\DeviceData;
use SubscriptionBundle\Subscription\Notification\Common\ShortUrlHashGenerator;

/**
 * Class UserFactory
 */
class UserFactory
{
    /**
     * @var ShortUrlHashGenerator
     */
    private $shortUrlHashGenerator;

    /**
     * UserFactory constructor
     *
     * @param ShortUrlHashGenerator $shortUrlHashGenerator
     */
    public function __construct(ShortUrlHashGenerator $shortUrlHashGenerator)
    {
        $this->shortUrlHashGenerator = $shortUrlHashGenerator;
    }

    /**
     * @param string $msisdn
     * @param CarrierInterface $carrier
     * @param string $ip
     * @param string|null $identificationToken
     * @param string|null $processId
     * @param DeviceData|null $deviceData
     *
     * @return User
     *
     * @throws \Exception
     */
    public function create(
        string $msisdn,
        CarrierInterface $carrier,
        string $ip,
        string $identificationToken = null,
        string $processId = null,
        DeviceData $deviceData = null
    ): User
    {
        $user = new User(UuidGenerator::generate());

        $user->setIdentifier($msisdn);
        $user->setCarrier($carrier);
        $user->setCountry($carrier->getCountryCode());
        $user->setIp($ip);
        $user->setShortUrlId($this->shortUrlHashGenerator->generate());

        if ($processId) {
            $user->setIdentificationProcessId($processId);
        }

        if ($identificationToken) {
            $user->setIdentificationToken($identificationToken);
        }

        if ($deviceData) {
            $user->setDeviceManufacturer($deviceData->getDeviceManufacturer());
            $user->setDeviceModel($deviceData->getDeviceModel());
            $user->setConnectionType($deviceData->getConnectionType());
            $user->setIdentificationUrl($deviceData->getIdentificationUrl());
        }

        return $user;
    }
}