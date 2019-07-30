<?php

namespace SubscriptionBundle\Carriers\VodafoneEGTpay\Subscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\BillingFramework\Process\DTO\PinVerifyResult;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class VodafoneEGTpaySubscriptionHandler
 */
class VodafoneEGSubscriptionHandler implements SubscriptionHandlerInterface, HasConsentPageFlow
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
     * @var RouterInterface
     */
    private $router;

    /**
     * VodafoneEGSubscriptionHandler constructor
     *
     * @param LocalExtractor $localExtractor
     * @param WifiIdentificationDataStorage $wifiIdentificationDataStorage
     * @param RouterInterface $router
     */
    public function __construct(
        LocalExtractor $localExtractor,
        WifiIdentificationDataStorage $wifiIdentificationDataStorage,
        RouterInterface $router
    ) {
        $this->localExtractor = $localExtractor;
        $this->wifiIdentificationDataStorage = $wifiIdentificationDataStorage;
        $this->router = $router;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::VODAFONE_EGYPT_TPAY;
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $user): array
    {
        $data = [
            'url_id' => $user->getShortUrlId(),
            'lang' => $this->localExtractor->getLocal(),
            'redirect_url' => $this->router->generate('index', [], RouterInterface::ABSOLUTE_URL)
        ];

        if ((bool) $this->wifiIdentificationDataStorage->isWifiFlow()) {
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
        $failReason = $billingData->provider_fields->fail_reason;

        switch ($failReason) {
            case SubscribingProcessException::FAIL_REASON_NOT_ENOUGH_CREDIT:
                $redirectUrl = $this->router->generate('index', ['err_handle' => 'not_enough_credit']);
                break;
            case SubscribingProcessException::FAIL_REASON_BLACKLISTED:
                $redirectUrl = $this->router->generate('blacklisted_user');
                break;
            default:
                $redirectUrl = $this->router->generate('whoops');
                break;
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param Subscription $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void
    {

    }
}