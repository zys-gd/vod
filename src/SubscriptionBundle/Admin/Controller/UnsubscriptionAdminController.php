<?php

namespace SubscriptionBundle\Admin\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use SubscriptionBundle\Admin\Form\Unsubscription\UnsubscribeByAffiliateForm;
use SubscriptionBundle\Admin\Form\Unsubscription\UnsubscribeByCampaignForm;
use SubscriptionBundle\Admin\Form\Unsubscription\UnsubscribeByFileForm;
use SubscriptionBundle\Admin\Form\Unsubscription\UnsubscribeByMsisdnForm;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
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
        UnsubscribeByFileForm::NAME => 'FILE',
        UnsubscribeByCampaignForm::NAME => 'CAMPAIGNS',
        UnsubscribeByAffiliateForm::NAME => 'AFFILIATE'
    ];

    /**
     * UnsubscriptionAdminController constructor
     *
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createAction(Request $request = null)
    {
        $formName = $this->detectCurrentFormTab($request->request->all());

        if (empty($formName)) {
            throw new BadRequestHttpException('Invalid form data');
        }

        $actionName = 'handle' . ucfirst($formName);

        return $this->renderWithExtraParams('@SubscriptionAdmin/Unsubscription/create.html.twig',[
            'tabs' => $this->tabList,
            'tabContent' => $this->$actionName($request),
            'activeTab' => $formName
        ]);
    }

    private function handleMsisdn(Request $request = null)
    {
        $form = $this->formFactory->create(UnsubscribeByMsisdnForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->renderView('@SubscriptionAdmin/Unsubscription/unsubscribeByMsisdnForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function handleFile(Request $request = null)
    {
        $form = $this->formFactory->create(UnsubscribeByFileForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->renderView('@SubscriptionAdmin/Unsubscription/unsubscribeByFileForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param array $postData
     *
     * @return string|null
     */
    private function detectCurrentFormTab(array $postData)
    {
        if (count($postData) > 1) {
            return null;
        }

        $requestFormName = empty($postData) ? UnsubscribeByMsisdnForm::NAME : array_keys($postData)[0];

        return empty($this->tabList[$requestFormName]) ? null : $requestFormName;
    }
}