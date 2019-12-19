<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219112709 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE subscription_reminders (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', subscription_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', last_reminder_sent DATE DEFAULT NULL, UNIQUE INDEX UNIQ_5F2D7B689A1887DC (subscription_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription_reminders ADD CONSTRAINT FK_5F2D7B689A1887DC FOREIGN KEY (subscription_id) REFERENCES subscriptions (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE subscription_reminders');
    }
}
