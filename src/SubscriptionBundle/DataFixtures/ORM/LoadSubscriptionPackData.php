<?php

namespace SubscriptionBundle\DataFixtures\ORM;

use CommonDataBundle\DataFixtures\LoadCountriesData;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ExtrasBundle\Utils\FixtureDataLoader;
use SubscriptionBundle\Entity\SubscriptionPack;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadSubscriptionPackData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @var DependentFixtureInterface
     */
    private $carrierFixture;

    /**
     * LoadSubscriptionPackData constructor.
     */
    public function __construct(DependentFixtureInterface $carrierFixture)
    {
        $this->carrierFixture = $carrierFixture;
    }


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile(__DIR__ . '/Data/', 'subscription_packs.json');

        foreach ($data as $row) {
            $uuid                                = $row['uuid'];
            $country_uuid                        = $row['country']['uuid'];
            $status                              = $row['status'];
            $name                                = $row['name'];
            $description                         = $row['description'];
            $carrier_id                          = $row['carrier']['uuid'];
            $periodicity                         = $row['periodicity'];
            $custom_renew_period                 = $row['customRenewPeriod'];
            $grace_period                        = $row['gracePeriod'];
            $tier_id                             = $row['tierId'];
            $tier_price                          = $row['tierPrice'] ?? '';
            $tier_currency                       = $row['tierCurrency'] ?? '';
            $credits                             = $row['credits'];
            $unlimited_grace_period              = $row['unlimitedGracePeriod'];
            $preferred_renewal_start             = $row['preferredRenewalStart'];
            $preferred_renewal_end               = $row['preferredRenewalEnd'];
            $welcome_sms_text                    = $row['welcomeSMSText'];
            $renewal_sms_text                    = $row['renewalSMSText'];
            $unsubscribe_sms_text                = $row['unsubscribeSMSText'];
            $buy_strategy_id                     = $row['buyStrategyId'];
            $renew_strategy_id                   = $row['renewStrategyId'];
            $unlimited                           = $row['unlimited'];
            $is_first_subscription_free          = $row['firstSubscriptionPeriodIsFree'];
            $is_first_subscription_free_multiple = $row['firstSubscriptionPeriodIsFreeMultiple'];
            $allow_bonus_credit                  = $row['allowBonusCredit'];
            $allow_bonus_credit_multiple         = $row['allowBonusCreditMultiple'];
            $bonus_credit                        = $row['bonusCredit'];
            $provider_managed_subscriptions      = $row['providerManagedSubscriptions'];
            $created                             = $row['created'];
            $updated                             = $row['updated'];
            $is_resub_allowed                    = $row['isResubAllowed'];
            $displayCurrency                     = $row['displayCurrency'] ?? '';
            $zeroCreditSubAvailable              = $row['zeroCreditSubAvailable'] ?? 0;


            $pack = new SubscriptionPack($uuid);


            $pack->setCountry($this->getReference(sprintf('country_%s', $country_uuid)));
            $pack->setStatus($status);
            $pack->setName($name);
            $pack->setDescription($description);


            try {

                /** @var CarrierInterface $carrier */
                $carrier = $this->getReference(sprintf('carrier_%s', $carrier_id));
            } catch (\OutOfBoundsException $exception) {
                echo "Missing carrier with internal ID `$carrier_id` for subscription pack `$uuid`. Skipping.\n\r ";
                continue;
            }

            $this->addReference(sprintf('subscription_pack_%s', $uuid), $pack);
            $this->addReference(sprintf('subscription_pack_with_name_%s', $name), $pack);

            if ($status == SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK) {
                $this->addReference(sprintf('subscription_pack_for_carrier_%s', $carrier_id), $pack);
                $this->addReference(sprintf('subscription_pack_for_carrier_with_internal_id_%s', $carrier->getBillingCarrierId()), $pack);
            }

            $pack->setCarrier($carrier);

            $pack->setTierPrice($tier_price);
            $pack->setTierCurrency($tier_currency);
            $pack->setTierId($tier_id);

            $pack->setCredits($credits);
            $pack->setPeriodicity($periodicity);
            $pack->setCustomRenewPeriod($custom_renew_period);
            $pack->setGracePeriod($grace_period);
            $pack->setUnlimitedGracePeriod($unlimited_grace_period);
            $pack->setPreferredRenewalStart(new \DateTime($preferred_renewal_start));
            $pack->setPreferredRenewalEnd(new \DateTime($preferred_renewal_end));
            $pack->setWelcomeSMSText($welcome_sms_text);
            $pack->setRenewalSMSText($renewal_sms_text);
            $pack->setUnsubscribeSMSText($unsubscribe_sms_text);
            $pack->setBuyStrategyId($buy_strategy_id);
            $pack->setRenewStrategyId($renew_strategy_id);
            $pack->setUnlimited($unlimited);
            $pack->setFirstSubscriptionPeriodIsFree($is_first_subscription_free);
            $pack->setFirstSubscriptionPeriodIsFreeMultiple($is_first_subscription_free_multiple);
            $pack->setAllowBonusCredit($allow_bonus_credit);
            $pack->setAllowBonusCreditMultiple($allow_bonus_credit_multiple);
            $pack->setBonusCredit($bonus_credit);
            $pack->setProviderManagedSubscriptions($provider_managed_subscriptions);
            $pack->setCreated(new \DateTime($created));
            $pack->setUpdated(new \DateTime($updated));
            $pack->setIsResubAllowed($is_resub_allowed);
            $pack->setDisplayCurrency($displayCurrency);
            $pack->setZeroCreditSubAvailable($zeroCreditSubAvailable);

            $manager->persist($pack);
        }

        $manager->flush();
    }


    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     * @return array
     */
    function getDependencies()
    {
        return [
            LoadCountriesData::class,
            get_class($this->carrierFixture)
        ];
    }
}