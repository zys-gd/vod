<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 4/17/18
 * Time: 4:11 PM
 */

namespace DataFixtures;


use App\Domain\Entity\Affiliate;
use CommonDataBundle\DataFixtures\LoadCountriesData;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadAffiliatesData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('affiliates.json');

        foreach ($data as $row) {

            $uuid              = $row['uuid'];
            $countryId         = $row['country']['uuid'];
            $name              = $row['name'];
            $type              = $row['type'];
            $url               = $row['url'];
            $commercialContact = $row['commercialContact'];
            $technicalContact  = $row['technicalContact'];
            $skypeId           = $row['skypeId'];
            $enabled           = $row['enabled'];
            $postBackUrl       = $row['postbackUrl'];
            $subPriceName      = $row['subPriceName'];
            $isOneClickFlow    = $row['isOneClickFlow'];

            $affiliate = new Affiliate($uuid);
            $affiliate->setCountry($this->getReference(sprintf('country_%s', $countryId)));
            $affiliate->setName($name);
            $affiliate->setType($type);
            $affiliate->setUrl($url);
            $affiliate->setCommercialContact($commercialContact);
            $affiliate->setTechnicalContact($technicalContact);
            $affiliate->setSkypeId($skypeId);
            $affiliate->setEnabled($enabled);
            $affiliate->setPostbackUrl($postBackUrl);
            $affiliate->setSubPriceName($subPriceName);

            $this->addReference(sprintf('affiliate_%s', $uuid), $affiliate);

            $manager->persist($affiliate);
        }

        $manager->flush();

    }


    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [LoadCountriesData::class];
    }
}