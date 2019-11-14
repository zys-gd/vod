<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.05.18
 * Time: 12:29
 */


namespace SubscriptionBundle\Tests\DataFixtures;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use ExtrasBundle\Utils\UuidGenerator;
use DataFixtures\LoadCarriersData;
use DataFixtures\LoadGamesData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SubscriptionBundle\DataFixtures\ORM\LoadSubscriptionPackData;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use Tests\Core\TestEntityProvider;

class LoadSubscriptionTestData extends AbstractFixture implements DependentFixtureInterface
{

    const GENERIC_CARRIER = 10241024;
    const ALLOWED_RESUB_CARRIER = 10241027;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {

        $subscription = $this->createUserWithSubscription($manager);


        $this->createUserWithoutSubscription($manager);
        $this->createUserWithInactiveSubscription($manager);
        $this->createUserWithActiveSubscription($manager);
        $this->createUserWithHoldSubscription($manager);

        $this->createCampaignWithAffiliateAndPricing($manager);

        $this->createUserForJazzPKSubscribe($manager);

        $this->createGenericCarrier($manager);
        $this->createGenericSubscriptionPack($manager);

        $this->createCarrierWithAllowedResub($manager);
        $this->createSubscriptionPackForCarrierWithAllowedResub($manager);

        $this->createUserWithInactiveSubscriptionForCarrierWithAllowedResub($manager);

        $manager->flush();
    }


    /**
     * @param ObjectManager $manager
     *
     * @return Subscription
     * @throws \Exception
     */
    protected function createUserWithSubscription(ObjectManager $manager): Subscription
    {
        /** @var SubscriptionPack $randomSubscriptionPack */
        $randomSubscriptionPack = $this->getReference('subscription_pack_with_name_Jazz PK');
        $carrier                = $this->getReference(sprintf('carrier_with_internal_id_%s', $randomSubscriptionPack->getCarrier()->getBillingCarrierId()));

        $user = TestEntityProvider::createUserWithIdentificationRequest($carrier, 'identtoken');

        $manager->persist($user);
        $this->addReference('default_billable_user', $user);

        $subscription = TestEntityProvider::createSubscription($user, $randomSubscriptionPack);
        $manager->persist($subscription);

        $this->addReference('subscription_for_default_billable_user', $subscription);
        return $subscription;
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    protected function createUserForJazzPKSubscribe(ObjectManager $manager)
    {
        /** @var SubscriptionPack $jazzPk */
        $jazzPk  = $this->getReference('subscription_pack_with_name_Jazz PK');
        $carrier = $this->getReference(sprintf('carrier_with_internal_id_%s', $jazzPk->getCarrier()->getBillingCarrierId()));

        $user = TestEntityProvider::createUserWithIdentificationRequest($carrier, 'token_for_dialog_user');

        $manager->persist($user);

        $this->addReference('jazz_pk_user', $user);

    }

    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    protected function createUserWithoutSubscription(ObjectManager $manager)
    {

        /** @var SubscriptionPack $randomSubscriptionPack */
        $randomSubscriptionPack = $this->getReference('subscription_pack_with_name_Jazz PK'); // Orange Tunis.
        $carrier                = $this->getReference(sprintf('carrier_with_internal_id_%s', $randomSubscriptionPack->getCarrier()->getBillingCarrierId()));

        $user = TestEntityProvider::createUserWithIdentificationRequest($carrier, 'token_for_user_without_subscription');

        $manager->persist($user);
        $this->addReference('user_without_subscription', $user);
    }


    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    protected function createCampaignWithAffiliateAndPricing(ObjectManager $manager)
    {

        $randomSubscriptionPack = $this->getReference('subscription_pack_with_name_Jazz PK');
        $carrier                = $this->getReference(sprintf('carrier_with_internal_id_%s', $randomSubscriptionPack->getCarrier()->getBillingCarrierId()));

        $affiliate = new Affiliate(UuidGenerator::generate());
        $affiliate->setName('');
        $affiliate->setType(1);
        $affiliate->setPostbackUrl('');
        $affiliate->setEnabled(1);
        $manager->persist($affiliate);

        $campaign = new Campaign(UuidGenerator::generate());
        $campaign->setCampaignToken('');
        $campaign->setTestUrl('');
        $campaign->setAffiliate($affiliate);
        $campaign->addCarrier($carrier);
        $campaign->setImageName('');

        $this->addReference('campaign', $campaign);

        $manager->persist($campaign);
    }

    public function getDependencies(): array
    {
        return [

            LoadCarriersData::class,
            LoadSubscriptionPackData::class,
            LoadGamesData::class,
        ];
    }

    /**
     * @param ObjectManager $manager
     *
     * @return Subscription
     * @throws \Exception
     */
    private function createUserWithInactiveSubscription(ObjectManager $manager)
    {
        /** @var SubscriptionPack $randomSubscriptionPack */
        $randomSubscriptionPack = $this->getReference('subscription_pack_with_name_Jazz PK');
        $carrier                = $this->getReference(sprintf('carrier_with_internal_id_%s', $randomSubscriptionPack->getCarrier()->getBillingCarrierId()));

        $user = TestEntityProvider::createUserWithIdentificationRequest($carrier, 'inactive_subscription_ident_request');

        $manager->persist($user);

        $subscription = TestEntityProvider::createSubscription($user, $randomSubscriptionPack, Subscription::IS_INACTIVE, Subscription::ACTION_SUBSCRIBE);
        $subscription->setCredits(0);
        $manager->persist($subscription);

        $this->addReference('inactive_subscription', $subscription);
        return $subscription;
    }

    /**
     * @TODO: It will be better to came up with more pretty name of function)))
     * @param ObjectManager $manager
     * @return Subscription
     * @throws \Exception
     */
    private function createUserWithInactiveSubscriptionForCarrierWithAllowedResub(ObjectManager $manager)
    {
        /** @var SubscriptionPack $subscriptionPackForCarrierWithAllowedResub */
        /** @var Carrier $carrier */
        $subscriptionPackForCarrierWithAllowedResub = $this->getReference('subscription_pack_for_carrier_with_allowed_resub');
        $carrier                                    = $this->getReference(sprintf('carrier_with_internal_id_%s', $subscriptionPackForCarrierWithAllowedResub
            ->getCarrier()->getBillingCarrierId()));

        $user = TestEntityProvider::createUserWithIdentificationRequest($carrier, 'inactive_subscription_ident_for_carrier_with_allowed_resub_request');

        $manager->persist($user);

        $subscription = TestEntityProvider::createSubscription(
            $user,
            $subscriptionPackForCarrierWithAllowedResub,
            Subscription::IS_INACTIVE,
            Subscription::ACTION_SUBSCRIBE
        );
        $subscription->setCredits(0);

        $manager->persist($subscription);

        $this->addReference('inactive_subscription_for_carrier_with_allowed_resub', $subscription);
        return $subscription;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return Subscription
     * @throws \Exception
     */
    private function createUserWithActiveSubscription(ObjectManager $manager)
    {
        /** @var SubscriptionPack $randomSubscriptionPack */
        $randomSubscriptionPack = $this->getReference('subscription_pack_with_name_Jazz PK');
        $carrier                = $this->getReference(sprintf('carrier_with_internal_id_%s', $randomSubscriptionPack->getCarrier()->getBillingCarrierId()));

        $user = TestEntityProvider::createUserWithIdentificationRequest($carrier, 'active_subscription_ident_request');

        $manager->persist($user);

        $subscription = TestEntityProvider::createSubscription($user, $randomSubscriptionPack, Subscription::IS_ACTIVE, Subscription::ACTION_SUBSCRIBE);
        $subscription->setCredits(2);
        $manager->persist($subscription);

        $this->addReference('active_subscription', $subscription);
        return $subscription;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return Subscription
     * @throws \Exception
     */
    private function createUserWithHoldSubscription(ObjectManager $manager)
    {
        /** @var SubscriptionPack $randomSubscriptionPack */
        $randomSubscriptionPack = $this->getReference('subscription_pack_with_name_Jazz PK');
        $carrier                = $this->getReference(sprintf('carrier_with_internal_id_%s', $randomSubscriptionPack->getCarrier()->getBillingCarrierId()));

        $user = TestEntityProvider::createUserWithIdentificationRequest($carrier, 'onhold_subscription_ident_request');

        $manager->persist($user);

        $subscription = TestEntityProvider::createSubscription($user, $randomSubscriptionPack, Subscription::IS_ON_HOLD, Subscription::ACTION_SUBSCRIBE, 'not_enough_credit');
        $manager->persist($subscription);

        $this->addReference('onhold_subscription', $subscription);
        return $subscription;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return Carrier
     * @throws \Exception
     */
    private function createGenericCarrier(ObjectManager $manager): Carrier
    {
        $carrier = new Carrier(UuidGenerator::generate());

        $carrier->setBillingCarrierId(self::GENERIC_CARRIER);
        $carrier->setName('Generic Carrier');
        $carrier->setCountryCode('AB');
        $carrier->setIsp('Generic');
        $carrier->setPublished(true);
        $carrier->setTrialInitializer('store');
        $carrier->setTrialPeriod(0);
        $carrier->setSubscriptionPeriod(7);
        $carrier->setOperatorId('');
        $carrier->setIsOneClickFlow(false);
        $carrier->setIsCampaignsOnPause(false);
        $carrier->setSubscribeAttempts(0);
        $carrier->setNumberOfAllowedSubscriptionsByConstraint(0);
        $carrier->setRedirectUrl(false);
        $carrier->setFlushDate(null);

        $this->addReference('generic_carrier', $carrier);
        $this->addReference(sprintf('carrier_with_internal_id_%s', self::GENERIC_CARRIER), $carrier);
        $manager->persist($carrier);

        return $carrier;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return SubscriptionPack
     * @throws \Exception
     */
    private function createGenericSubscriptionPack(ObjectManager $manager): SubscriptionPack
    {
        $pack = new SubscriptionPack(UuidGenerator::generate());
        $pack->setCredits(2);
        $pack->setCarrier($this->getReference('generic_carrier'));
        $pack->setName('Generic');
        $pack->setIsResubAllowed(false);
        $pack->setStatus(SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK);
        $pack->setTier('Generic Carrier Tier');
        $pack->setTierId(10241024);
        $pack->setTierCurrency('PKR');
        $pack->setBuyStrategyId(10241024 + 1);
        $pack->setRenewStrategyId(10241024 + 2);
        $pack->setCreated(new \DateTime());
        $pack->setUpdated(new \DateTime());
        $this->addReference('generic_subscription_pack', $pack);
        $this->addReference(sprintf('subscription_pack_for_carrier_%s', self::GENERIC_CARRIER), $pack);

        $manager->persist($pack);

        return $pack;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return Carrier
     * @throws \Exception
     */
    private function createCarrierWithAllowedResub(ObjectManager $manager): Carrier
    {
        $carrier = new Carrier(UuidGenerator::generate());

        $carrier->setBillingCarrierId(self::ALLOWED_RESUB_CARRIER);
        $carrier->setName('Allowed Resub Carrier');
        $carrier->setCountryCode('AB');
        $carrier->setIsp('Generic');
        $carrier->setPublished(true);
        $carrier->setTrialInitializer('store');
        $carrier->setTrialPeriod(0);
        $carrier->setSubscriptionPeriod(7);
        $carrier->setOperatorId('');
        $carrier->setIsCampaignsOnPause(false);
        $carrier->setSubscribeAttempts(0);
        $carrier->setNumberOfAllowedSubscriptionsByConstraint(0);
        $carrier->setRedirectUrl(false);
        $carrier->setFlushDate(null);

        $this->addReference('allowed_resub_carrier', $carrier);
        $this->addReference(sprintf('carrier_with_internal_id_%s', self::ALLOWED_RESUB_CARRIER), $carrier);
        $manager->persist($carrier);

        return $carrier;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return SubscriptionPack
     * @throws \Exception
     */
    private function createSubscriptionPackForCarrierWithAllowedResub(ObjectManager $manager): SubscriptionPack
    {
        $pack = new SubscriptionPack(UuidGenerator::generate());
        $pack->setCredits(2);
        $pack->setName('Allowed Resub Carrier');
        $pack->setIsResubAllowed(true);
        $pack->setStatus(SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK);
        $pack->setTier('Allowed Resub Carrier Tier');
        $pack->setTierId(10241027);
        $pack->setCarrier($this->getReference('allowed_resub_carrier'));
        $pack->setTierCurrency('PKR');
        $pack->setBuyStrategyId(10241027 + 1);
        $pack->setRenewStrategyId(10241027 + 2);
        $pack->setCreated(new \DateTime());
        $pack->setUpdated(new \DateTime());
        $this->addReference('subscription_pack_for_carrier_with_allowed_resub', $pack);

        $manager->persist($pack);

        return $pack;
    }

}