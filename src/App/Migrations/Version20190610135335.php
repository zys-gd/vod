<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190610135335 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE campaign_schedule (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', campaign_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', day_start INT NOT NULL, day_end INT NOT NULL, `time_start` TIME NOT NULL, `time_end` TIME NOT NULL, INDEX IDX_6848C784D0986B44 (campaign_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE campaign_schedule ADD CONSTRAINT FK_6848C784D0986B44 FOREIGN KEY (campaign_uuid) REFERENCES campaigns (uuid)');
        $this->addSql('DROP TABLE affiliate_carrier');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE affiliate_carrier (affiliate_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', INDEX IDX_1ADE3A2E9F12C49A (affiliate_id), INDEX IDX_1ADE3A2E21DFC797 (carrier_id), PRIMARY KEY(affiliate_id, carrier_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE campaign_schedule');
    }
}
