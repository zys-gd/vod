<?php

namespace DataFixtures;

use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use PriceBundle\Entity\Strategy;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LoadStrategiesData
 */
class LoadStrategiesData extends AbstractFixture
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('strategies.json');

        foreach ($data as $row) {
            $name         = $row['name'];
            $bfStrategyId = $row['bfStrategyId'];
            $uuid         = $row['uuid'];

            $strategy = new Strategy($uuid);

            $strategy->setName($name);
            $strategy->setBfStrategyId($bfStrategyId);
            $strategy->setUuid($uuid);

            $this->addReference(sprintf('strategy_%s', $uuid), $strategy);

            $manager->persist($strategy);
        }

        $manager->flush();
        $manager->clear();
    }
}