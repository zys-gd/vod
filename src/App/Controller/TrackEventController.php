<?php

namespace App\Controller;

use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\UploadedVideoRepository;
use App\Piwik\ContentStatisticSender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
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
     * @param UploadedVideoRepository                                       $uploadedVideoRepository
     * @param ContentStatisticSender                                        $contentStatisticSender
     * @param \SubscriptionBundle\Subscription\Common\SubscriptionExtractor $subscriptionExtractor
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
     * @Route("/track-video-play",name="track_video_play")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function trackVideoPlay(Request $request)
    {
        if (!$remoteId = $request->get('video', '')) {
            throw new BadRequestHttpException('Missing `video` parameter');
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