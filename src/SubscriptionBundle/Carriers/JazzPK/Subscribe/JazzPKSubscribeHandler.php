<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.03.19
 * Time: 15:13
 */

namespace SubscriptionBundle\Carriers\JazzPK\Subscribe;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\SubscribeProcess;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Service\Notification\Notifier;
use Symfony\Component\HttpFoundation\Request;

class JazzPKSubscribeHandler implements SubscriptionHandlerInterface, HasCommonFlow
{
    /**
     * @var Notifier
     */
    private $notifier;


    /**
     * TelenorPKSubscribeHandler constructor.
     * @param Notifier $notifier
     */
    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::MOBILINK_PAKISTAN;
    }

    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        return [];
    }


    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        if ($result->isFailed()) {
            return;
        }

        $subscriptionPack = $subscription->getSubscriptionPack();
        $carrier          = $subscription->getUser()->getCarrier();

        if ($subscriptionPack->isFirstSubscriptionPeriodIsFree()) {
            return;
        }

        $this->notifier->sendNotification(SubscribeProcess::PROCESS_METHOD_SUBSCRIBE, $subscription, $subscriptionPack, $carrier);

    }


}