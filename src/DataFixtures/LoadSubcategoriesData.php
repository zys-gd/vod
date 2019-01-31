<?php

namespace DataFixtures;

use App\Domain\Entity\Subcategory;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSubcategoriesData extends AbstractFixture implements DependentFixtureInterface
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
            $uuid       = $row['uuid'];
            $title      = $row['title'];
            $alias      = $row['alias'];
            $parentUuid = $row['parent']['uuid'];

            $subcategory = new Subcategory($uuid);

            $subcategory
                ->setTitle($title)
                ->setAlias($alias)
                ->setParent($this->getReference(sprintf('main_category_%s', $parentUuid)));

            $manager->persist($subcategory);

            $this->addReference(sprintf('subcategory_%s', $uuid), $subcategory);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            LoadMainCategoriesData::class
        ];
    }
}