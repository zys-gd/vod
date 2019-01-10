<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 4/17/18
 * Time: 5:27 PM
 */

namespace DataFixtures;


use App\Domain\Entity\Carrier;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JsonMapper;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadCarriersData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{

    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $data = FixtureDataLoader::loadDataFromJSONFile('carriers.json');

        foreach ($data as $row) {

            $uuid                                    = $row['uuid'];
            $billingCarrierId                               = $row['id_carrier'];
            $operatorId                              = $row['operator_id'];
            $name                                    = $row['name'];
            $countryCode                             = $row['countryCode'];
            $isp                                     = $row['isp'];
            $published                               = $row['published'];
            $lpOtp                                   = $row['lpOtp'];
            $pinIdentSupport                         = $row['pinIdentSupport'];
            $trialInitializer                        = $row['trial_initializer'];
            $trialPeriod                             = $row['trial_period'];
            $subscriptionPeriod                      = $row['subscription_period'];
            $resubAllowed                            = $row['resubAllowed'];
            $isCampaignsOnPause                      = $row['isCampaignsOnPause'];
            $numberOfAllowedSubscription             = $row['numberOfAllowedSubscription'];
            $isUnlimitedSubscriptionAttemptsAllowed  = $row['isUnlimitedSubscriptionAttemptsAllowed'];
            $isCaptcha                               = $row['isCaptcha'];
            $numberOfAllowedSubscriptionByConstraint = $row['numberOfAllowedSubscriptionsByConstraint'];
            $redirectUrl                             = $row['redirectUrl'];
            $counter                                 = $row['counter'];
            $flushdate                               = $row['flushDate'];
            $isCapAlertDispatch                      = $row['isCapAlertDispatch'];
            $flushdate = $row['flushDate'];


            $defaultLanguageId = $row['default_language']['uuid'];

            $carrier = new Carrier($uuid);

            $carrier->setBillingCarrierId($billingCarrierId);
            $carrier->setName($name);
            $carrier->setCountryCode($countryCode);
            $carrier->setIsp((string)$isp);
            $carrier->setPublished($published);
            $carrier->setTrialInitializer($trialInitializer);
            $carrier->setTrialPeriod($trialPeriod);
            $carrier->setSubscriptionPeriod($subscriptionPeriod);
            $carrier->setOperatorId($operatorId);
            $carrier->setLpOtp($lpOtp);
            $carrier->setPinIdentSupport($pinIdentSupport);
            $carrier->setResubAllowed($resubAllowed);
            $carrier->setIsCampaignsOnPause($isCampaignsOnPause);
            $carrier->setNumberOfAllowedSubscription((int)$numberOfAllowedSubscription);
            $carrier->setnumberOfAllowedSubscriptionsByConstraint($numberOfAllowedSubscriptionByConstraint);
            $carrier->setIsCaptcha($isCaptcha);
            $carrier->setRedirectUrl($redirectUrl);
            $carrier->setCounter($counter);
            $carrier->setFlushDate($flushdate ? \DateTime::createFromFormat('Y-m-d', $flushdate) : null);
            $carrier->setIsUnlimitedSubscriptionAttemptsAllowed($isUnlimitedSubscriptionAttemptsAllowed);
            $carrier->setUuid($uuid);
            $carrier->setIsCapAlertDispatch((bool)$isCapAlertDispatch);

            if ($defaultLanguageId) {
                $carrier->setDefaultLanguage($this->getReference(sprintf('language_%s', $defaultLanguageId)));
            }

            $manager->persist($carrier);

            $this->addReference(sprintf('carrier_%s', $uuid), $carrier);
            $this->addReference(sprintf('carrier_with_internal_id_%s', $billingCarrierId), $carrier);

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
        return [LoadLanguagesData::class];
    }
}