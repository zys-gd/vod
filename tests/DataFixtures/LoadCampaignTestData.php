<?php


namespace Tests\DataFixtures;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Utils\UuidGenerator;
use DataFixtures\LoadAffiliatesData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCampaignTestData extends AbstractFixture implements DependentFixtureInterface
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
        $this->createGoogleCampaignWithCustomPage($manager);

        $manager->flush();
    }

    private function createGoogleCampaignWithCustomPage(ObjectManager $manager)
    {
        /** @var Affiliate $affiliate */
        $affiliate = $this->getReference('affiliate_514fe478-ebd4-11e8-95c4-02bb250f0f22');

        $campaign = new Campaign(UuidGenerator::generate());
        $campaign->setCampaignToken('google_campaign_token');
        $campaign->setImageName('google_campaign_');
        $campaign->setAffiliate($affiliate);
        $manager->persist($campaign);
        $this->addReference('google_campaign', $campaign);

        return $campaign;
    }
}