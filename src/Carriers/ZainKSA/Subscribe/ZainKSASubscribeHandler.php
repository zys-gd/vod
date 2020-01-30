<?php

namespace Carriers\ZainKSA\Subscribe;

use App\Domain\Repository\CarrierRepository;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ZainKSAHandler
 */
class ZainKSASubscribeHandler implements SubscriptionHandlerInterface, HasCustomResponses, HasCommonFlow
{
    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;


    /**
     * ZainKSAHandler constructor.
     *
     * @param string            $redirectUrl
     * @param CarrierRepository $carrierRepository
     */
    public function __construct(
        string $redirectUrl,
        CarrierRepository $carrierRepository
    )
    {
        $this->redirectUrl       = $redirectUrl;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ZAIN_SAUDI_ARABIA;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return Response|null
     */
    public function createResponseBeforeSubscribeAttempt(Request $request, User $user)
    {
        if (preg_match('/966831\d+/', $user->getIdentifier())) {
            return new RedirectResponse($this->redirectUrl);
        }

        return null;
    }

    /**
     * @param Request $request
     * @param User    $User
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $User): array
    {
        return [];
    }

    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     *
     * @return Response|null
     */
    public function createResponseForSuccessfulSubscribe(Request $request, User $User, Subscription $subscription)
    {
        // TODO: Implement createResponseForSuccessfulSubscribe() method.
    }

    /**
     * @param Request      $request
     * @param User         $User
     * @param Subscription $subscription
     *
     * @return Response|null
     */
    public function createResponseForExistingSubscription(Request $request, User $User, Subscription $subscription)
    {
    }

    public function afterProcess(
        Subscription $subscription,
        \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result
    )
    {
        // TODO: Implement afterProcess() method.
    }
}