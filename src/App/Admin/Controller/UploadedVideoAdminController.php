<?php

namespace App\Admin\Controller;

use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\VideoManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    /**
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function uploadAction(Request $request)
    {
        $form = $this->formFactory->create(UploadedVideoForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedVideo $uploadedVideo */
            $uploadedVideo = $form->getData();
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('file')->getData();

            $options = [
                'start_offset' => $form->get('startOffset')->getData()
            ];

            $uploadResult = $this
                ->videoManager
                ->uploadVideoFileToStorage($uploadedFile, $uploadedVideo->getSubcategory()->getAlias(), $options);

            $this->videoManager->persistUploadedVideo($uploadResult, $uploadedVideo);

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams('@Admin/UploadedVideo/upload.html.twig', [
            'form' => $form->createView()
        ]);
    }
}