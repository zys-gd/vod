<?php

namespace DataFixtures;

use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use PriceBundle\Entity\Tier;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LoadTiersData
 */
class LoadTiersData extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('tiers.json');

        foreach ($data as $row) {
            $uuid         = $row['uuid'];
            $name         = $row['name'];
            $bfTierId     = $row['bfTierId'];

            $tier = new Tier($uuid);
            $tier->setName($name);
            $tier->setBfTierId($bfTierId);
            $tier->setUuid($uuid);
            $this->addReference(sprintf('tier_%s', $uuid), $tier);

            $manager->persist($tier);
        }

        $manager->flush();
        $manager->clear();
    }
}