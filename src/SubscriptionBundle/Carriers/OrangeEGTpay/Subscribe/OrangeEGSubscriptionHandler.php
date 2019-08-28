<?php

namespace SubscriptionBundle\Carriers\OrangeEGTpay\Subscribe;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use App\Domain\Repository\CarrierRepository;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\RouteProvider;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OrangeEGSubscriptionHandler
 */
class OrangeEGSubscriptionHandler implements SubscriptionHandlerInterface, HasConsentPageFlow, HasCustomAffiliateTrackingRules
{
    /**
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * VodafoneEGSubscriptionHandler constructor
     *
     * @param LocalExtractor            $localExtractor
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     * @param RouteProvider             $routeProvider
     * @param RouterInterface           $router* @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     * @param CarrierRepository              $carrierRepository
     */
    public function __construct(
        LocalExtractor $localExtractor,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        RouteProvider $routeProvider,
        RouterInterface $router,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking,
        CarrierRepository $carrierRepository
    )
    {
        $this->localExtractor            = $localExtractor;
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
        $this->routeProvider             = $routeProvider;
        $this->router                    = $router;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $user): array
    {
        $defaultLang = $user->getCarrier()->getDefaultLanguage();
        $lang = empty($defaultLang) ? $this->localExtractor->getLocal() : $defaultLang->getCode();

        $data = [
            'url_id'       => $user->getShortUrlId(),
            'lang'         => $lang,
            'redirect_url' => $this->routeProvider->getLinkToHomepage()
        ];

        if ((bool)$this->wifiIdentificationDataStorage->isWifiFlow()) {
            /** @var PinVerifyResult $pinVerifyResult */
            $pinVerifyResult = $this->wifiIdentificationDataStorage->getPinVerifyResult();
            $rawData = $pinVerifyResult->getRawData();

            $data['subscription_contract_id'] = $rawData['subscription_contract_id'];
        }

        return $data;
    }

    /**
     * @param SubscribingProcessException $exception
     *
     * @return Response
     */
    public function getSubscriptionErrorResponse(SubscribingProcessException $exception): Response
    {
        $billingData = $exception->getBillingData();
        $failReason = $billingData
            ? $billingData->provider_fields->fail_reason
            : null;

        switch ($failReason) {
            case SubscribingProcessException::FAIL_REASON_NOT_ENOUGH_CREDIT:
                $redirectUrl = $this->routeProvider->getLinkToHomepage(['err_handle' => 'not_enough_credit']);
                break;
            case SubscribingProcessException::FAIL_REASON_BLACKLISTED:
                $redirectUrl = $this->router->generate('blacklisted_user');
                break;
            default:
                $redirectUrl = $this->routeProvider->getLinkToWifiFlowPage();
                break;
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param ProcessResult     $result
     * @param CampaignInterface $campaign
     *
     * @return bool
     */
    public function isAffiliateTrackedForSub(ProcessResult $result, CampaignInterface $campaign): bool
    {
        $carrier = $this->carrierRepository->findOneByBillingId(ID::ORANGE_EGYPT_TPAY);

        $isSuccess = $result->isFailedOrSuccessful() && $result->isFinal();
        $isZeroCreditsSub = $this
            ->zeroCreditSubscriptionChecking
            ->isZeroCreditAvailable(ID::ORANGE_EGYPT_TPAY, $campaign);

        if ($isZeroCreditsSub) {
            return $isSuccess && $carrier->getTrackAffiliateOnZeroCreditSub();
        }

        return $isSuccess;
    }

    /**
     * @param ProcessResult $result
     *
     * @return bool
     */
    public function isAffiliateTrackedForResub(ProcessResult $result): bool
    {
        return false;
    }

    /**
     * @param Subscription  $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void
    {

    }
}