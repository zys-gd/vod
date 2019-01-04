<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 4/18/18
 * Time: 12:25 PM
 */

namespace DataFixtures;


use App\Domain\Entity\Translation;
use App\Utils\UuidGenerator;
use DataFixtures\LoadLanguagesData;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Playwing\DiffToolBundle\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadTranslationsData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{

    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $data = \DataFixtures\Utils\FixtureDataLoader::loadDataFromJSONFile('tranlations.json');;


        foreach ($data as $row) {
            $key         = $row['key'];
            $translation = $row['translation'];
            $uuid        = $row['uuid'];
            $language    = $row['language']['uuid'];

            FixtureDataLoader::insertRow([
                '`key`'       => $key,
                'translation' => $translation,
                'uuid'        => $uuid,
                'language_id' => $language
            ], Translation::class, $manager);
        }
    }


    function getDependencies()
    {
        return [LoadLanguagesData::class];
    }

}