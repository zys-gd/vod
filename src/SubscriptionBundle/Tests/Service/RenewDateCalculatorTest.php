<?php


namespace App\SubscriptionBundle\Tests\Service;

use ExtrasBundle\Utils\UuidGenerator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator;


class RenewDateCalculatorTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var RenewDateCalculator
     */
    private $renewDateCalculator;

    /**
     * @throws \Exception
     */
    public function testCalculateRenewDate()
    {
        $knownDate = Carbon::create(date('Y'), date('m'), date('d'), 01);
        Carbon::setTestNow($knownDate);

        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setPreferredRenewalStart(new \DateTime('02:00:00'));
        $subscriptionPack->setPreferredRenewalEnd(new \DateTime('21:00:00'));
        $subscriptionPack->setPeriodicity(1);

        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);

        $renewDate = $this->renewDateCalculator->calculateRenewDate($subscription);

        $this->assertTrue(
            $renewDate->between(
                Carbon::instance($subscriptionPack->getPreferredRenewalStart())->addDay(),
                Carbon::instance($subscriptionPack->getPreferredRenewalEnd())->addDay()
            )
        );
    }

    public function testCalculateRenewDateWithDifferentTrialPeriod()
    {
        $knownDate = Carbon::create(date('Y'), date('m'), date('d'), 01);
        Carbon::setTestNow($knownDate);

        $trialPeriod = 1;

        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setPreferredRenewalStart(new \DateTime('02:00:00'));
        $subscriptionPack->setPreferredRenewalEnd(new \DateTime('21:00:00'));
        $subscriptionPack->setPeriodicity(7);
        $subscriptionPack->setFirstSubscriptionPeriodIsFree(0);
        $subscriptionPack->setTrialPeriod($trialPeriod);

        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);

        $renewDate = $this->renewDateCalculator->calculateRenewDate($subscription);

        $periodicity = $subscriptionPack->isFirstSubscriptionPeriodIsFree()
            ? $trialPeriod
            : $subscriptionPack->getPeriodicity();

        // without trial
        $this->assertTrue(
            $renewDate->between(
                Carbon::instance($subscriptionPack->getPreferredRenewalStart())->addDays($periodicity),
                Carbon::instance($subscriptionPack->getPreferredRenewalEnd())->addDays($periodicity)
            )
        );

        // with trial
        $subscriptionPack->setFirstSubscriptionPeriodIsFree(1);
        $renewDate   = $this->renewDateCalculator->calculateRenewDate($subscription);
        $periodicity = $subscriptionPack->isFirstSubscriptionPeriodIsFree()
            ? $trialPeriod
            : $subscriptionPack->getPeriodicity();

        $this->assertTrue(
            $renewDate->between(
                Carbon::instance($subscriptionPack->getPreferredRenewalStart())->addDays($periodicity),
                Carbon::instance($subscriptionPack->getPreferredRenewalEnd())->addDays($periodicity)
            )
        );

    }

    protected function setUp()
    {
        $this->renewDateCalculator = new \SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator();
    }
}