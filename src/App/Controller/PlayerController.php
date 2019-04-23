<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.04.19
 * Time: 10:56
 */

namespace App\Controller;


use App\Domain\Entity\MainCategory;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlayerController extends AbstractController
{
    /**
     * @var UploadedVideoRepository
     */
    private $repository;
    /**
     * @var MainCategoryRepository
     */
    private $mainCategoryRepository;

    /**
     * PlayerController constructor.
     * @param UploadedVideoRepository $repository
     * @param MainCategoryRepository  $mainCategoryRepository
     */
    public function __construct(UploadedVideoRepository $repository, MainCategoryRepository $mainCategoryRepository)
    {
        $this->repository             = $repository;
        $this->mainCategoryRepository = $mainCategoryRepository;
    }


    /**
     * @Method("GET")
     * @Route("/related-videos",name="get_related_videos_block")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function getRelatedVideosAction(ISPData $data, IdentificationData $identificationData, Request $request): Response
    {

        if (!$categoryUuid = $request->get('categoryUuid')) {
            throw new BadRequestHttpException('Missing `categoryUuid` parameter');
        }

        /** @var MainCategory $category */
        if (!$category = $this->mainCategoryRepository->findOneBy(['uuid' => $categoryUuid])) {
            throw new NotFoundHttpException('Category not found');
        }


        $videos = $this->repository->findNotExpiredBySubcategories(
            $category->getSubcategories()->toArray()
        );

        return $this->render('@App/Components/player_related_videos.html.twig', [
            'videos'   => $videos,
            'category' => $category
        ]);
    }

}