<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.11.18
 * Time: 11:16
 */

namespace SubscriptionBundle\Carriers\EtisalatEG\Callback;


use App\Domain\Constants\ConstBillingCarrierId;
use AppBundle\Constant\Carrier;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Service\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Service\Callback\Impl\HasCustomTrackingRules;
use IdentificationBundle\Entity\User;

class EtisalatEGCallbackSubscribe implements CarrierCallbackHandlerInterface, HasCustomTrackingRules, HasCommonFlow
{

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId == ConstBillingCarrierId::ETISALAT_EGYPT;
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {
        // TODO: Implement onRenewSend() method.
    }

    public function isNeedToBeTracked(ProcessResult $result): bool
    {
        return true;
    }
}