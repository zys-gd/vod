<?php

namespace App\Admin\Controller;

use App\Domain\Service\VideoProcessing\VideoManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Admin\Form\MultiUploadingForm;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UploadedVideoAdminController
 */
class UploadedVideoAdminController extends CRUDController
{
    const PRESET_WITH_TRIM = '';
    const PRESET_DEFAULT = 'efmyyi7p';

    private $presets = [
        'Default' => self::PRESET_DEFAULT,
        'Trim' => self::PRESET_WITH_TRIM
    ];

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
        $form = $this->formFactory->create(MultiUploadingForm::class, null, [
            'presets' => $this->presets
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams('@Admin/UploadedVideo/upload.html.twig', [
            'form' => $form->createView()
        ]);
    }

//    public function uploadAction(Request $request)
//    {
//        $test = $request;
//
//        return $this->renderWithExtraParams('@Admin/UploadedVideo/create.html.twig');
//    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function signatureAction(Request $request)
    {
        $requestData = $request->query->get('data');
        ksort($requestData);

        $preparedSignature = '';

        foreach ($requestData as $key => $value) {
            $preparedSignature .= empty($preparedSignature) ? $key . '=' . $value : '&' . $key . '=' . $value;
        }

        $preparedSignature .= 'sHmSwu7rTZqiAmfqFMM-XLl-r0k';

        $signature = sha1($preparedSignature);

        return new Response($signature);
    }
}