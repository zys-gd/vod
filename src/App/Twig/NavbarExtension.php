<?php

namespace App\Twig;

use App\Domain\Entity\CountryCategoryPriorityOverride;
use App\Domain\Entity\MainCategory;
use App\Domain\Repository\CountryCategoryPriorityOverrideRepository;
use App\Domain\Repository\MainCategoryRepository;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class NavbarExtension
 */
class NavbarExtension extends \Twig_Extension
{
    /**
     * @var MainCategoryRepository
     */
    private $mainCategoryRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var CountryCategoryPriorityOverrideRepository
     */
    private $categoryPriorityOverrideRepository;

    /**
     * NavbarExtension constructor
     *
     * @param MainCategoryRepository                    $mainCategoryRepository
     * @param RouterInterface                           $router
     * @param Session                                   $session
     * @param CountryCategoryPriorityOverrideRepository $categoryPriorityOverrideRepository
     */
    public function __construct(
        MainCategoryRepository $mainCategoryRepository,
        RouterInterface $router,
        Session $session,
        CountryCategoryPriorityOverrideRepository $categoryPriorityOverrideRepository
    )
    {
        $this->mainCategoryRepository             = $mainCategoryRepository;
        $this->router                             = $router;
        $this->session                            = $session;
        $this->categoryPriorityOverrideRepository = $categoryPriorityOverrideRepository;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getMenuElements', function () {
                /** @var MainCategory[] $categories */
                $categories = $this->mainCategoryRepository->findWithSubcategories();

                $ispData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
                if ($ispData) {
                    $categoryOverrides = $this
                        ->categoryPriorityOverrideRepository
                        ->findByBillingCarrierId($ispData['carrier_id']);
                }

                if (!empty($categoryOverrides)) {
                    $mainCategoryOverride = array_map(function (CountryCategoryPriorityOverride $categoryOverride) {
                        $mainCategory = $categoryOverride->getMainCategory();
                        $mainCategory->setMenuPriority($categoryOverride->getMenuPriority());

                        return $mainCategory;
                    }, $categoryOverrides);

                    $categories = array_unique(array_merge($mainCategoryOverride, $categories), SORT_STRING);
                }

                usort($categories, function (MainCategory $a, MainCategory $b) {
                    return $a->getMenuPriority() - $b->getMenuPriority();
                });

                $result = [];
                foreach ($categories as $category) {
                    $subitems = [];

                    foreach ($category->getSubcategories() as $subcategory) {
                        $subitems[] = [
                            'title' => $subcategory->getTitle(),
                            'link'  => $this->router->generate('show_category', [
                                'categoryUuid'    => $category->getUuid(),
                                'subcategoryUuid' => $subcategory->getUuid()
                            ])
                        ];
                    }

                    $result[] = [
                        'link'     => $this->router->generate('show_category', ['categoryUuid' => $category->getUuid()]),
                        'title'    => $category->getTitle(),
                        'subitems' => $subitems
                    ];
                }
                return $result;

            })
        ];
    }
}