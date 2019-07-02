<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190619142549 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tiers_values DROP FOREIGN KEY FK_E2BC1256D5CAD932');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31A354F9DC');
        $this->addSql('ALTER TABLE tiers_values DROP FOREIGN KEY FK_E2BC1256A354F9DC');
        $this->addSql('DROP TABLE strategies');
        $this->addSql('DROP TABLE tiers');
        $this->addSql('DROP TABLE tiers_values');
        $this->addSql('DROP INDEX IDX_FF232B31A354F9DC ON games');
        $this->addSql('ALTER TABLE games DROP tier_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE strategies (uuid CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, bf_strategy_id INT DEFAULT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tiers (uuid CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, bf_tier_id INT DEFAULT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tiers_values (uuid CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', tier_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', strategy_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', value DOUBLE PRECISION NOT NULL, currency VARCHAR(3) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(500) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_E2BC1256D5CAD932 (strategy_id), INDEX IDX_E2BC125621DFC797 (carrier_id), INDEX IDX_E2BC1256A354F9DC (tier_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tiers_values ADD CONSTRAINT FK_E2BC125621DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
        $this->addSql('ALTER TABLE tiers_values ADD CONSTRAINT FK_E2BC1256A354F9DC FOREIGN KEY (tier_id) REFERENCES tiers (uuid)');
        $this->addSql('ALTER TABLE tiers_values ADD CONSTRAINT FK_E2BC1256D5CAD932 FOREIGN KEY (strategy_id) REFERENCES strategies (uuid)');
        $this->addSql('ALTER TABLE games ADD tier_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31A354F9DC FOREIGN KEY (tier_id) REFERENCES tiers (uuid)');
        $this->addSql('CREATE INDEX IDX_FF232B31A354F9DC ON games (tier_id)');
    }
}
