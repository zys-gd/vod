<?php

namespace DataFixtures;

use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IdentificationBundle\Entity\TestUser;

/**
 * Class LoadTestUsersData
 */
class LoadTestUsersData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('test_users.json');

        foreach ($data as $row) {
            $uuid = $row['uuid'];
            $identifier = $row['userIdentifier'];
            $carrierUuid = $row['carrier']['uuid'];

            $testUser = new TestUser($uuid);
            $testUser
                ->setUserIdentifier($identifier)
                ->setCarrier($this->getReference(sprintf('carrier_%s', $carrierUuid)));

            $manager->persist($testUser);
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
        return [LoadCarriersData::class];
    }
}