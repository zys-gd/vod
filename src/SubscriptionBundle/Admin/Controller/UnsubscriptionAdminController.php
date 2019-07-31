<?php

namespace SubscriptionBundle\Admin\Controller;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Carrier;
use ExtrasBundle\Utils\UuidGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use IdentificationBundle\Entity\User;
use Sonata\AdminBundle\Controller\CRUDController;
use SubscriptionBundle\Admin\Form\Unsubscription\UnsubscribeByAffiliateForm;
use SubscriptionBundle\Admin\Form\Unsubscription\UnsubscribeByCampaignForm;
use SubscriptionBundle\Admin\Form\Unsubscription\UnsubscribeByFileForm;
use SubscriptionBundle\Admin\Form\Unsubscription\UnsubscribeByMsisdnForm;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\BlackList;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\Affiliate\AffiliateLogRepository;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Service\Action\Unsubscribe\Unsubscriber;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class UnsubscriptionAdminController
 */
class UnsubscriptionAdminController extends CRUDController
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Unsubscriber
     */
    private $unsubscriber;

    /**
     * @var UnsubscriptionHandlerProvider
     */
    private $unsubscriptionHandlerProvider;

    /**
     * @var array
     */
    private $tabList = [
        UnsubscribeByMsisdnForm::NAME => 'MSISDN',
        UnsubscribeByFileForm::NAME => 'FILE',
        UnsubscribeByCampaignForm::NAME => 'CAMPAIGNS',
        UnsubscribeByAffiliateForm::NAME => 'AFFILIATE'
    ];

    /**
     * @var array
     */
    private $tableHeaders = [
        'Msisdn',
        'Unsubscribe',
        'Set to blacklist'
    ];

    /**
     * UnsubscriptionAdminController constructor
     *
     * @param FormFactory $formFactory
     * @param Unsubscriber $unsubscriber
     * @param UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider
     */
    public function __construct(
        FormFactory $formFactory,
        Unsubscriber $unsubscriber,
        UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider
    ) {
        $this->formFactory = $formFactory;
        $this->unsubscriber = $unsubscriber;
        $this->unsubscriptionHandlerProvider = $unsubscriptionHandlerProvider;
    }

    /**
     * @param Request|null $request
     *
     * @return Response
     */
    public function createAction(Request $request = null)
    {
        $formName = $this->detectCurrentFormTab($request->request->all());

        if (empty($formName)) {
            throw new BadRequestHttpException('Unknown tab');
        }

        $actionName = 'handle' . ucfirst($formName);

        return $this->renderWithExtraParams('@SubscriptionAdmin/Unsubscription/create.html.twig',[
            'tabs' => $this->tabList,
            'tabContent' => $this->$actionName($request),
            'activeTab' => $formName
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function unsubscribeAction(Request $request)
    {
        $users = $request->request->get('users');

        if (empty($users)) {
            throw new BadRequestHttpException('At least one user should be present to unsubscribe');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $subscriptionRepository = $entityManager->getRepository(Subscription::class);
        $carrierRepository = $entityManager->getRepository(Carrier::class);

        $success = [];
        $errors = [];

        foreach ($users as $user) {
            $subscriptionId = $user['subscriptionId'];

            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->find($subscriptionId);
            /** @var SubscriptionPack $subscriptionPack */
            $subscriptionPack = $subscription->getSubscriptionPack();
            /** @var Carrier $carrier */
            $carrier = $carrierRepository->findOneBy(['billingCarrierId' => $subscriptionPack->getCarrier()->getBillingCarrierId()]);

            try {
                $response = $this->unsubscriber->unsubscribe($subscription, $subscriptionPack);

                $unsubscriptionHandler = $this->unsubscriptionHandlerProvider->getUnsubscriptionHandler($carrier);
                $unsubscriptionHandler->applyPostUnsubscribeChanges($subscription);

                if ($unsubscriptionHandler->isPiwikNeedToBeTracked($response)) {
                    $this->unsubscriber->trackEventsForUnsubscribe($subscription, $response);
                }

                if ((int) $user['toBlacklist']) {
                    $blackList = new BlackList(UuidGenerator::generate());
                    $blackList
                        ->setBillingCarrierId($subscriptionPack->getCarrier()->getBillingCarrierId())
                        ->setAlias($subscription->getUser()->getIdentifier());

                    $entityManager->persist($blackList);
                    $entityManager->flush();
                }

                $success[] = $subscription->getUuid();
            } catch (\Exception $e) {
                $errors[] = $subscription->getUuid();
            }
        }

        return new Response(json_encode(['success' => $success, 'errors' => $errors]));
    }

    /**
     * @param Request|null $request
     *
     * @return string
     */
    protected function handleMsisdn(Request $request = null): string
    {
        $form = $this->formFactory->create(UnsubscribeByMsisdnForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->renderPreparedUsers([$form->get('msisdn')->getData()]);
        }

        return $this->renderView('@SubscriptionAdmin/Unsubscription/unsubscribe_by_msisdn.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request|null $request
     *
     * @return string
     */
    protected function handleFile(Request $request = null): string
    {
        $form = $this->formFactory->create(UnsubscribeByFileForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            return $this->renderPreparedUsers(str_getcsv(file_get_contents($file->getRealPath()), ','));
        }

        return $this->renderView('@SubscriptionAdmin/Unsubscription/unsubscribe_by_file.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request|null $request
     *
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function handleCampaign(Request $request = null): string
    {
        $form = $this->formFactory->create(UnsubscribeByCampaignForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var ArrayCollection $campaigns */
            $campaigns = $data['campaign'];
            $period = $data['period'];
            $usersLimit = $data['usersCount'];
            $msisdns = [];

            /** @var AffiliateLogRepository $affiliateLogRepository */
            $affiliateLogRepository = $this->getDoctrine()->getRepository(AffiliateLog::class);

            /** @var CampaignInterface $campaign */
            foreach ($campaigns->getIterator() as $campaign) {
                $identifiers = $affiliateLogRepository->findByCampaignAndDateRange(
                    $campaign->getCampaignToken(),
                    $period['start'],
                    $period['end'],
                    $usersLimit
                );

                $msisdns = array_merge($msisdns, $identifiers);
            }

            return $this->renderPreparedUsers($msisdns);
        }

        return $this->renderView('@SubscriptionAdmin/Unsubscription/unsubscribe_by_campaign.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request|null $request
     *
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function handleAffiliate(Request $request = null): string
    {
        $form = $this->formFactory->create(UnsubscribeByAffiliateForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var Affiliate $affiliate */
            $affiliate = $data['affiliate'];
            /** @var Carrier $carrier */
            $carrier = $data['carrier'];
            $period = $data['period'];
            $usersLimit = $data['usersCount'];

            /** @var AffiliateLogRepository $affiliateLogRepository */
            $affiliateLogRepository = $this->getDoctrine()->getRepository(AffiliateLog::class);
            $msisdns = $affiliateLogRepository->findByAffiliateCarrierAndDateRange(
                $affiliate->getUuid(),
                $carrier->getUuid(),
                $period['start'],
                $period['end'],
                $usersLimit
            );

            return $this->renderPreparedUsers($msisdns);
        }

        return $this->renderView('@SubscriptionAdmin/Unsubscription/unsubscribe_by_affiliate.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param array $msisdns
     *
     * @return string
     */
    protected function renderPreparedUsers(array $msisdns): string
    {
        $nonexistentUsers = [];
        $users = [];
        $alreadyUnsubscribed = [];
        $alreadyBlacklisted = [];
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($msisdns as $msisdn) {
            /** @var User $user */
            $user = $entityManager->getRepository(User::class)->findOneBy(['identifier' => $msisdn]);

            if (empty($user)) {
                $nonexistentUsers[] = $msisdn;
                continue;
            }

            $isBlacklisted = $entityManager->getRepository(BlackList::class)->findOneBy(['alias' => $msisdn]);

            if (!empty($isBlacklisted)) {
                $alreadyBlacklisted[] = $msisdn;
                continue;
            }

            /** @var Subscription $subscription */
            $subscription = $entityManager
                ->getRepository(Subscription::class)
                ->findOneBy(['user' => $user->getUuid()]);

            if (!empty($subscription) && $subscription->isUnsubscribed()) {
                $alreadyUnsubscribed[] = $msisdn;
            } elseif (!empty($subscription)) {
                $users[$msisdn] = $subscription->getUuid();
            }
        }

        return $this->renderView('@SubscriptionAdmin/Unsubscription/prepared_users.html.twig', [
            'admin' => $this->admin,
            'headers' => $this->tableHeaders,
            'users' => $users,
            'nonexistentUsers' => $nonexistentUsers,
            'alreadyUnsubscribed' => $alreadyUnsubscribed,
            'alreadyBlacklisted' => $alreadyBlacklisted
        ]);
    }

    /**
     * @param array $postData
     *
     * @return string|null
     */
    private function detectCurrentFormTab(array $postData): ?string
    {
        if (count($postData) > 1) {
            return null;
        }

        $requestFormName = empty($postData) ? UnsubscribeByMsisdnForm::NAME : array_keys($postData)[0];

        return empty($this->tabList[$requestFormName]) ? null : $requestFormName;
    }

    public function listAction()
    {
        return RedirectResponse::create($this->admin->generateUrl('create'));
    }
}