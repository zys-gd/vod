<?php

namespace SubscriptionBundle\Admin\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use SubscriptionBundle\Admin\Form\Unsubscription\AffiliateForm;
use SubscriptionBundle\Admin\Form\Unsubscription\CampaignForm;
use SubscriptionBundle\Admin\Form\Unsubscription\FileForm;
use SubscriptionBundle\Admin\Form\Unsubscription\MsisdnForm;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

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
        MsisdnForm::NAME => 'MSISDN',
        FileForm::NAME => 'FILE',
        CampaignForm::NAME => 'CAMPAIGNS',
        AffiliateForm::NAME => 'AFFILIATE'
    ];

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createAction(Request $request = null)
    {
        $currentTab = $request->query->get('tab') ?? MsisdnForm::NAME;
        $actionName = 'handle' . ucfirst($currentTab);

        return $this->$actionName($request);
    }

    private function handleMsisdn(Request $request = null)
    {
        $form = $this->formFactory->create(MsisdnForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        $tabView = $this->renderView('@SubscriptionAdmin/Unsubscription/msisdnForm.html.twig', [
            'form' => $form->createView()
        ]);

        return $this->renderWithExtraParams('@SubscriptionAdmin/Unsubscription/create.html.twig', [
            'tabs' => $this->tabList,
            'currentTabView' => $tabView
        ]);
    }
}