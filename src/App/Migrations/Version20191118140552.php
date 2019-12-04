<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Domain\Entity\Carrier;
use CommonDataBundle\Entity\Country;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\SubscriptionPack;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191118140552 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function postUp(Schema $schema)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $egypt = $em->find(Country::class, '5103ce41-ebd4-11e8-95c4-02bb250f0f22');
        $orangeEGTpayCarrier = $em->find(Carrier::class, '7c8385df-8d56-464a-98ff-66c55a7a5741');
        $orangeEGTpayCarrier->setIsp('');

        $orangeEGMMCarrier = new Carrier('352481d3-ff8d-4f02-8219-23f10d359419');
        $orangeEGMMCarrier
            ->setBillingCarrierId(2328) // todo change for live
            ->setName('Orange EG MM')
            ->setCountryCode('EG')
            ->setIsp('MOBINIL')
            ->setPublished(true)
            ->setTrialInitializer('carrier')
            ->setOperatorId(0)
            ->setSubscribeAttempts(5)
            ->setIsCampaignsOnPause(false);

        $orangeEGMMSubPack = new SubscriptionPack('25420b9d-61b2-486b-9a25-1c44f886779e');
        $orangeEGMMSubPack->setCarrier($orangeEGMMCarrier);
        $orangeEGMMSubPack->setName('Orange EG MM');
        $orangeEGMMSubPack->setZeroCreditSubAvailable(false);
        $orangeEGMMSubPack->setProviderManagedSubscriptions(true);
        $orangeEGMMSubPack->setRenewStrategyId(1);
        $orangeEGMMSubPack->setBuyStrategyId(1);
        $orangeEGMMSubPack->setTierId(2);
        $orangeEGMMSubPack->setPeriodicity(1);
        $orangeEGMMSubPack->setTierCurrency('EGP');
        $orangeEGMMSubPack->setTierPrice(3);
        $orangeEGMMSubPack->setStatus(1);
        $orangeEGMMSubPack->setIsResubAllowed(false);
        $orangeEGMMSubPack->setCreated(new \DateTime());
        $orangeEGMMSubPack->setUpdated(new \DateTime());
        $orangeEGMMSubPack->setCountry($egypt);

        $em->persist($orangeEGTpayCarrier);
        $em->persist($orangeEGMMCarrier);
        $em->persist($orangeEGMMSubPack);

        $em->flush();
    }

    public function up(Schema $schema)
    {
        // TODO: Implement up() method.
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
