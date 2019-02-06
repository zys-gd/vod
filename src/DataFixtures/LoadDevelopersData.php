<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 4/17/18
 * Time: 4:04 PM
 */

namespace DemoDataBundle\DataFixtures\ORM;


use App\Domain\Entity\Developer;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadDevelopersData extends AbstractFixture implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('developers.json');

        foreach ($data as $row) {
            $id    = $row['id'];
            $name  = $row['name'];
            $email = $row['email'];
            $uuid  = $row['uuid'];

            $developer = new Developer();
            $developer->setEmail($email);
            $developer->setName($name);
            $developer->setUuid($uuid);

            $this->addReference(sprintf('developer_%s', $uuid), $developer);

            $manager->persist($developer);
        }

        $manager->flush();
        $manager->clear();;

    }
}