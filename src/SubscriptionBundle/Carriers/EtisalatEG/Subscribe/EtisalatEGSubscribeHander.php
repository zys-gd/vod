<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.04.18
 * Time: 15:20
 */

namespace SubscriptionBundle\Carriers\EtisalatEG\Subscribe;


use App\Domain\Constants\ConstBillingCarrierId;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCommonFlow;
use IdentificationBundle\Entity\User;

class EtisalatEGSubscribeHander implements SubscriptionHandlerInterface, HasCommonFlow
{

    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
    {
        return in_array($carrier->getBillingCarrierId(), [
            ConstBillingCarrierId::ETISALAT_EGYPT,
        ]);
    }

    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        return [
            'subscription_contract_id' => $request->get('subscription_contract_id'),
            'url_id'                   => $User->getUrlId()
        ];
    }

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        // TODO: Implement performPostSubscribeActions() method.
    }

}