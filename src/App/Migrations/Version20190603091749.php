<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190603091749 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE affiliate_carrier (affiliate_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_1ADE3A2E9F12C49A (affiliate_id), INDEX IDX_1ADE3A2E21DFC797 (carrier_id), PRIMARY KEY(affiliate_id, carrier_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affiliate_carrier ADD CONSTRAINT FK_1ADE3A2E9F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliates (uuid)');
        $this->addSql('ALTER TABLE affiliate_carrier ADD CONSTRAINT FK_1ADE3A2E21DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE affiliate_carrier');
    }
}
