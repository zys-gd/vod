<?php

namespace App\Controller;

use App\Domain\DTO\BatchOfNotExpiredVideos;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\VideoProcessing\UploadedVideoSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VideoController
 */
class VideoController extends AbstractController implements AppControllerInterface
{
    /** @var UploadedVideoRepository */
    private $videoRepository;

    /** @var UploadedVideoSerializer */
    private $videoSerializer;

    /** @var MainCategoryRepository */
    private $mainCategoryRepository;

    /** @var UploadedVideoRepository */
    private $uploadedVideoRepository;

    /**
     * VideosController constructor
     *
     * @param UploadedVideoRepository $videoRepository
     * @param MainCategoryRepository $mainCategoryRepository
     * @param UploadedVideoSerializer $videoSerializer
     * @param UploadedVideoRepository $uploadedVideoRepository
     */
    public function __construct(
        UploadedVideoRepository $videoRepository,
        MainCategoryRepository $mainCategoryRepository,
        UploadedVideoSerializer $videoSerializer,
        UploadedVideoRepository $uploadedVideoRepository
    ) {
        $this->videoRepository = $videoRepository;
        $this->mainCategoryRepository = $mainCategoryRepository;
        $this->videoSerializer = $videoSerializer;
        $this->uploadedVideoRepository = $uploadedVideoRepository;
    }

    /**
     * @Route("/video/load-more", methods={"GET"}, name="load_more_video")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function loadMore(Request $request)
    {
        if (!$videoPublicId = $request->get('videoPublicId')) {
            throw new BadRequestHttpException('Missing `videoPublicId` parameter');
        }

        /** @var UploadedVideo $uploadedVideo */
        if (!$uploadedVideo = $this->uploadedVideoRepository->findOneBy(['remoteId' => $videoPublicId])) {
            throw new NotFoundHttpException('Category not found');
        }

        $offset = $request->get('offset', 0);

        /** @var BatchOfNotExpiredVideos $videos */
        $videos = $this->videoRepository->findNotExpiredBySubcategories(
            $uploadedVideo->getSubcategory()->getParent()->getSubcategories()->toArray(),
            $offset
        );

        $serializedData = [];

        foreach ($videos->getVideos() as $video) {
            $serializedData[] = $this->videoSerializer->serializeShort($video);
        }

        $html = $this->renderView('@App/Components/related_videos.html.twig', [
            'videos'   => $serializedData
        ]);

        return new JsonResponse([
            'html' => $html,
            'isLast' => $videos->isLast()
        ]);
    }
}