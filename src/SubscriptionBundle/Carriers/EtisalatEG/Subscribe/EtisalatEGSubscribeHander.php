<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 15:20
 */

namespace SubscriptionBundle\Carriers\EtisalatEG\Subscribe;


use AppBundle\Constant\Carrier;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCommonFlow;
use UserBundle\Entity\BillableUser;

class EtisalatEGSubscribeHander implements SubscriptionHandlerInterface, HasCommonFlow
{

    public function canHandle(\AppBundle\Entity\Carrier $carrier): bool
    {
        return in_array($carrier->getIdCarrier(), [
            Carrier::ETISALAT_EGYPT,
        ]);
    }

    public function getAdditionalSubscribeParams(Request $request, BillableUser $billableUser): array
    {
        return [
            'subscription_contract_id' => $request->get('subscription_contract_id'),
            'url_id'                   => $billableUser->getUrlId()
        ];
    }

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        // TODO: Implement performPostSubscribeActions() method.
    }

}