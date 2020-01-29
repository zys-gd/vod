<?php

namespace SubscriptionBundle\Subscription\Unsubscribe\Admin\Controller;

use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\UserRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\Affiliate\AffiliateLogRepository;
use SubscriptionBundle\Repository\BlackListRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Unsubscribe\Admin\Form\UnsubscribeByFileForm;
use SubscriptionBundle\Subscription\Unsubscribe\Admin\Form\UnsubscribeByMsisdnForm;
use SubscriptionBundle\Subscription\Unsubscribe\Admin\Service\AdminUnsubscriber;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @var array
     */
    private $tabList = [
        UnsubscribeByMsisdnForm::NAME => 'MSISDN',
        UnsubscribeByFileForm::NAME   => 'FILE',
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
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var BlackListRepository
     */
    private $blackListRepository;
    /**
     * @var AffiliateLogRepository
     */
    private $affiliateLogRepository;
    /**
     * @var AdminUnsubscriber
     */
    private $adminUnsubscriber;


    /**
     * UnsubscriptionAdminController constructor
     *
     * @param FormFactory            $formFactory
     * @param SubscriptionRepository $subscriptionRepository
     * @param UserRepository         $userRepository
     * @param BlackListRepository    $blackListRepository
     * @param AffiliateLogRepository $affiliateLogRepository
     * @param AdminUnsubscriber      $adminUnsubscriber
     */
    public function __construct(
        FormFactory $formFactory,
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository,
        BlackListRepository $blackListRepository,
        AffiliateLogRepository $affiliateLogRepository,
        AdminUnsubscriber $adminUnsubscriber
    )
    {
        $this->formFactory            = $formFactory;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository         = $userRepository;
        $this->blackListRepository    = $blackListRepository;
        $this->affiliateLogRepository = $affiliateLogRepository;
        $this->adminUnsubscriber      = $adminUnsubscriber;
    }

    /**
     * @param Request|null $request
     *
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function unsubscribeFormAction(Request $request = null)
    {
        $formName = $this->detectCurrentFormTab($request->request->all());


        switch ($formName) {
            case 'msisdn':
                $tabContent = $this->handleMsisdnForm($request);
                break;
            case 'file':
                $tabContent = $this->handleFileForm($request);
                break;
            default;
                throw new BadRequestHttpException('Unknown tab');
                break;
        }


        return $this->renderWithExtraParams('@SubscriptionAdmin/Unsubscription/unsubscribe_form.html.twig', [
            'tabs'       => $this->tabList,
            'tabContent' => $tabContent,
            'activeTab'  => $formName
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


        $success = [];
        $errors  = [];

        foreach ($users as $user) {
            $subscriptionId = $user['subscriptionId'];
            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->find($subscriptionId);
            if ($this->adminUnsubscriber->unsubscribe($subscription, (bool)$user['toBlacklist'])) {
                $success[] = $subscription->getUuid();
            } else {
                $errors[] = $subscription->getUuid();
            }

        }

        return new JsonResponse([
            'success' => $success,
            'errors'  => $errors
        ]);
    }

    /**
     * @param Request|null $request
     *
     * @return string
     */
    private function handleMsisdnForm(Request $request = null): string
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
     * @param array $msisdns
     *
     * @return string
     */
    private function renderPreparedUsers(array $msisdns): string
    {
        $nonexistentUsers    = [];
        $users               = [];
        $alreadyUnsubscribed = [];
        $alreadyBlacklisted  = [];

        foreach ($msisdns as $msisdn) {
            /** @var User $user */
            $user = $this->userRepository->findOneByMsisdn($msisdn);

            if (empty($user)) {
                $nonexistentUsers[] = $msisdn;
                continue;
            }

            $isBlacklisted = $this->blackListRepository->findOneBy(['alias' => $msisdn]);

            if (!empty($isBlacklisted)) {
                $alreadyBlacklisted[] = $msisdn;
                continue;
            }

            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->findOneBy(['user' => $user->getUuid()]);

            if (!empty($subscription) && $subscription->isUnsubscribed()) {
                $alreadyUnsubscribed[] = $msisdn;
            } elseif (!empty($subscription)) {
                $users[$msisdn] = $subscription->getUuid();
            }
        }

        return $this->renderView('@SubscriptionAdmin/Unsubscription/prepared_users.html.twig', [
            'admin'               => $this->admin,
            'headers'             => $this->tableHeaders,
            'users'               => $users,
            'nonexistentUsers'    => $nonexistentUsers,
            'alreadyUnsubscribed' => $alreadyUnsubscribed,
            'alreadyBlacklisted'  => $alreadyBlacklisted
        ]);
    }

    /**
     * @param Request|null $request
     *
     * @return string
     */
    protected function handleFileForm(Request $request = null): string
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

}