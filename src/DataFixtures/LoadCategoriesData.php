<?php

namespace DataFixtures;

use App\Domain\Entity\Category;
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
     */
    public function load(ObjectManager $manager)
    {
        $data = [
            ["Sports", "sports", "44860f37-c9b7-4c41-99f1-1483af0f424e", 0, null],
            ["Hokey", "hokey", "44860f37-c9b7-4c41-89f1-1483dhr7424a", 0, "44860f37-c9b7-4c41-99f1-1483af0f424e"],
            ["Football", "football", "44d84k37-c9b7-4c41-99f1-1484850f424r", 0, "44860f37-c9b7-4c41-99f1-1483af0f424e"],
            ["Volleyball", "volleyball", "44860f37-c9b7-4h71-99f1-1483af0fl64k", 0, "44860f37-c9b7-4c41-99f1-1483af0f424e"]
        ];

        foreach ($data as $row) {

            $title = $row[0];
            $alias = $row[1];
            $uuid  = $row[2];
            $menuPriority = $row[3];

            $category = new Category($uuid);

            $category->setTitle($title);
            $category->setAlias($alias);
            $category->setMenuPriority($menuPriority);

            $manager->persist($category);
        }

        $manager->flush();
        $manager->clear();

        foreach ($data as $row) {
            $uuid  = $row[2];
            $parentUuid = $row[4];

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