<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191003112401 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE `carriers` SET isp = '' WHERE billing_carrier_id = 2253");
        $this->addSql("UPDATE `carriers` SET isp = 'Vodafone Egypt|Vodafone Data' WHERE billing_carrier_id = 2259");

        $this->addSql("UPDATE `subscription_packs` SET status = 0 WHERE uuid = 'a8cc8e95-22c7-4faf-8bd5-1c507f4b4914'");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE `carriers` SET isp = 'Vodafone Egypt' WHERE billing_carrier_id = 2253");

        $this->addSql("UPDATE `subscription_packs` SET status = 1 WHERE uuid = 'a8cc8e95-22c7-4faf-8bd5-1c507f4b4914'");
    }
}
