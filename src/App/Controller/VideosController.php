<?php


namespace App\Controller;

use App\Domain\DTO\BatchOfNotExpiredVideos;
use App\Domain\Entity\MainCategory;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\VideoProcessing\UploadedVideoSerializer;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class VideosController extends AbstractController implements AppControllerInterface
{
    /** @var UploadedVideoRepository */
    private $videoRepository;

    /** @var UploadedVideoSerializer */
    private $videoSerializer;

    /** @var MainCategoryRepository */
    private $mainCategoryRepository;

    /**
     * VideosController constructor.
     * @param UploadedVideoRepository $videoRepository
     * @param MainCategoryRepository $mainCategoryRepository
     * @param UploadedVideoSerializer $videoSerializer
     */
    public function __construct(
        UploadedVideoRepository $videoRepository,
        MainCategoryRepository $mainCategoryRepository,
        UploadedVideoSerializer $videoSerializer
    )
    {
        $this->videoRepository = $videoRepository;
        $this->mainCategoryRepository = $mainCategoryRepository;
        $this->videoSerializer = $videoSerializer;
    }

    /**
     * @Route("/videos/category/load-more", methods={"GET"}, name="load_more_category_videos")
     *
     * @param ISPData $data
     * @param IdentificationData $identificationData
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function loadMoreCategoryVideosAction(ISPData $data, IdentificationData $identificationData, Request $request)
    {

        if (!$categoryUuid = $request->get('categoryUuid')) {
            throw new BadRequestHttpException('Missing `categoryUuid` parameter');
        }

        /** @var MainCategory $category */
        if (!$category = $this->mainCategoryRepository->findOneBy(['uuid' => $categoryUuid])) {
            throw new NotFoundHttpException('Category not found');
        }

        $offset = $request->get('offset', 0);

        /** @var BatchOfNotExpiredVideos $videos */
        $videos = $this->videoRepository->findNotExpiredBySubcategories(
            $category->getSubcategories()->toArray(),
            $offset
        );

        $serializedData = [];

        foreach ($videos->getVideos() as $video) {
            $serializedData[] = $this->videoSerializer->serializeShort($video);
        }

        $html = $this->renderView('@App/Components/category_player_related_videos.html.twig', [
            'videos'   => $serializedData,
            'category' => $category
        ]);

        return new JsonResponse([
            'html' => $html,
            'isLast' => $videos->isLast()
        ]);
    }

    /**
     * @Route("/videos/load-more", methods={"GET"}, name="load_more_related_videos")
     *
     * @param ISPData $data
     * @param IdentificationData $identificationData
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function loadMoreRelatedVideoAction(ISPData $data, IdentificationData $identificationData, Request $request)
    {

        if (!$categoryUuid = $request->get('categoryUuid')) {
            throw new BadRequestHttpException('Missing `categoryUuid` parameter');
        }

        /** @var MainCategory $category */
        if (!$category = $this->mainCategoryRepository->findOneBy(['uuid' => $categoryUuid])) {
            throw new NotFoundHttpException('Category not found');
        }

        $offset = $request->get('offset', 0);

        /** @var BatchOfNotExpiredVideos $videos */
        $videos = $this->videoRepository->findNotExpiredBySubcategories(
            $category->getSubcategories()->toArray(),
            $offset,
            20
        );

        $serializedData = [];

        foreach ($videos->getVideos() as $video) {
            $serializedData[] = $this->videoSerializer->serializeShort($video);
        }

        $html = $this->renderView('@App/Components/player_related_videos.html.twig', [
            'videos' => $serializedData,
            'category' => $category
        ]);

        return new JsonResponse([
            'html' => $html,
            'isLast' => $videos->isLast()
        ]);
    }
}