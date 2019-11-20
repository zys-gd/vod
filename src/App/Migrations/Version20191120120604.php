<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\SubscriptionPack;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120120604 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE `carriers`  DROP `trial_period`,  DROP `subscription_period`;");

        $this->addSql("ALTER TABLE subscription_packs ADD trial_period INT(11) DEFAULT '0' NOT NULL");
    }

    public function postUp(Schema $schema)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $subscriptionPackRepository = $em->getRepository(SubscriptionPack::class);
        $subscriptionPacks          = $subscriptionPackRepository->findAll();
        foreach ($subscriptionPacks as $subscriptionPack) {
            $subscriptionPack->setTrialPeriod($subscriptionPack->getFinalPeriodForSubscription());
            $em->persist($subscriptionPack);
        }

        $em->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE carriers ADD trial_period INT(11) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE carriers ADD subscription_period INT(11) DEFAULT '0' NOT NULL");

        $this->addSql("ALTER TABLE `subscription_packs`  DROP `trial_period`;");
    }
}
