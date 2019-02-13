<?php

namespace DataFixtures;

use App\Domain\Entity\GameBuild;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LoadGameBuildsData
 */
class LoadGameBuildsData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('game_builds.json');;

        foreach ($data as $key => $row) {
            try {
                $uuid           = $row['uuid'];
                $game_id        = $row['game']['uuid'];
                $os_type        = $row['os_type'];
                $min_os_version = $row['min_os_version'];
                $game_apk       = $row['game_apk'];
                $apk_size       = $row['apk_size'];
                $version        = $row['version'];
                $apkVersion     = $row['apk_version'] . '';

                $build = new GameBuild($uuid);

                $build->setGame($this->getReference(sprintf('game_%s', $game_id)));
                $build->setOsType($os_type);
                $build->setMinOsVersion($min_os_version);
                $build->setGameApk($game_apk);
                $build->setApkSize($apk_size);
                $build->setUuid($uuid);
                $build->setApkVersion($apkVersion);
                $build->setVersion($version);

                foreach ($row['device_displays'] as $display) {
                    $build->addDeviceDisplay($this->getReference(sprintf('device_display_%s', $display['uuid'])));
                }

                $this->addReference(sprintf('game_build_%s', $uuid), $build);
                $manager->persist($build);
            } catch (\Exception $exception) {
                //do nothing
            }
        }

        $manager->flush(); $manager->clear();;
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
            LoadGamesData::class,
            LoadDeviceDisplaysData::class
        ];
    }
}