<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190805101714 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("INSERT INTO subscription_packs (uuid, country_id, carrier_uuid, status, name, description, is_resub_allowed, tier_price, tier_currency, display_currency, credits, periodicity, custom_renew_period, grace_period, unlimited_grace_period, preferred_renewal_start, preferred_renewal_end, welcome_sms_text, renewal_sms_text, unsubscribe_sms_text, tier_id, buy_strategy_id, renew_strategy_id, unlimited, is_first_subscription_free_multiple, is_first_subscription_free, allow_bonus_credit, allow_bonus_credit_multiple, bonus_credit, provider_managed_subscriptions, created, updated, zero_credit_sub_available) VALUES ('f5a21dba-2bdc-4159-a7e6-6d7c86d9fe94', '5103ed28-ebd4-11e8-95c4-02bb250f0f22', '038275ac-dae1-446a-8eac-17a26fb69ea2', 1, 'Hutch Indonesia', '', 0, 2200.00, 'IDR', 'Rp.', 2, 8, 3, 7, 0, '06:00:00', '21:00:00', '', '', '', 2, 44, '41', 0, 0, 0, 0, 0, 0, 0, '2017-12-08 09:51:28', '2018-04-18 08:10:01', 0);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DELETE FROM subscription_packs WHERE uuid = "f5a21dba-2bdc-4159-a7e6-6d7c86d9fe94"');
    }
}
