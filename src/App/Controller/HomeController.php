<?php

namespace App\Controller;

use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\DTO\BatchOfGames;
use App\Domain\Entity\CountryCategoryPriorityOverride;
use App\Domain\Entity\MainCategory;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\CountryCategoryPriorityOverrideRepository;
use App\Domain\Repository\GameRepository;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use App\Piwik\ContentStatisticSender;
use App\Domain\Service\VideoProcessing\UploadedVideoSerializer;
use ExtrasBundle\Utils\ArraySorter;
use IdentificationBundle\Controller\ControllerWithIdentification;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\AlreadySubscribedIdentFinisher;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 */
class HomeController extends AbstractController implements
    ControllerWithISPDetection,
    AppControllerInterface,
    ControllerWithIdentification
{
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
     * @var ContentStatisticSender
     */
    private $contentStatisticSender;

    /**
     * @var UploadedVideoSerializer
     */
    private $videoSerializer;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var AlreadySubscribedIdentFinisher
     */
    private $alreadySubscribedIdentFinisher;

    /**
     * HomeController constructor.
     *
     * @param TemplateConfigurator $templateConfigurator
     * @param MainCategoryRepository $mainCategoryRepository
     * @param UploadedVideoRepository $videoRepository
     * @param UploadedVideoSerializer $videoSerializer
     * @param GameRepository $gameRepository
     * @param CountryCategoryPriorityOverrideRepository $categoryOverrideRepository
     * @param ContentStatisticSender $contentStatisticSender
     * @param UserRepository $userRepository
     * @param AlreadySubscribedIdentFinisher $alreadySubscribedIdentFinisher
     */
    public function __construct(
        TemplateConfigurator $templateConfigurator,
        MainCategoryRepository $mainCategoryRepository,
        UploadedVideoRepository $videoRepository,
        UploadedVideoSerializer $videoSerializer,
        GameRepository $gameRepository,
        CountryCategoryPriorityOverrideRepository $categoryOverrideRepository,
        ContentStatisticSender $contentStatisticSender,
        UserRepository $userRepository,
        AlreadySubscribedIdentFinisher $alreadySubscribedIdentFinisher
    ) {
        $this->templateConfigurator           = $templateConfigurator;
        $this->mainCategoryRepository         = $mainCategoryRepository;
        $this->videoRepository                = $videoRepository;
        $this->videoSerializer                = $videoSerializer;
        $this->gameRepository                 = $gameRepository;
        $this->categoryOverrideRepository     = $categoryOverrideRepository;
        $this->contentStatisticSender         = $contentStatisticSender;
        $this->userRepository                 = $userRepository;
        $this->alreadySubscribedIdentFinisher = $alreadySubscribedIdentFinisher;
    }

    /**
     * @Route("/",name="index")
     * @param Request $request
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function indexAction(Request $request, ISPData $data)
    {
        /**
         * @var BatchOfGames $games
         */
        $videos            = $this->videoRepository->findNotExpiredWithCategories();
        $games             = $this->gameRepository->findBatchOfGames(0, 2);
        $categoryOverrides = $this->categoryOverrideRepository->findByBillingCarrierId($data->getCarrierId());
        $categories        = $this->mainCategoryRepository->findAll();

        $indexedCategoryData = $this->getIndexedCategoryData($categories, $categoryOverrides);

        $categoryVideos = [];
        /** @var UploadedVideo[] $videos */
        foreach ($videos as $video) {

            $categoryEntity                                  = $video->getSubcategory()->getParent();
            $categoryKey                                     = $categoryEntity->getTitle();
            $categoryVideos[$categoryKey][$video->getUuid()] = $this->videoSerializer->serializeShort($video);
        }

        $categoryVideos = array_slice(
            ArraySorter::sortArrayByKeys(
                $categoryVideos,
                array_keys($indexedCategoryData)
            ),
            0,
            5
        );

        if ($this->alreadySubscribedIdentFinisher->needToHandle($request)) {
            $this->alreadySubscribedIdentFinisher->tryToIdentify($request);
        }

        $this->contentStatisticSender->trackVisit($request->getSession());

        $template = $this->templateConfigurator->getTemplate('home', $data->getCarrierId());

        return $this->render($template, [
            'categoryVideos'  => array_slice($categoryVideos, 1, 3),
            'categories'      => $indexedCategoryData,
            'sliderVideos'    => array_slice($categoryVideos, 0, 1),
            'games'           => $games->getGames()
        ]);
    }

    /**
     * @param array $categories
     * @param array $categoryOverrides
     *
     * @return array
     */
    private function getIndexedCategoryData(array $categories, array $categoryOverrides): array
    {
        $categoryData = [];
        /** @var MainCategory[] $categories */
        foreach ($categories as $category) {
            $categoryData[$category->getUuid()] = [
                'uuid'         => $category->getUuid(),
                'title'        => $category->getTitle(),
                'menuPriority' => $category->getMenuPriority()
            ];
        }

        /** @var CountryCategoryPriorityOverride[] $categoryOverrides */
        foreach ($categoryOverrides as $categoryOverride) {
            $category                           = $categoryOverride->getMainCategory();
            $categoryData[$category->getUuid()] = [
                'uuid'         => $category->getUuid(),
                'title'        => $category->getTitle(),
                'menuPriority' => $categoryOverride->getMenuPriority()
            ];
        }

        usort($categoryData, function ($a, $b) {
            return $a['menuPriority'] - $b['menuPriority'];
        });

        $indexedCategoryData = [];
        foreach ($categoryData as $categoryRow) {
            $indexedCategoryData[$categoryRow['title']] = $categoryRow;
        }

        return $indexedCategoryData;
    }
}