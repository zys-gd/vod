<?php

namespace SubscriptionBundle\Admin\Controller;

use SubscriptionBundle\Admin\Form\RefundForm;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

class RefundAdminController extends CRUDController
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * RefundAdminController constructor
     *
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createAction(Request $request = null)
    {
        $form = $this->formFactory->create(RefundForm::class);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->renderWithExtraParams('@SubscriptionAdmin/Refund/create.html.twig', [
            'error' => $error,
            'form' => $form->createView()
        ]);
    }
}