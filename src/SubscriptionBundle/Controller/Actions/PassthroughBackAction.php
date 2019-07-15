<?php


namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Handler\PassthroughFlow\HasPassthroughFlow;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\BillingFramework\Process\Exception\SubscribingProcessException;
use SubscriptionBundle\Service\Action\Subscribe\Common\BlacklistVoter;
use SubscriptionBundle\Service\Action\Subscribe\Common\CommonFlowHandler;
use SubscriptionBundle\Service\Action\Subscribe\Handler\ConsentPageFlow\HasConsentPageFlow;
use SubscriptionBundle\Service\Action\Subscribe\Handler\SubscriptionHandlerProvider;
use SubscriptionBundle\Service\Blacklist\BlacklistAttemptRegistrator;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;

class PassthroughBackAction
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var IdentificationHandlerProvider
     */
    private $identificationHandlerProvider;

    /**
     * @var SubscriptionHandlerProvider
     */
    private $subscriptionHandlerProvider;

    /**
     * @var UserExtractor
     */
    private $userExtractor;

    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var BlacklistAttemptRegistrator
     */
    private $blacklistAttemptRegistrator;

    /**
     * @var BlacklistVoter
     */
    private $blacklistVoter;

    /**
     * PassthroughBackAction constructor.
     *
     * @param CarrierRepositoryInterface    $carrierRepository
     * @param IdentificationHandlerProvider $identificationHandlerProvider
     * @param SubscriptionHandlerProvider   $subscriptionHandlerProvider
     * @param UserExtractor                 $userExtractor
     * @param CommonFlowHandler             $commonFlowHandler
     * @param RouterInterface               $router
     * @param BlacklistVoter                $blacklistVoter
     * @param BlacklistAttemptRegistrator   $blacklistAttemptRegistrator
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        IdentificationHandlerProvider $identificationHandlerProvider,
        SubscriptionHandlerProvider $subscriptionHandlerProvider,
        UserExtractor $userExtractor,
        CommonFlowHandler $commonFlowHandler,
        RouterInterface $router,
        BlacklistVoter $blacklistVoter,
        BlacklistAttemptRegistrator $blacklistAttemptRegistrator
    )
    {
        $this->carrierRepository             = $carrierRepository;
        $this->identificationHandlerProvider = $identificationHandlerProvider;
        $this->subscriptionHandlerProvider   = $subscriptionHandlerProvider;
        $this->userExtractor                 = $userExtractor;
        $this->commonFlowHandler             = $commonFlowHandler;
        $this->router                        = $router;
        $this->blacklistVoter                = $blacklistVoter;
        $this->blacklistAttemptRegistrator   = $blacklistAttemptRegistrator;
    }

    /**
     * @param Request            $request
     * @param IdentificationData $identificationData
     * @param ISPData            $ispData
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function __invoke(Request $request, ISPData $ispData)
    {

        $carrierId           = $ispData->getCarrierId();

        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);
        $user    = $this->userExtractor->getUserByIdentificationData($identificationData);

        $this->ensurePassthroughFlowIsAvailable($carrier);

        $subscriber = $this->subscriptionHandlerProvider->getSubscriber($carrier);

        if (!$subscriber instanceof HasConsentPageFlow) {
            throw new BadRequestHttpException('This action is available only for subscription `PassthroughFlow`');
        }

        if ($this->blacklistVoter->isUserBlacklisted($request->getSession())
            || !$this->blacklistAttemptRegistrator->registerSubscriptionAttempt($identificationToken, (int)$carrierId)
        ) {
            return $this->blacklistVoter->createNotAllowedResponse();
        }

        try {
            return $this->commonFlowHandler->process($request, $user);
        } catch (SubscribingProcessException $exception) {
            return $subscriber->getSubscriptionErrorResponse($exception);
        } catch (\Exception $exception) {
            return new RedirectResponse($this->router->generate('whoops'));
        }
    }

    /**
     * @param CarrierInterface $carrier
     */
    private function ensurePassthroughFlowIsAvailable(CarrierInterface $carrier): void
    {
        $handler = $this->identificationHandlerProvider->get($carrier);

        if (!$handler instanceof HasPassthroughFlow) {
            throw new BadRequestHttpException('This action is available only for identification `PassthroughFlow`');
        }
    }
}