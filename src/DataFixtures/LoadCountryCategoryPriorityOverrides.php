<?php

namespace DataFixtures;

use App\Domain\Entity\CountryCategoryPriorityOverride;
use CommonDataBundle\DataFixtures\LoadCountriesData;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadCountryCategoryPriorityOverrides
 */
class LoadCountryCategoryPriorityOverrides extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('country_category_priority_overrides.json');

        foreach ($data as $row) {
            $uuid = $row['uuid'];
            $menuPriority = $row['menuPriority'];

            $countryOverride = new CountryCategoryPriorityOverride($uuid);
            $countryOverride
                ->setCountry($this->getReference(sprintf('country_%s', $row['country']['uuid'])))
                ->setMainCategory($this->getReference(sprintf('main_category_%s', $row['mainCategory']['uuid'])))
                ->setMenuPriority($menuPriority);

            $manager->persist($countryOverride);
        }

        $manager->flush();
        $manager->clear();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            LoadCountriesData::class,
            LoadMainCategoriesData::class
        ];
    }
}