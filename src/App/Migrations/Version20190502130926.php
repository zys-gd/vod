<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190502130926 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscription_packs CHANGE carrier_name carrier_name VARCHAR(255) DEFAULT NULL, CHANGE tier_name tier_name VARCHAR(255) DEFAULT NULL, CHANGE tier_id tier_id INT DEFAULT NULL, CHANGE buy_strategy_name buy_strategy_name VARCHAR(255) DEFAULT NULL, CHANGE renew_strategy_name renew_strategy_name VARCHAR(255) DEFAULT NULL, CHANGE billing_carrier_id billing_carrier_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE subscription_packs CHANGE buy_strategy_id buy_strategy_id VARCHAR(36) NOT NULL, CHANGE renew_strategy_id renew_strategy_id VARCHAR(36) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscription_packs CHANGE carrier_name carrier_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE tier_name tier_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE tier_id tier_id INT NOT NULL, CHANGE buy_strategy_name buy_strategy_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE billing_carrier_id billing_carrier_id INT NOT NULL, CHANGE renew_strategy_name renew_strategy_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');

        $this->addSql('ALTER TABLE subscription_packs CHANGE buy_strategy_id buy_strategy_id INT NOT NULL, CHANGE renew_strategy_id renew_strategy_id INT NOT NULL');
    }
}
