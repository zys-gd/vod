<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 16:45
 */

namespace App\Controller;


use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\CountryCategoryPriorityOverrideRepository;
use App\Domain\Repository\GameRepository;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController implements ControllerWithISPDetection, AppControllerInterface
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;
    /**
     * @var MainCategoryRepository
     */
    private $mainCategoryRepository;
    /**
     * @var UploadedVideoRepository
     */
    private $videoRepository;
    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * @var CountryCategoryPriorityOverrideRepository
     */
    private $categoryOverrideRepository;

    /**
     * HomeController constructor.
     *
     * @param CarrierRepositoryInterface $carrierRepository
     * @param TemplateConfigurator $templateConfigurator
     * @param MainCategoryRepository $mainCategoryRepository
     * @param UploadedVideoRepository $videoRepository
     * @param GameRepository $gameRepository
     * @param CountryCategoryPriorityOverrideRepository $categoryOverrideRepository
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        TemplateConfigurator $templateConfigurator,
        MainCategoryRepository $mainCategoryRepository,
        UploadedVideoRepository $videoRepository,
        GameRepository $gameRepository,
        CountryCategoryPriorityOverrideRepository $categoryOverrideRepository
    )
    {
        $this->carrierRepository          = $carrierRepository;
        $this->templateConfigurator       = $templateConfigurator;
        $this->mainCategoryRepository     = $mainCategoryRepository;
        $this->videoRepository            = $videoRepository;
        $this->gameRepository             = $gameRepository;
        $this->categoryOverrideRepository = $categoryOverrideRepository;
    }


    /**
     * @Route("/",name="index")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request, ISPData $data)
    {
        $carrier = $this->carrierRepository->findOneByBillingId($data->getCarrierId());
        $videos  = $this->videoRepository->findWithCategories();
        $categoryOverrides = $this->categoryOverrideRepository->findByBillingCarrierId($data->getCarrierId());

        $categories     = [];
        $categoryVideos = [];
        $sliderVideos   = [];
        /** @var UploadedVideo[] $videos */
        foreach ($videos as $video) {
            $categoryEntity = $video->getSubcategory()->getParent();
            $categoryKey    = $categoryEntity->getTitle();

            $videoData = [
                'uuid'       => $video->getUuid(),
                'title'      => $video->getTitle(),
                'publicId'   => $video->getRemoteId(),
                'thumbnails' => $video->getThumbnails()
            ];


            $viralVideosUUID = '15157409-49a4-4823-a7f9-654ac1d7c12f';
            if ($categoryEntity->getUuid() === $viralVideosUUID) {
                $sliderVideos[$categoryKey][$video->getUuid()] = $videoData;
            } else {
                $categoryVideos[$categoryKey][$video->getUuid()] = $videoData;
            }

            $categories[$categoryKey] = [
                'uuid'  => $categoryEntity->getUuid(),
                'title' => $categoryEntity->getTitle(),
            ];

        }

        return $this->render('@App/Common/home.html.twig', [
            'templateHandler' => $this->templateConfigurator->getTemplateHandler($carrier),
            'categoryVideos'  => $categoryVideos,
            'categories'      => $categories,
            'sliderVideos'    => $sliderVideos,
            'games'           => $this->gameRepository->findBatchOfGames(0, 2)
        ]);
    }
}