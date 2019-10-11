<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191011123757 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO carriers (uuid, billing_carrier_id, operator_id, name, country_code, isp, published, trial_initializer, trial_period, subscription_period, is_campaigns_on_pause, subscribe_attempts, is_unlimited_subscription_attempts_allowed, track_affiliate_on_zero_credit_sub, is_clickable_sub_image, is_one_click_flow) VALUES ('dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 2260, 0, 'TMobile Poland', 'PL', 'T-mobile Polska|blueconnect|T-Mobile Polska S.A.', 1, 'carrier', 0, 7, 0, 1, 1, 0, 1, 0)");
        $this->addSql("INSERT INTO subscription_packs (uuid, country_id, carrier_uuid, status, name, description, is_resub_allowed, tier_id, tier_price, tier_currency, display_currency, credits, periodicity, custom_renew_period, grace_period, unlimited_grace_period, welcome_sms_text, renewal_sms_text, unsubscribe_sms_text, buy_strategy_id, renew_strategy_id, unlimited, is_first_subscription_free_multiple, is_first_subscription_free, allow_bonus_credit, allow_bonus_credit_multiple, bonus_credit, provider_managed_subscriptions, zero_credit_sub_available) VALUES ('d7ejd9e8-2bdc-dj74-a7e6-6d7c86f1hf8d', '5104482b-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 1, 'TMobile PL', '', 0, 2, 13, 'PLN', 'PLN', 2, 7, 0, 0, 0, '', '', '', '1', '1', 0, 0, 0, 0, 0, 0, 0, 0)");

        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb25d3dyd7', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', '48694878641')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM subscription_packs WHERE uuid = 'd7ejd9e8-2bdc-dj74-a7e6-6d7c86f1hf8d'");
        $this->addSql("DELETE FROM carriers WHERE uuid = 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e'");
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb25d3dyd7'");
    }
}
