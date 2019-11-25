<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125142421 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE affiliate_banned_publisher ADD carrier_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE affiliate_banned_publisher ADD CONSTRAINT FK_29D2A3F721DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29D2A3F721DFC797 ON affiliate_banned_publisher (carrier_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE affiliate_banned_publisher DROP FOREIGN KEY FK_29D2A3F721DFC797');
        $this->addSql('DROP INDEX UNIQ_29D2A3F721DFC797 ON affiliate_banned_publisher');
        $this->addSql('ALTER TABLE affiliate_banned_publisher DROP carrier_id');
    }
}
