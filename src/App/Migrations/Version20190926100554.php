<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190926100554 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO carriers (uuid, billing_carrier_id, operator_id, name, country_code, isp, published, is_confirmation_click, trial_initializer, trial_period, subscription_period, is_campaigns_on_pause, subscribe_attempts, is_unlimited_subscription_attempts_allowed, is_lp_off, track_affiliate_on_zero_credit_sub, is_clickable_sub_image, is_confirmation_popup) VALUES ('dk83h8d7-ebd4-12e8-91c4-02bb25d3k8dk', 2258, 0, 'Viva Bahrain', 'BH', 'VIVA Bahrain|VIVA Bahrain|VIVA Bahrain BSC Closed', 1, 0, 'carrier', 0, 1, 0, 1, 1, 0, 0, 1, 0)");
        $this->addSql("INSERT INTO subscription_packs (uuid, country_id, carrier_uuid, status, name, description, is_resub_allowed, tier_id, tier_price, tier_currency, display_currency, credits, periodicity, custom_renew_period, grace_period, unlimited_grace_period, welcome_sms_text, renewal_sms_text, unsubscribe_sms_text, buy_strategy_id, renew_strategy_id, unlimited, is_first_subscription_free_multiple, is_first_subscription_free, allow_bonus_credit, allow_bonus_credit_multiple, bonus_credit, provider_managed_subscriptions, zero_credit_sub_available) VALUES ('js8d7r74-2bdc-dj74-a7e6-6d7c86fl1d7d', '5103ad63-ebd4-11e8-95c4-02bb250f0f22', 'dk83h8d7-ebd4-12e8-91c4-02bb25d3k8dk', 1, 'Viva Bahrain', '', 0, 2, 0.15, 'BHD', 'BHD', 2, 1, 0, 0, 0, '', '', '', '1', '1', 0, 0, 1, 0, 0, 0, 1, 0)");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM subscription_packs WHERE uuid = 'dk83h8d7-ebd4-12e8-91c4-02bb25d3k8dk'");
        $this->addSql("DELETE FROM carriers WHERE uuid = 'js8d7r74-2bdc-dj74-a7e6-6d7c86fl1d7d'");
    }
}
