<?php

namespace SubscriptionBundle\Controller;

use App\Domain\Repository\CarrierRepository;
use App\Domain\Service\Translator\Translator;
use ExtrasBundle\Controller\Traits\ResponseTrait;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\SubscriptionPack\Exception\ActiveSubscriptionPackNotFound;
use SubscriptionBundle\Subscription\Notification\Notifier;
use SubscriptionBundle\SubscriptionPack\SubscriptionPackProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SubnotifController
 */
class SubnotifController
{
    use ResponseTrait;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var IdentificationDataStorage
     */
    private $identificationDataStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * SubnotifController constructor
     *
     * @param Notifier $notifier
     * @param CarrierRepository $carrierRepository
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     * @param LocalExtractor $localExtractor
     * @param SubscriptionPackProvider $subscriptionPackProvider
     * @param Translator $translator
     * @param IdentificationDataStorage $identificationDataStorage
     * @param RouterInterface $router
     */
    public function __construct(
        Notifier $notifier,
        CarrierRepository $carrierRepository,
        UserRepository $userRepository,
        SessionInterface $session,
        LocalExtractor $localExtractor,
        SubscriptionPackProvider $subscriptionPackProvider,
        Translator $translator,
        IdentificationDataStorage $identificationDataStorage,
        RouterInterface $router
    ) {
        $this->notifier = $notifier;
        $this->carrierRepository = $carrierRepository;
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->localExtractor = $localExtractor;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->translator = $translator;
        $this->identificationDataStorage = $identificationDataStorage;
        $this->router = $router;
    }

    /**
     * @Route("/subnotif/remind", name="remind_credentials")
     *
     * @param Request $request
     * @param ISPData $data
     *
     * @return JsonResponse
     *
     * @throws ActiveSubscriptionPackNotFound
     */
    public function sendRemindSms(Request $request, ISPData $data)
    {
        if ($this->identificationDataStorage->isWifiFlow()) {
            $phoneNumber = $request->request->get('phoneNumber');
            $user = $this->userRepository->findOneByMsisdn($phoneNumber);
            $redirectUrl = $this->router->generate('index', ['msisdn' => $phoneNumber]);
        } else {
            $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($this->session);
            $identificationToken = $identificationData['identification_token'];

            $user = $this->userRepository->findOneByIdentificationToken($identificationToken);
            $redirectUrl = $redirectUrl = $this->router->generate('index');
        }

        $carrier = $this->carrierRepository->findOneByBillingId($data->getCarrierId());
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        $localLanguage = $this->localExtractor->getLocal();

        $this->notifier->sendSMS(
            $carrier,
            $user->getIdentifier(),
            $user->getShortUrlId() ?? '',
            $subscriptionPack->convertPeriodicity2Text(),
            $localLanguage
        );

        return $this->getSimpleJsonResponse('Success', 200, [], [
            'message' => $this->translator->translate('messages.info.remind_credentials', $carrier->getBillingCarrierId(), $localLanguage),
            'redirectUrl' => $redirectUrl
        ]);
    }
}