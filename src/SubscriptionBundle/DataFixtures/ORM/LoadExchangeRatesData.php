<?php

namespace SubscriptionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use ExtrasBundle\Utils\FixtureDataLoader;
use SubscriptionBundle\Entity\ExchangeRate;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class LoadExchangeRatesData
 */
class LoadExchangeRatesData extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile(__DIR__ . '/Data/', 'exchange_rates.json');

        foreach ($data as $row) {
            $uuid          = $row['uuid'];
            $currency_code = $row['currencyCode'];
            $currency_name = $row['currencyName'];
            $exchange_rate = $row['exchangeRate'];

            $rate = new ExchangeRate($uuid);

            $rate->setCurrencyCode($currency_code);
            $rate->setCurrencyName($currency_name);
            $rate->setExchangeRate($exchange_rate);

            $this->addReference(sprintf('exchange_rate_%s', $uuid), $rate);

            $manager->persist($rate);
        }

        $manager->flush();
        $manager->clear();
    }
}