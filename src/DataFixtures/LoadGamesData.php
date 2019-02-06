<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 4/17/18
 * Time: 3:48 PM
 */

namespace DataFixtures;


use App\Domain\Entity\Game;
use DataFixtures\Utils\FixtureDataLoader;
use DemoDataBundle\DataFixtures\ORM\LoadDevelopersData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadGamesData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('games.json');

        foreach ($data as $row) {


            $id          = $row['id'];
            $developerId = $row['developer']['uuid'];

            try{

            $tierId      = $row['tier']['uuid'];
            }catch (\Throwable $exception){
                echo $id."\n";
            }
            $name        = $row['title'];
            $description = $row['description'];
            $icon        = $row['icon'];
            $thumbnail   = $row['thumbnail'];
            $tags        = $row['tags'];
            $rating      = $row['rating'];
            $published   = $row['published'];
            $created     = $row['created'];
            $updated     = $row['updated'];
            $deleted_at  = $row['deletedAt'];
            $is_bookmark = $row['isBookmark'];
            $uuid        = $row['uuid'];

            $game = new Game($uuid);

            $developer = $this->getReference(sprintf('developer_%s', $developerId));
            $game->setDeveloper($developer);
            $tier = $this->getReference(sprintf('tier_%s', $tierId));
            $game->setTier($tier);
            $game->setTitle($name);
            $game->setDescription($description);
            $game->setIcon($icon);
            $game->setThumbnail($thumbnail);
            $game->setTags($tags);
            $game->setRating($rating);
            $game->setPublished($published);
            $game->setCreated(new \DateTimeImmutable($created));
            $game->setUpdated(new \DateTimeImmutable($updated));
            $game->setDeletedAt($deleted_at ? new \DateTimeImmutable($deleted_at) : null);
            $game->setIsBookmark($is_bookmark);


            $game->setUuid($uuid);

            $this->addReference(sprintf('game_%s', $uuid), $game);
            $this->addReference(sprintf('game_with_id_%s', $id), $game);


            foreach ($row['deactivatedCarriers'] as $deactivatedCarrier) {
                $game->addDeactivatedCarriers($this->getReference(sprintf('carrier_%s', $deactivatedCarrier['uuid'])));
            }
            foreach ($row['deactivatedCountries'] as $deactivatedCountry) {
                $game->addDeactivatedCountries($this->getReference(sprintf('country_%s', $deactivatedCountry['uuid'])));
            }

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