<?php

namespace Carriers\TMobilePolandDimoco\Subscribe;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TMobilePolandDimocoSubscribeHandler
 */
class TMobilePolandDimocoSubscribeHandler implements SubscriptionHandlerInterface, HasCommonFlow
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var WifiIdentificationDataStorage
     */
    private $storage;

    /**
     * TMobilePolandDimocoSubscribeHandler constructor.
     *
     * @param RouterInterface               $router
     * @param WifiIdentificationDataStorage $storage
     */
    public function __construct(RouterInterface $router, WifiIdentificationDataStorage $storage)
    {
        $this->router = $router;
        $this->storage = $storage;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TMOBILE_POLAND_DIMOCO;
    }

    /**
     * @param Request $request
     * @param User    $User
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        $additionalData = [
            'redirect_url' => $this->router->generate('payment_confirmation', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ];

        if ($this->storage->isWifiFlow() && $pinVerifyResult = $this->storage->getPinVerifyResult()) {
            $additionalData['process_via_pin_flow'] = $pinVerifyResult->getRawData()['transactionId'];
        }

        return $additionalData;
    }

    /**
     * @param Subscription  $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result)
    {
        // TODO: Implement afterProcess() method.
    }
}