<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 15-03-19
 * Time: 11:53
 */

namespace SubscriptionBundle\Tests\DataFixtures;


use DataFixtures\LoadAffiliatesData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadAffiliateTestData extends AbstractFixture implements DependentFixtureInterface
{

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     * @return array
     */
    public function getDependencies()
    {
        return [
            LoadAffiliatesData::class
        ];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // $this->createUserWithoutSubscription($manager);
        //
        // $manager->flush();
    }
}