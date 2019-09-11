<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190816114649 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO carriers (uuid, billing_carrier_id, operator_id, name, country_code, isp, published, is_confirmation_click, trial_initializer, trial_period, subscription_period, is_campaigns_on_pause, subscribe_attempts, is_unlimited_subscription_attempts_allowed, number_of_allowed_subscriptions_by_constraint, is_lp_off, track_affiliate_on_zero_credit_sub, is_clickable_sub_image, is_confirmation_popup) VALUES ('51dj58d7-ebd4-12e8-91c4-02bb25dlf94j', 2257, 0, 'Zain KSA', 'SA', 'Zain Saudi Arabia', 1, 1, 'store', 0, 1, 0, 5, 0, 1000, 0, 0, 1, 0)");
        $this->addSql("INSERT INTO subscription_packs (uuid, country_id, carrier_uuid, status, name, description, is_resub_allowed, tier_id, tier_price, tier_currency, display_currency, credits, periodicity, custom_renew_period, grace_period, unlimited_grace_period, preferred_renewal_start, preferred_renewal_end, welcome_sms_text, renewal_sms_text, unsubscribe_sms_text, buy_strategy_id, renew_strategy_id, unlimited, is_first_subscription_free_multiple, is_first_subscription_free, allow_bonus_credit, allow_bonus_credit_multiple, bonus_credit, provider_managed_subscriptions, created, updated, zero_credit_sub_available) VALUES ('f5dj8r74-2bdc-dj74-a7e6-6d7c86flg7r6', '51044eaa-ebd4-11e8-95c4-02bb250f0f22', '51dj58d7-ebd4-12e8-91c4-02bb25dlf94j', 1, 'Zain KSA', '', 0, 2, 1.00, 'SAR', 'SAR', 2, 1, 3, 7, 0, '06:00:00', '21:00:00', '', '', '', '60', '60', 0, 0, 0, 0, 0, 0, 0, '2019-08-16 09:51:28', '2019-08-16 09:51:28', 0)");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM carriers WHERE uuid = '51dj58d7-ebd4-12e8-91c4-02bb25dlf94j'");
        $this->addSql("DELETE FROM subscription_packs WHERE uuid = 'f5dj8r74-2bdc-dj74-a7e6-6d7c86flg7r6'");
    }
}
