<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 17:38
 */

namespace SubscriptionBundle\Piwik\DataMapper;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CountryCarrierDetectionBundle\Service\Interfaces\ICountryCarrierDetection;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\DTO\DeviceData;
use IdentificationBundle\Identification\Service\CarrierResolver;
use IdentificationBundle\Identification\Service\DeviceDataProvider;
use IdentificationBundle\User\Service\UserExtractor;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\DTO\UserInformation;
use SubscriptionBundle\Piwik\Service\AffiliateStringProvider;
use Symfony\Component\HttpFoundation\Request;

class UserInformationMapper
{
    /**
     * @var AffiliateStringProvider
     */
    private $affiliateStringProvider;
    /**
     * @var UserExtractor
     */
    private $userExtractor;
    /**
     * @var ICountryCarrierDetection
     */
    private $carrierDetection;
    /**
     * @var CarrierResolver
     */
    private $carrierResolver;
    /**
     * @var DeviceDataProvider
     */
    private $deviceDataProvider;


    /**
     * UserInformationMapper constructor.
     * @param AffiliateStringProvider  $affiliateStringProvider
     * @param UserExtractor            $userExtractor
     * @param ICountryCarrierDetection $carrierDetection
     * @param CarrierResolver          $carrierResolver
     * @param DeviceDataProvider       $deviceDataProvider
     */
    public function __construct(
        AffiliateStringProvider $affiliateStringProvider,
        UserExtractor $userExtractor,
        ICountryCarrierDetection $carrierDetection,
        CarrierResolver $carrierResolver,
        DeviceDataProvider $deviceDataProvider
    )
    {
        $this->affiliateStringProvider = $affiliateStringProvider;
        $this->userExtractor           = $userExtractor;
        $this->carrierDetection        = $carrierDetection;
        $this->carrierResolver         = $carrierResolver;
        $this->deviceDataProvider      = $deviceDataProvider;
    }

    public function mapUserInformation(int $providerId, User $user, Subscription $subscription): UserInformation
    {
        $affiliateString = $this->affiliateStringProvider->getAffiliateString($subscription);

        return new UserInformation(
            $user->getCountry(),
            $user->getIp(),
            // Kinda risky, because im not sure if we always have userConnection type.
            // We can use $this->maxMindIpInfo->getConnectionType() instead but what about renews etc?
            (string)$user->getConnectionType(),
            $user->getIdentifier(),
            (int)$user->getBillingCarrierId(),
            $providerId,
            0,
            0,
            $affiliateString
        );
    }

    public function mapFromRequest(Request $request): UserInformation
    {

        if ($user = $this->userExtractor->getUserFromRequest($request)) {
            return new UserInformation(
                $user->getCountry(),
                $user->getIp(),
                (string)$user->getConnectionType(),
                $user->getIdentifier(),
                (int)$user->getBillingCarrierId(),
                0,
                0,
                0,
                ''
            );
        } else {

            $deviceData = $this->deviceDataProvider->get($request);
            $isp        = $this->carrierDetection->getCarrier($request->getClientIp());
            /** @var CarrierInterface $carrier */
            $carrier   = $this->carrierResolver->resolveCarrierByISP($isp);
            $country   = $carrier ? $carrier->getCountryCode() : '';
            $carrierId = $carrier ? $carrier->getBillingCarrierId() : 0;

            return new UserInformation(
                $country,
                $request->getClientIp(),
                (string)$deviceData->getConnectionType(),
                '',
                (int)$carrierId,
                $carrierId,
                0,
                0,
                ''
            );
        }

    }
}