<?php

namespace App\Admin\Controller;

use App\Domain\Service\VideoProcessing\VideoManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Admin\Form\UploadedVideoForm;

/**
 * Class UploadedVideoAdminController
 */
class UploadedVideoAdminController extends CRUDController
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var VideoManager
     */
    private $videoManager;

    /**
     * UploadedVideoAdminController constructor
     *
     * @param FormFactory $formFactory
     * @param VideoManager $videoManager
     */
    public function __construct(FormFactory $formFactory, VideoManager $videoManager)
    {
        $this->formFactory  = $formFactory;
        $this->videoManager = $videoManager;
    }

    public function uploadAction(Request $request)
    {
        $form = $this->formFactory->create(UploadedVideoForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams('@Admin/UploadedVideo/upload.html.twig', [
            'form' => $form->createView()
        ]);
    }
}