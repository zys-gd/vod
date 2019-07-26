<?php

namespace DataFixtures;

use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use CommonDataBundle\DataFixtures\LoadCountriesData;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadCampaignsData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{

    use ContainerAwareTrait;


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {

        $data = FixtureDataLoader::loadDataFromJSONFile('campaigns.json');

        foreach ($data as $row) {


            $affiliate_id                  = $row['affiliate']['uuid'];
            $image                         = $row['imageName'];
            $bg_color                      = $row['bgColor'];
            $text_color                    = $row['textColor'];
            $carriers                      = $row['carriers'];
            $campaign_token                = $row['campaignToken'];
            $is_pause                      = $row['isPause'];
            $ppd                           = $row['ppd'] ?? null;
            $sub                           = $row['sub'] ?? null;
            $click                         = $row['click'] ?? null;
            $test_url                      = $row['testUrl'];
            $uuid                          = $row['uuid'];

            $campaign = new Campaign($uuid);

            $affiliate = $this->getReference(sprintf('affiliate_%s', $affiliate_id));
            $campaign->setAffiliate($affiliate);


            $campaign->setImageName($image);
            $campaign->setBgColor($bg_color);
            $campaign->setTextColor($text_color);

            foreach ($carriers as $carrierData) {
                /** @var Carrier $carrier */

                try {

                    $carrier = $this->getReference(sprintf('carrier_%s', $carrierData['uuid']));
                } catch (\OutOfBoundsException $exception) {
                    echo "Missing carrier with uuid `${carrierData['uuid']}` for campaign `$uuid``\n\r";
                    continue;
                }
                $carrier->addCampaign($campaign);
                $campaign->addCarrier($carrier);
            }
            $campaign->setCampaignToken($campaign_token);

            $campaign->setIsPause($is_pause);
            $campaign->setTestUrl($test_url);
            $campaign->setUuid($uuid);

            $campaign->setPpd($ppd);
            $campaign->setClick($click);
            $campaign->setSub($sub);


            $this->addReference(sprintf('campaign_%s', $uuid), $campaign);

            $manager->persist($campaign);
        }

        $manager->flush();
        $manager->clear();;

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
            LoadCarriersData::class,
            LoadGamesData::class,
            LoadCountriesData::class,
            LoadAffiliatesData::class
        ];
    }
}