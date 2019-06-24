<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190619130322 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscription_packs DROP carrier_name, DROP billing_carrier_id, DROP tier_name, DROP buy_strategy_name, DROP renew_strategy_name');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscription_packs ADD carrier_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD billing_carrier_id INT DEFAULT NULL, ADD tier_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD buy_strategy_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD renew_strategy_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
