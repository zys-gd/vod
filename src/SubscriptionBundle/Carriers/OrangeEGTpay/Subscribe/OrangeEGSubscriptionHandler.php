<?php

namespace SubscriptionBundle\Carriers\OrangeEGTpay\Subscribe;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\RouteProvider;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OrangeEGSubscriptionHandler
 */
class OrangeEGSubscriptionHandler implements SubscriptionHandlerInterface, HasConsentPageFlow
{
    /**
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;
    /**
     * @var RouteProvider
     */
    private $routeProvider;
    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * VodafoneEGSubscriptionHandler constructor
     *
     * @param LocalExtractor            $localExtractor
     * @param IdentificationDataStorage $identificationDataStorage
     * @param RouteProvider             $routeProvider
     * @param RouterInterface           $router
     */
    public function __construct(
        LocalExtractor $localExtractor,
        IdentificationDataStorage $identificationDataStorage,
        RouteProvider $routeProvider,
        RouterInterface $router
    )
    {
        $this->localExtractor            = $localExtractor;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->routeProvider             = $routeProvider;
        $this->router                    = $router;
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
        $data = [
            'url_id'       => $user->getShortUrlId(),
            'lang'         => $this->localExtractor->getLocal(),
            'redirect_url' => $this->routeProvider->getLinkToHomepage()
        ];

        if ((bool)$this->identificationDataStorage->readValue('is_wifi_flow')) {
            $data['subscription_contract_id'] = $this->identificationDataStorage->readValue('subscription_contract_id');
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

        $failReason  = $billingData->provider_fields->fail_reason;
        $redirectUrl = $this->routeProvider->getLinkToWifiFlowPage();

        switch ($failReason) {
            case SubscribingProcessException::FAIL_REASON_NOT_ENOUGH_CREDIT:
                $redirectUrl = $this->routeProvider->getLinkToHomepage(['err_handle' => 'not_enough_credit']);
                break;
            case SubscribingProcessException::FAIL_REASON_BLACKLISTED:
                $redirectUrl = $this->router->generate('blacklisted_user');
                break;
            default:
                break;
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param Subscription  $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void
    {

    }
}