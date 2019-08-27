<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 12:15
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Subscribe;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class TelenorPKSubscribeHandler implements SubscriptionHandlerInterface, HasCommonFlow
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

    public function canHandle(\CommonDataBundle\Entity\Interfaces\CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::TELENOR_PAKISTAN_DOT;
    }

    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        return [];
    }


    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        // TODO: Implement afterProcess() method.
    }


}