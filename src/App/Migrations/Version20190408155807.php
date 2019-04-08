<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190408155807 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE video_partners (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE uploaded_video ADD video_partner_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE uploaded_video ADD CONSTRAINT FK_749275C01334E048 FOREIGN KEY (video_partner_id) REFERENCES video_partners (uuid)');
        $this->addSql('CREATE INDEX IDX_749275C01334E048 ON uploaded_video (video_partner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE uploaded_video DROP FOREIGN KEY FK_749275C01334E048');
        $this->addSql('DROP TABLE video_partners');
        $this->addSql('DROP INDEX IDX_749275C01334E048 ON uploaded_video');
        $this->addSql('ALTER TABLE uploaded_video DROP video_partner_id');
    }
}
