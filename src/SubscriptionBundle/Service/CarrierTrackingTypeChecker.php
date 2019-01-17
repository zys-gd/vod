<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.05.18
 * Time: 10:34
 */

namespace SubscriptionBundle\Service;


use App\Domain\Constants\ConstBillingCarrierId;
use App\Domain\Entity\Carrier;

class CarrierTrackingTypeChecker
{
    public function isShouldBeTrackedOnCallback(Carrier $carrier): bool
    {
        return in_array($carrier->getBillingCarrierId(), [
            ConstBillingCarrierId::ORANGE_EGYPT,
            ConstBillingCarrierId::ETISALAT_EGYPT,
            ConstBillingCarrierId::ORANGE_TUNISIA,
            ConstBillingCarrierId::OOREDOO_ALGERIA,
            ConstBillingCarrierId::TELENOR_MYANMAR
        ]);
    }

    public function isShouldBeTrackedOnCallbackForUnsubscribe(Carrier $carrier): bool
    {
        return in_array($carrier->getBillingCarrierId(), [
//            CarrierConstant::ETISALAT_EGYPT,
//            CarrierConstant::OOREDOO_ALGERIA,
        ]);
    }

}