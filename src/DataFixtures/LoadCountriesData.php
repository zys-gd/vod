<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 4/17/18
 * Time: 4:11 PM
 */

namespace DataFixtures;


use App\Domain\Entity\Country;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadCountriesData extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('countries.json');

        foreach ($data as $row) {
            $id = $row['id'];
            $countryCode = $row['countryCode'];
            $countryName = $row['countryName'];
            $currency = $row['currencyCode'];
            $isoNumeric = $row['isoNumeric'];
            $isoAlpha = $row['isoAlpha3'];
            $uuid = $row['uuid'];

            $country = new Country();
            $country->setCountryCode($countryCode);
            $country->setCountryName($countryName);
            $country->setCurrencyCode($currency);
            $country->setIsoNumeric($isoNumeric);
            $country->setIsoAlpha3($isoAlpha);
            $country->setUuid($uuid);
            $this->addReference(sprintf('country_%s', $uuid), $country);
            $this->addReference(sprintf('country_with_id_%s', $id), $country);

            $manager->persist($country);
        }

        $manager->flush();

    }

}