<?php

namespace DataFixtures;

use App\Domain\Entity\VideoPartner;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use DataFixtures\Utils\FixtureDataLoader;

/**
 * Class LoadVideoPartnersData
 */
class LoadVideoPartnersData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('video_partners.json');

        foreach ($data as $row) {
            $uuid = $row['uuid'];
            $name = $row['name'];

            $videoPartner = new VideoPartner($uuid);
            $videoPartner->setName($name);

            $this->addReference(sprintf('video_partner_%s', $uuid), $videoPartner);

            $manager->persist($videoPartner);
        }

        $manager->flush();
    }
}