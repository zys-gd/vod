<?php

namespace DataFixtures;

use App\Domain\Entity\AffiliateConstant;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadAffiliateConstants
 */
class LoadAffiliateConstants extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('affiliate_constants.json');

        foreach ($data as $row) {
            $affiliateId  = $row['affiliate']['uuid'];
            $name         = $row['name'];
            $value        = $row['value'];
            $uuid         = $row['uuid'];

            $affiliateConstant = new AffiliateConstant($uuid);
            $affiliateConstant->setAffiliate($this->getReference(sprintf('affiliate_%s', $affiliateId)));
            $affiliateConstant->setName($name);
            $affiliateConstant->setValue($value);

            $this->addReference(sprintf('affiliate_constant_%s', $uuid), $affiliateConstant);

            $manager->persist($affiliateConstant);
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