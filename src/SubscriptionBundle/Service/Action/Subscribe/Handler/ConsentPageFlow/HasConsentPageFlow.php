<?php

namespace SubscriptionBundle\Service\Action\Subscribe\Handler\ConsentPageFlow;

use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface HasConsentPageFlow
 */
interface HasConsentPageFlow
{
    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $user): array;

    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     */
    public function getAdditionalResubscribeParams(Request $request, User $user): array;

    /**
     * @param Subscription $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void;
}