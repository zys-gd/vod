<?php

namespace SubscriptionBundle\Service\Action\Subscribe;

use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Common\RequestParametersProvider;
use SubscriptionBundle\Service\CampaignExtractor;
use SubscriptionBundle\Service\ZeroCreditSubscriptionChecking;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class SubscribeParametersProvider
{
    /**
     * @var RequestParametersProvider
     */
    private $parametersProvider;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    /**
     * SubscribeParametersProvider constructor
     *
     * @param RequestParametersProvider      $parametersProvider
     * @param RequestStack                   $requestStack
     * @param RouterInterface                $router
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     * @param CampaignExtractor              $campaignExtractor
     */
    public function __construct(
        RequestParametersProvider $parametersProvider,
        RequestStack $requestStack,
        RouterInterface $router,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking,
        CampaignExtractor $campaignExtractor
    ) {
        $this->parametersProvider = $parametersProvider;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
        $this->campaignExtractor = $campaignExtractor;
    }

    /**
     * @param Subscription $subscription
     * @param array        $additionalInfo
     *
     * @return ProcessRequestParameters
     */
    public function provideParameters(Subscription $subscription, array $additionalInfo): ProcessRequestParameters
    {
        $subscriptionPack = $subscription->getSubscriptionPack();
        $campaign = $this->campaignExtractor->getCampaignForSubscription($subscription);

        $isZeroCreditAvailable = $this
            ->zeroCreditSubscriptionChecking
            ->isZeroCreditAvailable($subscription->getUser()->getCarrier()->getBillingCarrierId(), $campaign);

        $parameters                         = $this->parametersProvider->prepareRequestParameters($subscription);
        $parameters->additionalData         = $additionalInfo;
        $parameters->chargeProduct          = $subscription->getUuid();
        $parameters->chargeTier             = $subscriptionPack->getTierId();
        $parameters->chargeStrategy         = $subscriptionPack->getBuyStrategyId();
        $parameters->zeroCreditSubAvailable = $isZeroCreditAvailable;

        if ($request = $this->requestStack->getCurrentRequest()) {
            $parameters->redirectUrl = $request->get('location', $this->router->generate('subscription.wait_listen'));
        }

        return $parameters;
    }

}