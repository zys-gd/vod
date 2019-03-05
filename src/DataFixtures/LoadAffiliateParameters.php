<?php

namespace DataFixtures;

use App\Domain\Entity\AffiliateParameter;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadAffiliateParameters
 */
class LoadAffiliateParameters extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('affiliate_parameters.json');;

        foreach ($data as $row) {
            $affiliateId = $row['affiliate']['uuid'];
            $inputName   = $row['inputName'];
            $outputName  = $row['outputName'];
            $uuid        = $row['uuid'];

            $affiliateParameter = new AffiliateParameter($uuid);
            $affiliateParameter->setAffiliate($this->getReference(sprintf('affiliate_%s', $affiliateId)));
            $affiliateParameter->setInputName($inputName);
            $affiliateParameter->setOutputName($outputName);

            $this->addReference(sprintf('affiliate_parameters_%s', $uuid), $affiliateParameter);

            $manager->persist($affiliateParameter);
        }

        $manager->flush();
        $manager->clear();
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
            LoadAffiliatesData::class
        ];
    }
}