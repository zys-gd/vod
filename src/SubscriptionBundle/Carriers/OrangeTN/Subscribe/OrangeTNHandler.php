<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.05.18
 * Time: 14:49
 */

namespace SubscriptionBundle\Carriers\OrangeTN\Subscribe;


use AppBundle\Constant\Carrier as CarrierConst;
use AppBundle\Entity\Carrier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Common\RedirectUrlNullifier;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Service\SubscriptionProvider;
use UserBundle\Entity\BillableUser;

class OrangeTNHandler implements SubscriptionHandlerInterface, HasCustomResponses, HasCommonFlow
{
    /**
     * @var SubscriptionProvider
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
     * @param SubscriptionProvider $subscriptionProvider
     * @param RedirectUrlNullifier $redirectUrlNullifier
     */
    public function __construct(
        SubscriptionProvider $subscriptionProvider,
        RedirectUrlNullifier $redirectUrlNullifier,
        RouterInterface $router
    )
    {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->redirectUrlNullifier = $redirectUrlNullifier;
        $this->router               = $router;
    }

    public function canHandle(Carrier $carrier): bool
    {
        return $carrier->getIdCarrier() == CarrierConst::ORANGE_TUNISIA;
    }

    public function getAdditionalSubscribeParams(Request $request, BillableUser $billableUser): array
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

    public function createResponseForSuccessfulSubscribe(Request $request, BillableUser $billableUser, Subscription $subscription)
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
    public function createResponseForExistingSubscription(Request $request, BillableUser $billableUser, Subscription $subscription)
    {
        // TODO: Implement onExistingSubscription() method.
    }
}