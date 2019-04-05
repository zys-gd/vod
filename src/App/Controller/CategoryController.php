<?php

namespace App\Controller;

use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\SubcategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\ContentStatisticSender;
use IdentificationBundle\Identification\DTO\ISPData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 */
class CategoryController extends AbstractController implements AppControllerInterface
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
     * @var ContentStatisticSender
     */
    private $contentStatisticSender;
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;

    /**
     * CategoryController constructor.
     *
     * @param MainCategoryRepository  $mainCategoryRepository
     * @param UploadedVideoRepository $uploadedVideoRepository
     * @param SubcategoryRepository   $subcategoryRepository
     * @param ContentStatisticSender  $contentStatisticSender
     * @param TemplateConfigurator    $templateConfigurator
     */
    public function __construct(
        MainCategoryRepository $mainCategoryRepository,
        UploadedVideoRepository $uploadedVideoRepository,
        SubcategoryRepository $subcategoryRepository,
        ContentStatisticSender $contentStatisticSender,
        TemplateConfigurator $templateConfigurator
    ) {
        $this->mainCategoryRepository  = $mainCategoryRepository;
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->subcategoryRepository   = $subcategoryRepository;
        $this->contentStatisticSender  = $contentStatisticSender;
        $this->templateConfigurator = $templateConfigurator;
    }


    /**
     * @Route("/category/{categoryUuid}",name="show_category")
     *
     * @param string $categoryUuid
     * @param Request $request
     * @param ISPData $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function showCategoryAction(string $categoryUuid, Request $request, ISPData $data)
    {
        if (!$category = $this->mainCategoryRepository->findOneBy(['uuid' => $categoryUuid])) {
            throw new NotFoundHttpException('Category is not found');
        }

        $subcategories = $this->subcategoryRepository->findBy(['parent' => $category]);

        if ($subcategoryUuid = $request->get('subcategoryUuid', '')) {
            $selectedSubcategory = $this->subcategoryRepository->findOneBy([
                'parent' => $category,
                'uuid'   => $subcategoryUuid
            ]);
            $videos = $this->uploadedVideoRepository->findNotExpiredBySubcategories([$selectedSubcategory]);
        } else {
            $videos = $this->uploadedVideoRepository->findNotExpiredBySubcategories($subcategories);
        }


        $categoryVideos = [];
        /** @var UploadedVideo[] $videos */
        foreach ($videos as $video) {
            $categoryEntity                                  = $video->getSubcategory()->getParent();
            $categoryKey                                     = $categoryEntity->getUuid();
            $categoryVideos[$categoryKey][$video->getUuid()] = $video->getDataFormTemplate();
        }

        $this->contentStatisticSender->trackVisit($data);

        $template = $this->templateConfigurator->getTemplate('category', $data->getCarrierId());
        return $this->render($template, [
            'videos'              => $videos,
            'category'            => $category,
            'subcategories'       => $subcategories,
            'categoryVideos'      => $categoryVideos,
            'selectedSubcategory' => $selectedSubcategory ?? null
        ]);
    }
}