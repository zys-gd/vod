<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.10.19
 * Time: 17:11
 */

namespace App\Domain\Service;


use App\Domain\Entity\CountryCategoryPriorityOverride;
use App\Domain\Entity\MainCategory;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\CountryCategoryPriorityOverrideRepository;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\VideoProcessing\UploadedVideoSerializer;
use ExtrasBundle\Cache\ICacheService;
use ExtrasBundle\Utils\ArraySorter;
use IdentificationBundle\Identification\DTO\ISPData;

class HomepageVideoListProvider
{
    /**
     * @var UploadedVideoRepository
     */
    private $videoRepository;
    /**
     * @var MainCategoryRepository
     */
    private $mainCategoryRepository;
    /**
     * @var CountryCategoryPriorityOverrideRepository
     */
    private $categoryOverrideRepository;
    /**
     * @var UploadedVideoSerializer
     */
    private $videoSerializer;
    /**
     * @var ICacheService
     */
    private $cacheService;


    /**
     * HomepageVideoListProvider constructor.
     * @param UploadedVideoRepository                   $videoRepository
     * @param MainCategoryRepository                    $mainCategoryRepository
     * @param CountryCategoryPriorityOverrideRepository $categoryOverrideRepository
     * @param UploadedVideoSerializer                   $videoSerializer
     * @param ICacheService                             $cacheService
     */
    public function __construct(
        UploadedVideoRepository $videoRepository,
        MainCategoryRepository $mainCategoryRepository,
        CountryCategoryPriorityOverrideRepository $categoryOverrideRepository,
        UploadedVideoSerializer $videoSerializer,
        ICacheService $cacheService
    )
    {
        $this->videoRepository            = $videoRepository;
        $this->mainCategoryRepository     = $mainCategoryRepository;
        $this->categoryOverrideRepository = $categoryOverrideRepository;
        $this->videoSerializer            = $videoSerializer;
        $this->cacheService               = $cacheService;
    }

    /**
     * @param ISPData $data
     * @param int     $countForEachCategory
     *
     * @return array
     * @throws \Exception
     */
    public function getVideosForHomepage(ISPData $data, $countForEachCategory = 4): array
    {

        $categoryOverrides   = $this->categoryOverrideRepository->findByBillingCarrierId($data->getCarrierId());
        $categories          = $this->mainCategoryRepository->findAll();
        $indexedCategoryData = $this->getIndexedCategoryData($categories, $categoryOverrides);

        $resolvedIds = $this->resolveIdsToSelect($countForEachCategory);
        $videos      = $this->videoRepository->findWithCategories($resolvedIds);

        $categorizedVideos = [];
        /** @var UploadedVideo[] $videos */
        foreach ($videos as $video) {

            $categoryEntity                                     = $video->getSubcategory()->getParent();
            $categoryKey                                        = $categoryEntity->getTitle();
            $categorizedVideos[$categoryKey][$video->getUuid()] = $this->videoSerializer->serializeShort($video);
        }


        $categorizedVideos = array_slice(
            ArraySorter::sortArrayByKeys(
                $categorizedVideos,
                array_keys($indexedCategoryData)
            ),
            0,
            5
        );

        return [$categorizedVideos, $indexedCategoryData];
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

    /**
     * @param $countForEachCategory
     * @return array
     * @throws \Exception
     */
    private function resolveIdsToSelect($countForEachCategory): array
    {
        if (!$flattenIds = $this->cacheService->getValue('homepage.video_list.ids')) {

            $ids        = $this->videoRepository->findIdsOfNotExpiredVideos();
            $indexedIds = [];
            foreach ($ids as $row) {
                $categoryName                = $row['categoryName'];
                $indexedIds[$categoryName][] = $row['videoId'];
            }
            $slicedIds = [];
            foreach ($indexedIds as $categoryName => $videoIds) {
                $slicedIds[$categoryName] = array_slice($videoIds, 0, $countForEachCategory);
            }

            $flattenIds = [];
            foreach ($slicedIds as $categoryName => $videoIds) {
                $flattenIds = array_merge($flattenIds, $videoIds);
            }

            $this->cacheService->saveCache('homepage.video_list.ids', $flattenIds, 20);
        }
        return $flattenIds;
    }
}