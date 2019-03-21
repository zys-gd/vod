<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190321095152 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE test_user (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', user_identifier VARCHAR(255) NOT NULL, added_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_time_used_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_88EAFC86D0494586 (user_identifier), INDEX IDX_88EAFC8621DFC797 (carrier_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE test_user ADD CONSTRAINT FK_88EAFC8621DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE test_user');
    }
}
