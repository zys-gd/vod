<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 15:20
 */

namespace SubscriptionBundle\Carriers\EtisalatEG\Subscribe;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\BillingFramework\Process\DTO\PinRequestResult;
use IdentificationBundle\WifiIdentification\Service\WifiIdentificationDataStorage;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCommonFlow;
use IdentificationBundle\Entity\User;

class EtisalatEGSubscribeHander implements SubscriptionHandlerInterface, HasCommonFlow
{
    /**
     * @var WifiIdentificationDataStorage
     */
    private $wifiIdentificationDataStorage;


    /**
     * EtisalatEGSubscribeHander constructor.
     * @param WifiIdentificationDataStorage $identificationDataStorage
     */
    public function __construct(WifiIdentificationDataStorage $identificationDataStorage)
    {
        $this->wifiIdentificationDataStorage = $identificationDataStorage;
    }

    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
    {
        return in_array($carrier->getBillingCarrierId(), [
            ConstBillingCarrierId::ETISALAT_EGYPT,
        ]);
    }

    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        /** @var PinRequestResult $pinRequestResult */
        $pinRequestResult = $this->wifiIdentificationDataStorage->getPinRequestResult();

        $contractId = $pinRequestResult->getRawData()['subscription_contract_id'];

        return [
            'subscription_contract_id' => $contractId,
            'url_id'                   => $User->getShortUrlId()
        ];
    }

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        // TODO: Implement performPostSubscribeActions() method.
    }

}