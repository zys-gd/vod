<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.10.19
 * Time: 16:35
 */

namespace SubscriptionBundle\Carriers\HutchID\Callback;


use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Callback\Impl\CarrierCallbackHandlerInterface;
use SubscriptionBundle\Subscription\Callback\Impl\HasCommonFlow;
use SubscriptionBundle\Subscription\Unsubscribe\UnsubscribeFacade;
use Symfony\Component\HttpFoundation\Request;

class HutchIDCallbackRenew implements CarrierCallbackHandlerInterface, HasCommonFlow
{
    /**
     * @var UnsubscribeFacade
     */
    private $unsubscribeFacade;


    /**
     * HutchIDCallbackRenew constructor.
     * @param UnsubscribeFacade $unsubscribeFacade
     */
    public function __construct(UnsubscribeFacade $unsubscribeFacade)
    {
        $this->unsubscribeFacade = $unsubscribeFacade;
    }

    public function canHandle(Request $request, int $carrierId): bool
    {
        return $carrierId === ID::HUTCH3_INDONESIA_DOT;
    }

    public function afterProcess(Subscription $subscription, User $User, ProcessResult $processResponse)
    {

        if ($processResponse->getError() !== 'user_timeout') {
            return;
        }

        $this->unsubscribeFacade->doFullUnsubscribe($subscription);

    }

    public function getUser(string $msisdn): ?User
    {
        // TODO: Implement getUser() method.
    }

}