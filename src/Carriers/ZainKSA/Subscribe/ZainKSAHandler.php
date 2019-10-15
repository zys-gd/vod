<?php

namespace Carriers\ZainKSA\Subscribe;

use App\Domain\Repository\CarrierRepository;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Subscription\Subscribe\Common\ZeroCreditSubscriptionChecking;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomAffiliateTrackingRules;
use SubscriptionBundle\Subscription\Subscribe\Handler\HasCustomResponses;
use SubscriptionBundle\Subscription\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ZainKSAHandler
 */
class ZainKSAHandler implements SubscriptionHandlerInterface, HasCustomResponses, HasCommonFlow, HasCustomAffiliateTrackingRules
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
     * @var ZeroCreditSubscriptionChecking
     */
    private $zeroCreditSubscriptionChecking;


    /**
     * ZainKSAHandler constructor.
     *
     * @param string                         $redirectUrl
     * @param CarrierRepository              $carrierRepository
     * @param ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
     */
    public function __construct(
        string $redirectUrl,
        CarrierRepository $carrierRepository,
        ZeroCreditSubscriptionChecking $zeroCreditSubscriptionChecking
    )
    {
        $this->redirectUrl                    = $redirectUrl;
        $this->carrierRepository              = $carrierRepository;
        $this->zeroCreditSubscriptionChecking = $zeroCreditSubscriptionChecking;
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
     * @param ProcessResult     $result
     * @param CampaignInterface $campaign
     *
     * @return bool
     */
    public function isAffiliateTrackedForSub(ProcessResult $result, CampaignInterface $campaign): bool
    {
        $carrier = $this->carrierRepository->findOneByBillingId(ID::ZAIN_SAUDI_ARABIA);

        $isSuccess        = $result->isFailedOrSuccessful() && $result->isFinal();
        $isZeroCreditsSub = $this
            ->zeroCreditSubscriptionChecking
            ->isZeroCreditAvailable(ID::ZAIN_SAUDI_ARABIA, $campaign);

        if ($isZeroCreditsSub) {
            return $isSuccess &&
                $carrier->getTrackAffiliateOnZeroCreditSub() &&
                $this->zeroCreditSubscriptionChecking->isZeroCreditSubscriptionPerformed($result);
        } else {
            return $isSuccess;
        }

    }

    /**
     * @param ProcessResult $result
     *
     * @return bool
     */
    public function isAffiliateTrackedForResub(ProcessResult $result): bool
    {
        return false;
    }

    /**
     * @param Request $request
     * @param User    $user
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
     * @return Response|null
     */
    public function createResponseForExistingSubscription(Request $request, User $User, Subscription $subscription)
    {
    }

    public function afterProcess(Subscription $subscription, \SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result)
    {
        // TODO: Implement afterProcess() method.
    }
}