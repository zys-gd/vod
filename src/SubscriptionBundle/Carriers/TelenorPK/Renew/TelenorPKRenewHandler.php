<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Renew;


use App\Domain\Constants\ConstBillingCarrierId;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Renew\Handler\RenewHandlerInterface;
use SubscriptionBundle\Service\Callback\Impl\HasCommonFlow;

class TelenorPKRenewHandler implements RenewHandlerInterface, \SubscriptionBundle\Service\Action\Renew\Handler\HasCommonFlow
{

    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::TELENOR_PAKISTAN_DOT;
    }

    public function onSuccess(Subscription $subscription, int $processId): void
    {
        // TODO: Implement onSuccess() method.
    }

    public function onFailure(Subscription $subscription, string $errorText): void
    {
        // TODO: Implement onFailure() method.
    }
}