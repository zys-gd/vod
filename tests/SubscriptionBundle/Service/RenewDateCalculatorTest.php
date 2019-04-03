<?php


namespace App\Tests\SubscriptionBundle\Service;

use App\Domain\Entity\Language;
use App\Domain\Entity\Translation;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Repository\LanguageRepository;
use App\Domain\Repository\TranslationRepository;
use App\Domain\Service\Translator\Translator;
use App\Utils\UuidGenerator;
use Carbon\Carbon;
use ExtrasBundle\Cache\ICacheService;
use PHPUnit\Framework\TestCase;
use Mockery;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\RenewDateCalculator;


class RenewDateCalculatorTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var RenewDateCalculator
     */
    private $translatorProvider;

    /**
     * @throws \Exception
     */
    public function testCalculateRenewDate()
    {
        $knownDate = Carbon::create(2019, 4, 3, 01);
        Carbon::setTestNow($knownDate);

        $subscriptionPack = new SubscriptionPack(UuidGenerator::generate());
        $subscriptionPack->setPreferredRenewalStart(new \DateTime('02:00:00'));
        $subscriptionPack->setPreferredRenewalEnd(new \DateTime('21:00:00'));
        $subscriptionPack->setPeriodicity(1);

        $subscription = new Subscription(UuidGenerator::generate());
        $subscription->setSubscriptionPack($subscriptionPack);

        $renewDate = $this->translatorProvider->calculateRenewDate($subscription);

        $this->assertTrue(
            $renewDate->between(
                Carbon::instance($subscriptionPack->getPreferredRenewalStart())->addDay(),
                Carbon::instance($subscriptionPack->getPreferredRenewalEnd())->addDay()
            )
        );
    }

    protected function setUp()
    {
        $this->translatorProvider = new RenewDateCalculator();
    }
}