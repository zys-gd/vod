<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 11:17
 */

namespace SubscriptionBundle\Carriers\OrangeEG\Callback;


use AppBundle\Constant\Carrier;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Service\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Service\Callback\Impl\HasCustomTrackingRules;
use IdentificationBundle\Entity\User;

class OrangeEGSubscribeCallbackHandler implements CarrierCallbackHandlerInterface, HasCommonFlow, HasCustomTrackingRules
{

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId == Carrier::ORANGE_EGYPT;
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        // TODO: Implement onRenewSendSuccess() method.
    }

    public function isNeedToBeTracked(ProcessResult $result): bool
    {
        return true;
    }
}