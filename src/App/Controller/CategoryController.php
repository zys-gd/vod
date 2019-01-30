<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.01.19
 * Time: 17:40
 */

namespace App\Controller;


use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\SubcategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @var CategoryController
     */
    private $mainCategoryRepository;
    /**
     * @var UploadedVideoRepository
     */
    private $uploadedVideoRepository;
    /**
     * @var SubcategoryRepository
     */
    private $subcategoryRepository;

    /**
     * CategoryController constructor.
     * @param MainCategoryRepository  $mainCategoryRepository
     * @param UploadedVideoRepository $uploadedVideoRepository
     */
    public function __construct(MainCategoryRepository $mainCategoryRepository, UploadedVideoRepository $uploadedVideoRepository, SubcategoryRepository $subcategoryRepository)
    {
        $this->mainCategoryRepository  = $mainCategoryRepository;
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->subcategoryRepository   = $subcategoryRepository;
    }


    /**
     * @Route("/category/{categoryUuid}",name="show_category")
     * @param string $categoryUuid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryAction(string $categoryUuid, Request $request)
    {
        if (!$category = $this->mainCategoryRepository->findOneBy(['uuid' => $categoryUuid])) {
            throw new NotFoundHttpException('Category is not found');
        }

        $subcategories = $this->subcategoryRepository->findBy(['parent' => $category]);

        if ($subcategoryUuid = $request->get('subcategoryUuid', '')) {
            $selectedSubcategory = $this->subcategoryRepository->findOneBy(['parent' => $category, 'uuid' => $subcategoryUuid]);
            $videos              = $this->uploadedVideoRepository->findBy(['subcategory' => $selectedSubcategory]);
        } else {
            $videos = $this->uploadedVideoRepository->findBy(['subcategory' => $subcategories]);
        }


        $categoryVideos = [];
        /** @var UploadedVideo[] $videos */
        foreach ($videos as $video) {
            $categoryEntity                                  = $video->getSubcategory()->getParent();
            $categoryKey                                     = $categoryEntity->getUuid();
            $categoryVideos[$categoryKey][$video->getUuid()] = [
                'uuid'       => $video->getUuid(),
                'title'      => $video->getTitle(),
                'publicId'   => $video->getRemoteId(),
                'thumbnails' => $video->getThumbnails()
            ];
        }


        return $this->render('@App/Common/category.html.twig', [
            'videos'              => $videos,
            'category'            => $category,
            'subcategories'       => $subcategories,
            'categoryVideos'      => $categoryVideos,
            'selectedSubcategory' => $selectedSubcategory ?? null
        ]);

    }

}