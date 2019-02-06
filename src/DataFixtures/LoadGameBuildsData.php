<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 4/18/18
 * Time: 9:59 AM
 */

namespace DataFixtures;


use App\Domain\Entity\GameBuild;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadGameBuildsData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('game_builds.json');;

        foreach ($data as $row) {

            $id             = $row['id'];
            $game_id        = $row['game']['uuid'];
            $os_type        = $row['os_type'];
            $min_os_version = $row['min_os_version'];
            $game_apk       = $row['game_apk'];
            $apk_size       = $row['apk_size'];
            $version        = $row['version'];
            $uuid           = $row['uuid'];

            $build = new GameBuild($uuid);
            $game  = $this->getReference(sprintf('game_%s', $game_id));
            $build->setGame($game);

            $build->setOsType($os_type);
            $build->setMinOsVersion($min_os_version);
            $build->setGameApk($game_apk);
            $build->setApkSize($apk_size);
            $build->setUuid($uuid);
            $build->setApkVersion($id);

            if ($version)
                $build->setVersion($version);

            foreach ($row['device_displays'] as $display) {
                $build->addDeviceDisplay($this->getReference(sprintf('device_display_%s', $display['uuid'])));
            }

            $this->addReference(sprintf('game_build_%s', $id), $build);
            $manager->persist($build);
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