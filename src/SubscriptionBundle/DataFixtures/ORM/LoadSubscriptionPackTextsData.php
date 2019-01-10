<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.07.18
 * Time: 11:26
 */

namespace SubscriptionBundle\DataFixtures\ORM;


use App\Domain\Entity\Translation;
use DataFixtures\LoadLanguagesData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSubscriptionPackTextsData extends AbstractFixture implements DependentFixtureInterface
{

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            LoadLanguagesData::class,
            LoadSubscriptionPackData::class
        ];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = [];

        // foreach ($data as $row) {
        //     list($id, $carrier_id, $placeholder_id, $specific_value, $langCode, $subscriptionPackName) = $row;
        //
        //     $ph = new Translation();
        //
        //     $ph->setCarrierId($carrier_id);
        //     $ph->setPlaceholderId($placeholder_id);
        //     $ph->setSpecificValue($specific_value);
        //     $ph->setLanguage($this->getReference(sprintf('language_code_%s', strtolower($langCode))));
        //     $subscriptionPack = $this->getReference(sprintf('subscription_pack_with_name_%s', $subscriptionPackName));
        //     $ph->setSubscriptionPackId($subscriptionPack->getId());
        //
        //     $id != '' && $this->addReference(sprintf('subscription_pack_text_%s', $id), $ph);
        //     $manager->persist($ph);
        // }
        //
        // $manager->flush();


    }
}