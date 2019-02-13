<?php

namespace DataFixtures;

use App\Domain\Entity\GameImage;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LoadGameImagesData
 */
class LoadGameImagesData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
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
        $data = FixtureDataLoader::loadDataFromJSONFile('game_images.json');

        foreach ($data as $row) {
            try {
                $uuid    = $row['uuid'];
                $gameId  = $row['game']['uuid'];
                $name    = $row['name'];

                $gameImage = new GameImage($uuid);
                $gameImage->setGame($this->getReference(sprintf('game_%s', $gameId)));
                $gameImage->setName($name);

                $this->addReference(sprintf('game_image_%s', $uuid), $gameImage);

                $manager->persist($gameImage);
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
        ];
    }
}