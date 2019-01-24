<?php

namespace DataFixtures;

use App\Domain\Entity\MainCategory;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadMainCategoriesData extends AbstractFixture
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
        $data = FixtureDataLoader::loadDataFromJSONFile('mainCategories.json');

        foreach ($data as $row) {
            $uuid = $row['uuid'];
            $title = $row['title'];
            $menuPriority = $row['menu_priority'];

            $category = new MainCategory($uuid);

            $category->setTitle($title);
            $category->setMenuPriority($menuPriority);

            $this->addReference(sprintf('main_category_%s', $uuid), $category);

            $manager->persist($category);
        }

        $manager->flush();
        $manager->clear();
    }
}