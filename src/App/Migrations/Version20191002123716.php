<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191002123716 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `carriers` (`uuid`, `default_language_id`, `billing_carrier_id`, `operator_id`, `name`, `country_code`, `isp`, `published`, `is_confirmation_click`, `is_confirmation_popup`, `trial_initializer`, `trial_period`, `subscription_period`, `is_campaigns_on_pause`, `subscribe_attempts`, `is_unlimited_subscription_attempts_allowed`, `number_of_allowed_subscriptions_by_constraint`, `redirect_url`, `flush_date`, `is_cap_alert_dispatch`, `is_lp_off`, `is_clickable_sub_image`, `track_affiliate_on_zero_credit_sub`) VALUES ('ac8ac2ed-2885-40b2-acf5-874aef51e34d', '5179f269-ebd4-11e8-95c4-02bb250f0f22', '2259', '0', 'Vodafone EG', 'TN', 'Vodafone Egypt', '1', '0', '0', 'carrier', '0', '1', '0', '1', '1', NULL, NULL, NULL, '0', '0', '1', '0');");

        $this->addSql("INSERT INTO `subscription_packs` (`uuid`, `country_id`, `carrier_uuid`, `status`, `name`, `description`, `is_resub_allowed`, `tier_price`, `tier_currency`, `display_currency`, `credits`, `periodicity`, `custom_renew_period`, `grace_period`, `unlimited_grace_period`, `preferred_renewal_start`, `preferred_renewal_end`, `welcome_sms_text`, `renewal_sms_text`, `unsubscribe_sms_text`, `tier_id`, `buy_strategy_id`, `renew_strategy_id`, `unlimited`, `is_first_subscription_free_multiple`, `is_first_subscription_free`, `allow_bonus_credit`, `allow_bonus_credit_multiple`, `bonus_credit`, `provider_managed_subscriptions`, `created`, `updated`, `zero_credit_sub_available`) VALUES ('680778f1-51f8-4b35-a83c-5300043695f8', '5103ce41-ebd4-11e8-95c4-02bb250f0f22', 'ac8ac2ed-2885-40b2-acf5-874aef51e34d', 1, 'Vodafone Egypt via MondiaMedia', NULL, 0, '3', 'EGP', NULL, 0, 1, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 2, 1, '1', 0, 0, 0, 0, 0, 0, 0, '2019-08-19 14:57:48', '2019-08-19 14:57:48', 0);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM `carriers` WHERE `uuid` = 'ac8ac2ed-2885-40b2-acf5-874aef51e34d'");

        $this->addSql("DELETE FROM `subscription_packs` WHERE `uuid` = '680778f1-51f8-4b35-a83c-5300043695f8'");
    }
}
