<?php

namespace SubscriptionBundle\Controller;

use App\Domain\Repository\CarrierRepository;
use App\Domain\Service\Translator\Translator;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Service\Notification\Notifier;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

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
     * SubnotifController constructor
     *
     * @param Notifier $notifier
     * @param CarrierRepository $carrierRepository
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     * @param LocalExtractor $localExtractor
     * @param SubscriptionPackProvider $subscriptionPackProvider
     * @param Translator $translator
     */
    public function __construct(
        Notifier $notifier,
        CarrierRepository $carrierRepository,
        UserRepository $userRepository,
        SessionInterface $session,
        LocalExtractor $localExtractor,
        SubscriptionPackProvider $subscriptionPackProvider,
        Translator $translator
    ) {
        $this->notifier = $notifier;
        $this->carrierRepository = $carrierRepository;
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->localExtractor = $localExtractor;
        $this->subscriptionPackProvider = $subscriptionPackProvider;
        $this->translator = $translator;
    }

    /**
     * @Route("/subnotif/remind",name="remind_credentials")
     *
     * @param Request $request
     * @param ISPData $data
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     */
    public function sendRemindSms(Request $request, ISPData $data)
    {
        $identificationData = IdentificationFlowDataExtractor::extractIdentificationData($this->session);
        $identificationToken = $identificationData['identification_token'];

        $carrier = $this->carrierRepository->findOneByBillingId($data->getCarrierId());
        $user = $this->userRepository->findOneByIdentificationToken($identificationToken);
        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        $localLanguage = $this->localExtractor->getLocal();

        $this->notifier->sendSMS(
            $carrier,
            $user->getIdentifier(),
            $user->getShortUrlId() ?? '',
            $subscriptionPack->convertPeriodicity2Text(),
            $localLanguage
        );

        return $this->getSimpleJsonResponse('Success', 200, [
            'message' => $this->translator->translate('messages.info.remind_credentials', $carrier->getBillingCarrierId(), $localLanguage)
        ]);
    }
}