<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.01.19
 * Time: 18:20
 */

namespace App\Twig;


use App\Domain\Entity\MainCategory;
use App\Domain\Repository\MainCategoryRepository;
use Symfony\Component\Routing\RouterInterface;

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
     * NavbarExtension constructor.
     * @param MainCategoryRepository $mainCategoryRepository
     */
    public function __construct(MainCategoryRepository $mainCategoryRepository, RouterInterface $router)
    {
        $this->mainCategoryRepository = $mainCategoryRepository;
        $this->router                 = $router;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getMenuElements', function () {

                /** @var MainCategory[] $categories */
                $categories = $this->mainCategoryRepository->findAll();


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