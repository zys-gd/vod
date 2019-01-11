<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 26.12.18
 * Time: 10:53
 */

namespace App\Admin\Controller;


use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Admin\Form\UploadVideoForm;
use App\Domain\Service\VideoProcessing\VideoUploader;

class UploadedVideoAdminController extends CRUDController
{
    /**
     * @var FormFactory
     */
    private $factory;
    /**
     * @var VideoUploader
     */
    private $uploader;


    /**
     * UploadedVideoAdminController constructor.
     */
    public function __construct(FormFactory $factory, VideoUploader $uploader)
    {
        $this->factory  = $factory;
        $this->uploader = $uploader;
    }

    public function uploadAction(Request $request)
    {
        $form = $this->factory->create(UploadVideoForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entity = $this->uploader->uploadVideo($data);

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams('@Admin/UploadedVideo/upload.html.twig', [
            'form' => $form->createView()
        ]);
    }
}