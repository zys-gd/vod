<?php

namespace App\Controller;

use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\PageVisitTracker;
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
     * @var PageVisitTracker
     */
    private $pageVisitTracker;

    /**
     * TrackEventController constructor
     *
     * @param UploadedVideoRepository $uploadedVideoRepository
     * @param PageVisitTracker $pageVisitTracker
     */
    public function __construct(UploadedVideoRepository $uploadedVideoRepository, PageVisitTracker $pageVisitTracker)
    {
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->pageVisitTracker = $pageVisitTracker;
    }

    /**
     * @Method("POST")
     * @Route("/track-video-percent-event",name="track_video_percent_event")
     */
    public function trackVideoPercentEvent(Request $request)
    {

        if (!$remoteId = $request->get('video', '')) {
            throw new BadRequestHttpException('Missing `video` parameter');
        }
        if (!$percent = (int)$request->get('percent', 0)) {
            throw new BadRequestHttpException('Missing `percent` parameter');
        }

        try {
            $video = $this->uploadedVideoRepository->getOneBy(['video' => $percent]);

            return new JsonResponse(['result' => true]);
        } catch (\Exception $exception) {
            return new JsonResponse(['result' => false]);
        }
    }
}