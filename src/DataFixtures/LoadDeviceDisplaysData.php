<?php

namespace DataFixtures;

use App\Domain\Entity\DeviceDisplay;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LoadDeviceDisplaysData
 */
class LoadDeviceDisplaysData extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('device_displays.json');

        foreach ($data as $row) {
            $uuid   = $row['uuid'];
            $name   = $row['name'];
            $width  = $row['width'];
            $height = $row['height'];

            $deviceDisplay = new DeviceDisplay($uuid);
            $deviceDisplay->setName($name);
            $deviceDisplay->setWidth($width);
            $deviceDisplay->setHeight($height);

            $this->addReference(sprintf('device_display_%s', $uuid), $deviceDisplay);

            $manager->persist($deviceDisplay);
        }

        $manager->flush(); $manager->clear();;
    }
}