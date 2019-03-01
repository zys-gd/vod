<?php

namespace DataFixtures;

use App\Domain\Entity\Admin;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * Class LoadAdminData
 */
class LoadAdminData extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('admins.json');

        $encoderFactory = new EncoderFactory([
            Admin::class => new BCryptPasswordEncoder(rand(4, 31))
        ]);

        foreach ($data as $row) {
            $username = $row['username'];
            $password = $row['password'];
            $email    = $row['email'];
            $roles    = $row['roles'];

            $admin = new Admin();
            $encoder = $encoderFactory->getEncoder($admin);

            $admin
                ->setUsername($username)
                ->setEmail($email)
                ->setPassword($encoder->encodePassword($password, $admin->getSalt()))
                ->setEnabled(true)
                ->setRoles($roles);

            $manager->persist($admin);
        }

        $manager->flush();
    }
}