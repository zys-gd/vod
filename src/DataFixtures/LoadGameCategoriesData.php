<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 20.04.18
 * Time: 11:48
 */

namespace DataFixtures;


use App\Domain\Entity\CategoryGameAssociation;
use App\Domain\Entity\Game;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadGameCategoriesData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $data = FixtureDataLoader::loadDataFromJSONFile('category_games.json');;;
        foreach ($data as $row) {
            $id         = $row['id'];
            $gameId     = $row['game']['uuid'];
            $categoryId = $row['category']['uuid'];
            $position   = $row['position'];
            $uuid   = $row['uuid'];

            /** @var Game $game */
            $game = $this->getReference(sprintf('game_%s', $gameId));

            /** @var Category $category */
            $category = $this->getReference(sprintf('category_%s', $categoryId));

            $association = new CategoryGameAssociation($uuid);
            $association->setGame($game);
            $association->setCategory($category);
            $association->setPosition($position);
            $association->setUuid($uuid);

            $this->addReference(sprintf('category_game_association_%s', $uuid), $association);

            $manager->persist($association);
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
            LoadCategoriesData::class,
            LoadGamesData::class
        ];
    }
}