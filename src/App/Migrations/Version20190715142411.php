<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190715142411 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE affiliate_banned_publisher (publisher_id VARCHAR(255) NOT NULL, affiliate_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_29D2A3F79F12C49A (affiliate_id), PRIMARY KEY(publisher_id, affiliate_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affiliate_banned_publisher ADD CONSTRAINT FK_29D2A3F79F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliates (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE affiliate_banned_publisher');
    }
}
