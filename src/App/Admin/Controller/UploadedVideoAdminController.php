<?php

namespace App\Admin\Controller;

use App\Admin\Form\UploadedVideoForm;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use App\Admin\Form\PreUploadForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CloudinaryConnector
     */
    private $cloudinaryConnector;

    /**
     * @var string
     */
    private $cloudinaryApiKey;

    /**
     * @var string
     */
    private $cloudinaryCloudName;

    /**
     * @var string
     */
    private $cloudinaryApiSecret;

    /**
     * UploadedVideoAdminController constructor
     *
     * @param FormFactory $formFactory
     * @param EntityManagerInterface $entityManager
     * @param CloudinaryConnector $cloudinaryConnector
     * @param string $cloudinaryApiKey
     * @param string $cloudinaryCloudName
     * @param string $cloudinaryApiSecret
     */
    public function __construct(
        FormFactory $formFactory,
        EntityManagerInterface $entityManager,
        CloudinaryConnector $cloudinaryConnector,
        string $cloudinaryApiKey,
        string $cloudinaryCloudName,
        string $cloudinaryApiSecret
    ) {
        $this->formFactory  = $formFactory;
        $this->entityManager = $entityManager;
        $this->cloudinaryConnector = $cloudinaryConnector;
        $this->cloudinaryApiKey = $cloudinaryApiKey;
        $this->cloudinaryCloudName = $cloudinaryCloudName;
        $this->cloudinaryApiSecret = $cloudinaryApiSecret;
    }

    /**
     * @param Request $request
     *
     * @return Response
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
            /** @var UploadedVideo $uploadedVideo */
            $uploadedVideo = $form->getData();

            $preset = $form->get('preset')->getData();

            $widgetOptions = [
                'cloudName' => $this->cloudinaryCloudName,
                'apiKey' => $this->cloudinaryApiKey,
                'folder' => 'testWidgetFolder', //$uploadedVideo->getSubcategory()->getAlias(),
                'uploadPreset' => $preset,
                'sources' => ['local'],
                'resourceType' => 'video',
                'clientAllowedFormats' => ['mp4']
            ];

            return $this->renderWithExtraParams('@Admin/UploadedVideo/upload.html.twig', [
                'widgetOptions' => json_encode($widgetOptions),
                'preUploadFormData' => json_encode($uploadedVideo)
            ]);
        }

        return $this->renderWithExtraParams('@Admin/UploadedVideo/PreUpload/pre_upload.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response|BadRequestHttpException
     */
    public function saveBaseVideoDataAction(Request $request)
    {
        $form = $this->formFactory->create(UploadedVideoForm::class, null, [
            'presets' => $this->presets
        ]);

        $formData = json_decode($request->getContent(), true);

        $form->submit($formData);

        if ($form->isValid()) {
            /** @var UploadedVideo $uploadedVideo */
            $uploadedVideo = $form->getData();

            $thumbnails = $this->cloudinaryConnector->getThumbnails($uploadedVideo->getRemoteId());
            $uploadedVideo->setThumbnails($thumbnails);

            try {
                $this->entityManager->persist($uploadedVideo);
                $this->entityManager->flush();
            } catch (\Exception $exception) {
                return new Response('Error while saving uploaded video', 500);
            }

            return new Response(json_encode($uploadedVideo));
        }

        return new Response('Video data is invalid', 400);
    }

    public function savePostUploadVideoData(Request $request)
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

        $preparedSignature .= $this->cloudinaryApiSecret;

        $signature = sha1($preparedSignature);

        return new Response($signature);
    }
}