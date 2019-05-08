<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190508135235 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE carriers ADD is_lp_off TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE affiliates ADD is_lp_off TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE campaigns ADD is_lp_off TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE affiliates DROP is_lp_off');
        $this->addSql('ALTER TABLE campaigns DROP is_lp_off');
        $this->addSql('ALTER TABLE carriers DROP is_lp_off');
    }
}
