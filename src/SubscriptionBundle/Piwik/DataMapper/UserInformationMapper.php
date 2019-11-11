<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 17:38
 */

namespace SubscriptionBundle\Piwik\DataMapper;


use App\Domain\Service\Carrier\CarrierProvider;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CountryCarrierDetectionBundle\Service\Interfaces\ICountryCarrierDetection;
use IdentificationBundle\Entity\User;
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
     * @var CarrierProvider
     */
    private $carrierProvider;


    /**
     * UserInformationMapper constructor.
     * @param AffiliateStringProvider  $affiliateStringProvider
     * @param UserExtractor            $userExtractor
     * @param ICountryCarrierDetection $carrierDetection
     * @param CarrierResolver          $carrierResolver
     * @param DeviceDataProvider       $deviceDataProvider
     * @param CarrierProvider          $carrierProvider
     */
    public function __construct(
        AffiliateStringProvider $affiliateStringProvider,
        UserExtractor $userExtractor,
        ICountryCarrierDetection $carrierDetection,
        CarrierResolver $carrierResolver,
        DeviceDataProvider $deviceDataProvider,
        CarrierProvider $carrierProvider
    )
    {
        $this->affiliateStringProvider = $affiliateStringProvider;
        $this->userExtractor           = $userExtractor;
        $this->carrierDetection        = $carrierDetection;
        $this->carrierResolver         = $carrierResolver;
        $this->deviceDataProvider      = $deviceDataProvider;
        $this->carrierProvider         = $carrierProvider;
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
            $carrierId  = $this->carrierResolver->resolveCarrierByISP((string)$isp);

            /** @var CarrierInterface $carrier */
            $carrier   = $this->carrierProvider->fetchCarrierIfNeeded($carrierId);
            $country   = $carrier ? $carrier->getCountryCode() : '';

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