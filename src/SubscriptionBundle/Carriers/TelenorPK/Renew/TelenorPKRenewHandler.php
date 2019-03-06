<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Renew;


use App\Domain\Constants\ConstBillingCarrierId;
use SubscriptionBundle\Service\Action\Renew\Handler\RenewHandlerInterface;

class TelenorPKRenewHandler implements RenewHandlerInterface
{

    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::TELENOR_PAKISTAN_DOT;
    }
}