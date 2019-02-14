<?php

namespace DataFixtures;

use App\Domain\Entity\Game;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LoadGamesData
 */
class LoadGamesData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
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
        $data = FixtureDataLoader::loadDataFromJSONFile('games.json');

        foreach ($data as $row) {
            $uuid        = $row['uuid'];
            $developerId = $row['developer']['uuid'];
            $tierId      = $row['tier']['uuid'];
            $name        = $row['title'];
            $description = $row['description'];
            $icon        = $row['icon'];
            $thumbnail   = $row['thumbnail'];
            $rating      = $row['rating'];
            $published   = $row['published'];
            $created     = $row['created'];
            $updated     = $row['updated'];
            $deleted_at  = $row['deletedAt'];
            $is_bookmark = $row['isBookmark'];

            $game = new Game($uuid);

            $game->setDeveloper($this->getReference(sprintf('developer_%s', $developerId)));
            $game->setTier($this->getReference(sprintf('tier_%s', $tierId)));
            $game->setTitle($name);
            $game->setDescription($description);
            $game->setIcon($icon);
            $game->setThumbnail($thumbnail);
            $game->setRating($rating);
            $game->setPublished($published);
            $game->setCreated(new \DateTimeImmutable($created));
            $game->setUpdated(new \DateTimeImmutable($updated));
            $game->setDeletedAt($deleted_at ? new \DateTimeImmutable($deleted_at) : null);
            $game->setIsBookmark($is_bookmark);

            $this->addReference(sprintf('game_%s', $uuid), $game);

            $manager->persist($game);
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
            LoadDevelopersData::class,
            LoadTiersData::class,
            LoadCarriersData::class,
            LoadCountriesData::class,
        ];
    }
}