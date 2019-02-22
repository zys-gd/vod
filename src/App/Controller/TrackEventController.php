<?php

namespace App\Controller;

use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\ContentStatisticSender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class TrackEventController
 */
class TrackEventController extends AbstractController
{
    /**
     * @var UploadedVideoRepository
     */
    private $uploadedVideoRepository;

    /**
     * @var ContentStatisticSender
     */
    private $contentStatisticSender;

    /**
     * TrackEventController constructor
     *
     * @param UploadedVideoRepository $uploadedVideoRepository
     * @param ContentStatisticSender $contentStatisticSender
     */
    public function __construct(
        UploadedVideoRepository $uploadedVideoRepository,
        ContentStatisticSender $contentStatisticSender
    ) {
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->contentStatisticSender = $contentStatisticSender;
    }

    /**
     * @Method("POST")
     * @Route("/track-video-percent-event",name="track_video_percent_event")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function trackVideoPercentEvent(Request $request)
    {
        if (!$remoteId = $request->get('video', '')) {
            throw new BadRequestHttpException('Missing `video` parameter');
        }

        if (!$percent = (int) $request->get('percent', 0)) {
            throw new BadRequestHttpException('Missing `percent` parameter');
        }

        try {
            /** @var UploadedVideo $video */
            $video = $this->uploadedVideoRepository->findOneBy(['remoteId' => $remoteId]);
            $this->contentStatisticSender->trackPlayingVideo($video);

            return new JsonResponse(['result' => true]);
        } catch (\Exception $exception) {
            return new JsonResponse(['result' => false]);
        }
    }
}