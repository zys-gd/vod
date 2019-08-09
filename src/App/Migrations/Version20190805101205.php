<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190805101205 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("INSERT INTO carriers (uuid, default_language_id, billing_carrier_id, operator_id, name, country_code, isp, published, is_confirmation_click, trial_initializer, trial_period, subscription_period, resub_allowed, is_campaigns_on_pause, subscribe_attempts, is_unlimited_subscription_attempts_allowed, number_of_allowed_subscriptions_by_constraint, redirect_url, flush_date, is_cap_alert_dispatch, is_lp_off, track_affiliate_on_zero_credit_sub, is_clickable_sub_image, is_confirmation_popup) VALUES ('038275ac-dae1-446a-8eac-17a26fb69ea2', null, 2255, 0, 'Hutch Indonesia', 'ID', 'Three Indonesia|Three Indonesia|Hutchison CP Telecommunications, PT (ID)|Ruko Malaka Country|Three Indonesia (ID)', 1, 0, 'carrier', 0, 7, 0, 0, 0, 1, null, null, null, 0, 0, 0, 1, 1);");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("DELETE FROM  carriers WHERE uuid = '038275ac-dae1-446a-8eac-17a26fb69ea2'");
    }
}
