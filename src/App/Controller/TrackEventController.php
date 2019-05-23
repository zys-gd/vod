<?php

namespace App\Controller;

use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\Piwik\ContentStatisticSender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SubscriptionBundle\Service\SubscriptionExtractor;
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
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;

    /**
     * TrackEventController constructor
     *
     * @param UploadedVideoRepository $uploadedVideoRepository
     * @param ContentStatisticSender  $contentStatisticSender
     * @param SubscriptionExtractor   $subscriptionExtractor
     */
    public function __construct(
        UploadedVideoRepository $uploadedVideoRepository,
        ContentStatisticSender $contentStatisticSender,
        SubscriptionExtractor $subscriptionExtractor
    )
    {
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->contentStatisticSender  = $contentStatisticSender;
        $this->subscriptionExtractor = $subscriptionExtractor;
    }

    /**
     * @Method("POST")
     * @Route("/track-video-percent-event",name="track_video_percent_event")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function trackVideoPercentEvent(Request $request)
    {
        if (!$remoteId = $request->get('video', '')) {
            throw new BadRequestHttpException('Missing `video` parameter');
        }

        if (!$percent = (int)$request->get('percent', 0)) {
            throw new BadRequestHttpException('Missing `percent` parameter');
        }

        $subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($request->getSession());

        if (!$subscription) {
            throw new BadRequestHttpException('You are not subscribed');
        }

        try {
            /** @var UploadedVideo $video */
            $video = $this->uploadedVideoRepository->findOneBy(['remoteId' => $remoteId]);
            $this->contentStatisticSender->trackPlayingVideo($video, $subscription);

            return new JsonResponse(['result' => true]);
        } catch (\Exception $exception) {
            return new JsonResponse(['result' => false]);
        }
    }
}