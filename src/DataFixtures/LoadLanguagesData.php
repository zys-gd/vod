<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 20.04.18
 * Time: 12:29
 */

namespace DataFixtures;


use App\Domain\Entity\Language;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadLanguagesData extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('languages.json');

        foreach ($data as $row) {
            $name = $row['name'];
            $code = $row['code'];
            $uuid = $row['uuid'];

            $language = new Language($uuid);

            $language->setName($name);
            $language->setCode($code);

            $this->addReference(sprintf('language_%s', $uuid), $language);
            $this->addReference(sprintf('language_code_%s', $code), $language);
            $manager->persist($language);
        }

        $manager->flush();
    }


}