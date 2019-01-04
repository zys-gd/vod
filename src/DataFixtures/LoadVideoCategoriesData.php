<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.12.18
 * Time: 12:25
 */

namespace DataFixtures;


use App\Domain\Entity\VideoCategory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadVideoCategoriesData extends AbstractFixture
{

    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = [
            ["Sports", "sports", "44860f37-c9b7-4c41-99f1-1483af0f424e"]
        ];;

        foreach ($data as $row) {

            $title = $row[0];
            $alias = $row[1];
            $uuid  = $row[2];

            $category = new VideoCategory($uuid);

            $category->setTitle($title);
            $category->setAlias($alias);

            $manager->persist($category);
        }
        $manager->flush();
        $manager->clear();;


    }

}