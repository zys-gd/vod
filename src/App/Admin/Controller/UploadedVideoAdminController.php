<?php

namespace App\Admin\Controller;

use App\Admin\Form\PreUploadForm;
use App\Admin\Form\UploadedVideoForm;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;
use App\Domain\Service\VideoProcessing\UploadedVideoSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CloudinaryConnector
     */
    private $cloudinaryConnector;

    /**
     * @var UploadedVideoSerializer
     */
    private $uploadedVideoSerializer;

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
     * @var array
     */
    private $presets;

    /**
     * UploadedVideoAdminController constructor
     *
     * @param FormFactory             $formFactory
     * @param EntityManagerInterface  $entityManager
     * @param CloudinaryConnector     $cloudinaryConnector
     * @param UploadedVideoSerializer $uploadedVideoSerializer
     * @param string                  $cloudinaryApiKey
     * @param string                  $cloudinaryCloudName
     * @param string                  $cloudinaryApiSecret
     * @param string                  $defaultPreset
     * @param string                  $trimPreset
     */
    public function __construct(
        FormFactory $formFactory,
        EntityManagerInterface $entityManager,
        CloudinaryConnector $cloudinaryConnector,
        UploadedVideoSerializer $uploadedVideoSerializer,
        string $cloudinaryApiKey,
        string $cloudinaryCloudName,
        string $cloudinaryApiSecret,
        string $defaultPreset,
        string $trimPreset
    )
    {
        $this->formFactory             = $formFactory;
        $this->entityManager           = $entityManager;
        $this->cloudinaryConnector     = $cloudinaryConnector;
        $this->uploadedVideoSerializer = $uploadedVideoSerializer;
        $this->cloudinaryApiKey        = $cloudinaryApiKey;
        $this->cloudinaryCloudName     = $cloudinaryCloudName;
        $this->cloudinaryApiSecret     = $cloudinaryApiSecret;

        $this->presets = [
            'Default'       => $defaultPreset,
            'Trim for SNTV' => $trimPreset
        ];
    }

    /**
     * @param Request $request
     *
     * @return Response
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
                'cloudName'            => $this->cloudinaryCloudName,
                'apiKey'               => $this->cloudinaryApiKey,
                'folder'               => $uploadedVideo->getSubcategory()->getAlias(),
                'uploadPreset'         => $preset,
                'sources'              => ['local'],
                'resourceType'         => 'video',
                'clientAllowedFormats' => ['mp4']
            ];

            return $this->renderWithExtraParams('@Admin/UploadedVideo/upload.html.twig', [
                'widgetOptions'     => json_encode($widgetOptions),
                'preUploadFormData' => $this->uploadedVideoSerializer->serializeJson($uploadedVideo)
            ]);
        }

        return $this->renderWithExtraParams('@Admin/UploadedVideo/PreUpload/pre_upload.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|Response|BadRequestHttpException
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

            return new Response($this->uploadedVideoSerializer->serializeJson($uploadedVideo));
        }

        return new Response('Video data is invalid', 400);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|Response
     * @throws \Exception
     */
    public function confirmVideosAction(Request $request)
    {
        $confirmedVideos = json_decode($request->getContent(), true);

        $uploadedVideoRepository = $this->entityManager->getRepository(UploadedVideo::class);

        try {
            foreach ($confirmedVideos as $uuid => $confirmedData) {
                /** @var UploadedVideo $uploadedVideo */
                $uploadedVideo = $uploadedVideoRepository->find($uuid);

                if (empty($uploadedVideo)) {
                    continue;
                }

                $uploadedVideo
                    ->setTitle($confirmedData['title'])
                    ->setDescription($confirmedData['description'])
                    ->updateStatus(UploadedVideo::STATUS_CONFIRMED_BY_ADMIN);

                if (!empty($confirmedData['expiredDate'])) {
                    $uploadedVideo->setExpiredDate(new \DateTime($confirmedData['expiredDate']));
                }

                $this->entityManager->persist($uploadedVideo);
            }

            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return new Response('An error occurred while saving the video', 500);
        }

        return new JsonResponse(['result' => 'ok']);
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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function pingAction(Request $request)
    {
        return new Response();
    }
}