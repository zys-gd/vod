<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190911070856 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE campaigns ADD free_trial_price DECIMAL(10, 2) DEFAULT 0.00 NOT NULL");
        $this->addSql("ALTER TABLE campaigns ADD zero_eur_price DECIMAL(10, 2) DEFAULT 0.00 NOT NULL");
        $this->addSql("ALTER TABLE campaigns ADD general_price DECIMAL(10, 2) DEFAULT 0.00 NOT NULL");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE campaigns DROP COLUMN free_trial_price");
        $this->addSql("ALTER TABLE campaigns DROP COLUMN zero_eur_price");
        $this->addSql("ALTER TABLE campaigns DROP COLUMN general_price");
    }
}
