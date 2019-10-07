<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.10.19
 * Time: 17:55
 */

namespace Tests\DataFixtures;


use DataFixtures\LoadCarriersData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IdentificationBundle\BillingFramework\ID;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

class LoadAffiliateConstraintTestData extends AbstractFixture implements DependentFixtureInterface
{

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            LoadCampaignTestData::class,
            LoadCarriersData::class
        ];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $constraint = new ConstraintByAffiliate('random_uuid');

        $campaign = $this->getReference('test_campaign');

        $constraint->setCarrier($this->getReference(sprintf('carrier_with_internal_id_%s', ID::MOBILINK_PAKISTAN)));
        $constraint->setAffiliate($campaign->getAffiliate());
        $constraint->setCapType(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE);
        $constraint->setRedirectUrl('test_cap_redirect_url');

        $this->addReference('test_subscription_cap_constraint', $constraint);

        $manager->persist($constraint);

        $manager->flush();
    }
}