<?php

namespace DataFixtures;

use App\Domain\Entity\Subcategory;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSubcategoriesData extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('subcategories.json');

        foreach ($data as $row) {
            $uuid = $row['uuid'];
            $title = $row['title'];
            $alias = $row['alias'];
            $parentUuid = $row['parent'];

            $subcategory = new Subcategory($uuid);

            $subcategory
                ->setTitle($title)
                ->setAlias($alias)
                ->setParent($this->getReference(sprintf('main_category_%s', $parentUuid)));

            $manager->persist($subcategory);
        }

        $manager->flush();
    }
}