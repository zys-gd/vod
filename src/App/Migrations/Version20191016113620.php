<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191016113620 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `campaign_schedule`');
        $this->addSql('ALTER TABLE `campaigns` ADD `schedule` LONGTEXT NOT NULL DEFAULT "" COLLATE utf8_unicode_ci');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE `campaign_schedule` (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', campaign_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', day_start INT NOT NULL, day_end INT NOT NULL, time_start TIME NOT NULL, time_end TIME NOT NULL, INDEX IDX_6848C784D0986B44 (campaign_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        // $this->addSql('ALTER TABLE `campaign_schedule` ADD CONSTRAINT FK_6848C784D0986B44 FOREIGN KEY (campaign_uuid) REFERENCES `campaigns` (uuid)');
        $this->addSql('ALTER TABLE `campaigns` DROP `schedule`');
    }
}
