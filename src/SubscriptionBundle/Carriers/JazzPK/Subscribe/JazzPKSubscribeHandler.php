<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.03.19
 * Time: 15:13
 */

namespace SubscriptionBundle\Carriers\JazzPK\Subscribe;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Subscription\Notification\Notifier;
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

    public function canHandle(\CommonDataBundle\Entity\Interfaces\CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::MOBILINK_PAKISTAN;
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