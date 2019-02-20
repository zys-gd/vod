<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 20.02.19
 * Time: 10:50
 */

namespace App\Controller;


use App\Domain\Repository\UploadedVideoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TrackEventController extends AbstractController
{
    /**
     * @var UploadedVideoRepository
     */
    private $uploadedVideoRepository;


    /**
     * TrackEventController constructor.
     * @param UploadedVideoRepository $uploadedVideoRepository
     */
    public function __construct(UploadedVideoRepository $uploadedVideoRepository)
    {
        $this->uploadedVideoRepository = $uploadedVideoRepository;
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