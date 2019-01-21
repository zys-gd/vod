<?php

namespace DataFixtures;

use App\Domain\Entity\Category;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadCategoriesData extends AbstractFixture
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('categories.json');

        foreach ($data as $row) {
            $uuid = $row['uuid'];
            $title = $row['title'];
            $alias = $row['alias'];
            $menuPriority = $row['menu_priority'];

            $category = new Category($uuid);

            $category->setTitle($title);
            $category->setAlias($alias);
            $category->setMenuPriority($menuPriority);

            $manager->persist($category);
        }

        $manager->flush();
        $manager->clear();

        foreach ($data as $row) {
            $uuid  = $row['uuid'];
            $parentUuid = $row['parent'];

            if ($parentUuid) {
                $parentCategory = $manager->find(Category::class, $parentUuid);
                $childCategory = $manager->find(Category::class, $uuid);

                $childCategory->setParent($parentCategory);

                $manager->persist($childCategory);
            }
        }

        $manager->flush();
        $manager->clear();
    }
}