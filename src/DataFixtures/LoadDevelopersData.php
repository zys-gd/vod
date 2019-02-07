<?php

namespace DataFixtures;

use App\Domain\Entity\Developer;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LoadDevelopersData
 */
class LoadDevelopersData extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('developers.json');

        foreach ($data as $row) {
            $uuid  = $row['uuid'];
            $name  = $row['name'];
            $email = $row['email'];

            $developer = new Developer($uuid);
            $developer->setUuid($uuid);
            $developer->setEmail($email);
            $developer->setName($name);

            $this->addReference(sprintf('developer_%s', $uuid), $developer);

            $manager->persist($developer);
        }

        $manager->flush();
        $manager->clear();;
    }
}