<?php

namespace SubscriptionBundle\Carriers\HutchID\Subscribe;

use App\Domain\Constants\ConstBillingCarrierId;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\Session\IdentificationDataStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;


class HutchIDSubscriptionHandler implements SubscriptionHandlerInterface, HasConsentPageFlow
{
    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * HutchIDSubscriptionHandler constructor.
     *
     * @param IdentificationDataStorage $identificationDataStorage
     * @param RouterInterface           $router
     */
    public function __construct(
        IdentificationDataStorage $identificationDataStorage,
        RouterInterface $router
    ) {
        $this->identificationDataStorage = $identificationDataStorage;
        $this->router = $router;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return false; //$carrier->getBillingCarrierId() === ConstBillingCarrierId::HUTCH_INDONESIA;
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     */
    public function getAdditionalSubscribeParams(Request $request, User $user): array
    {

        return [];
    }

    /**
     * @param SubscribingProcessException $exception
     *
     * @return Response
     */
    public function getSubscriptionErrorResponse(SubscribingProcessException $exception): Response
    {
        $billingData = $exception->getBillingData();
        $failReason = $billingData->provider_fields->fail_reason;

        switch ($failReason) {
            case SubscribingProcessException::FAIL_REASON_NOT_ENOUGH_CREDIT:
                $redirectUrl = $this->router->generate('index', ['err_handle' => 'not_enough_credit']);
                break;
            case SubscribingProcessException::FAIL_REASON_BLACKLISTED:
                $redirectUrl = $this->router->generate('blacklisted_user');
                break;
            default:
                $redirectUrl = $this->router->generate('whoops');
                break;
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param Subscription $subscription
     * @param ProcessResult $result
     */
    public function afterProcess(Subscription $subscription, ProcessResult $result): void
    {

    }
}