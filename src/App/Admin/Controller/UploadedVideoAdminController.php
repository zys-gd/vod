<?php

namespace App\Admin\Controller;

use App\Domain\Entity\Subcategory;
use App\Domain\Service\VideoProcessing\VideoManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Admin\Form\PreUploadForm;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UploadedVideoAdminController
 */
class UploadedVideoAdminController extends CRUDController
{
    const PRESET_TRIM = 'trim_sntv_stage';
    const PRESET_DEFAULT = 'default_stage';

    private $presets = [
        'Default' => self::PRESET_DEFAULT,
        'Trim for SNTV' => self::PRESET_TRIM
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
    public function preUploadAction(Request $request)
    {
        $form = $this->formFactory->create(PreUploadForm::class, null, [
            'presets' => $this->presets
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            /** @var Subcategory $subcategory */
            $subcategory = $formData['subcategory'];

            $widgetOptions = [
                'cloudName' => 'origindata',
                'apiKey' => '187818276162186',
                'folder' => 'testWidgetFolder', //$subcategory->getAlias(),
                'uploadPreset' => $formData['preset'],
                'sources' => ['local']
            ];

            return $this->renderWithExtraParams('@Admin/UploadedVideo/upload.html.twig', [
                'widgetOptions' => json_encode($widgetOptions),
                'formData' => json_encode($formData)
            ]);
        }

        return $this->renderWithExtraParams('@Admin/UploadedVideo/PreUpload/pre_upload.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function saveBaseVideoDataAction(Request $request)
    {

    }

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