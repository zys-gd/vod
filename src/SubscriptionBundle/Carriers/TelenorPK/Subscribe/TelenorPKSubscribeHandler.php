<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 12:15
 */

namespace SubscriptionBundle\Carriers\TelenorPK\Subscribe;


use App\Domain\Constants\ConstBillingCarrierId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\HasCustomResponses;
use IdentificationBundle\Entity\User;

class TelenorPKSubscribeHandler implements SubscriptionHandlerInterface, HasCustomResponses, HasCommonFlow
{
    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::TELENOR_PAKISTAN;
    }

    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        return [];
    }


    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        // TODO: Implement applyPostSubscribeChanges() method.
    }

    /**
     * @param Request      $request
     * @param User $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForSuccessfulSubscribe(Request $request, User $User, Subscription $subscription)
    {
        $redirect           = $request->get('redirect', false);
        $session            = $request->getSession();
        $redirectForFortumo = $session->get('redirect_for_fortumo', '');

        if ($redirect || !empty($redirectForFortumo)) {
            $session->remove('redirect_for_fortumo');
        }
    }

    /**
     * @param Request      $request
     * @param User $User
     * @param Subscription $subscription
     * @return Response|null
     */
    public function createResponseForExistingSubscription(Request $request, User $User, Subscription $subscription)
    {
    }
}