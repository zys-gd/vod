<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322161418 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE constraints_by_affiliate (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', affiliate_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', number_of_actions INT DEFAULT NULL, redirect_url VARCHAR(255) DEFAULT NULL, counter INT DEFAULT 0 NOT NULL, flush_date DATE DEFAULT NULL, is_cap_alert_dispatch TINYINT(1) DEFAULT \'0\' NOT NULL, cap_type VARCHAR(255) NOT NULL, INDEX IDX_84A864FD9F12C49A (affiliate_id), INDEX IDX_84A864FD21DFC797 (carrier_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE constraints_by_affiliate ADD CONSTRAINT FK_84A864FD9F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliates (uuid)');
        $this->addSql('ALTER TABLE constraints_by_affiliate ADD CONSTRAINT FK_84A864FD21DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE constraints_by_affiliate');
    }
}
