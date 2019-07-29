<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.05.18
 * Time: 14:49
 */

namespace SubscriptionBundle\Carriers\OrangeTN\Subscribe;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Common\RedirectUrlNullifier;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class OrangeTNHandler implements SubscriptionHandlerInterface, HasCustomResponses, HasCommonFlow
{
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionProvider;

    /**
     * @var RedirectUrlNullifier
     */
    private $redirectUrlNullifier;
    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * OrangeTunisiaHandler constructor.
     *
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionExtractor $subscriptionProvider
     * @param RedirectUrlNullifier                                          $redirectUrlNullifier
     */
    public function __construct(
        SubscriptionExtractor $subscriptionProvider,
        RedirectUrlNullifier $redirectUrlNullifier,
        RouterInterface $router
    )
    {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->redirectUrlNullifier = $redirectUrlNullifier;
        $this->router               = $router;
    }

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() == ID::ORANGE_TUNISIA;
    }

    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        // We need subscription_contract for WiFi Flow (with send sms verification)
        // And dont need this for 3G one.
        if ($contractId = $request->get('subscription_contract_id')) {
            return [
                'subscription_contract_id' => $contractId,
            ];
        } else {
            return [];
        }
    }

    public function createResponseForSuccessfulSubscribe(Request $request, User $User, Subscription $subscription)
    {
        $jsRequest = $request->get('is_ajax_request', null);

        if ($redirectUrl = $subscription->getRedirectUrl()) {
            $this->redirectUrlNullifier->processSubscriptionAndSave($subscription);
        } else {
            $redirectUrl = $this->router->generate('homepage');

        }

        if ($jsRequest) {
            /** Should be fixed soon, cause it is general case not only for Airtel */
            return new JsonResponse(['data' => ['airtel_subscribe' => true, 'url' => $redirectUrl]], 200, []);
        } else {
            return new RedirectResponse($redirectUrl);
        }
    }

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
    }

    /**
     * @inheritdoc
     */
    public function createResponseForExistingSubscription(Request $request, User $User, Subscription $subscription)
    {
        // TODO: Implement onExistingSubscription() method.
    }
}