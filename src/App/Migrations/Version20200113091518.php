<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200113091518 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `carriers` (`uuid`, `default_language_id`, `billing_carrier_id`, `operator_id`, `name`, `country_code`, `isp`, `published`, `trial_initializer`, `is_campaigns_on_pause`, `subscribe_attempts`, `number_of_allowed_subscriptions_by_constraint`, `redirect_url`, `flush_date`, `is_cap_alert_dispatch`, `is_clickable_sub_image`, `is_one_click_flow`) VALUES
('3de06e41-f889-4f95-aac3-ebd31d9d7d9a', NULL, 2320, 0, 'Vodafone DE', 'DE', 'Vodafone GmbH|Vodafone GmbH|Vodafone GmbH', 1, 'carrier', 0, 0, 0, 'https://www.google.com/', NULL, 0, 1, 0);");
        $this->addSql("INSERT INTO `subscription_packs` (`uuid`, `country_id`, `carrier_uuid`, `status`, `name`, `description`, `is_resub_allowed`, `tier_price`, `tier_currency`, `display_currency`, `credits`, `periodicity`, `custom_renew_period`, `grace_period`, `unlimited_grace_period`, `preferred_renewal_start`, `preferred_renewal_end`, `welcome_sms_text`, `renewal_sms_text`, `unsubscribe_sms_text`, `tier_id`, `buy_strategy_id`, `renew_strategy_id`, `unlimited`, `is_first_subscription_free_multiple`, `is_first_subscription_free`, `allow_bonus_credit`, `allow_bonus_credit_multiple`, `bonus_credit`, `provider_managed_subscriptions`, `created`, `updated`, `zero_credit_sub_available`, `track_affiliate_on_zero_credit_sub`, `trial_period`) VALUES
('27261246-9034-438f-ba37-274dd6d5k9a8', '5103cc4f-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', 1, 'Vodafone DE', '', 0, '3.99', 'EUR', '€', 2, 7, 0, 0, 0, '02:00:00', '21:00:00', '', '', '', 2, 1, 1, 0, 0, 0, 0, 0, 0, 1, '2020-01-13 13:55:10', '2020-01-13 11:56:06', 0, 0, 0);
");

        $this->addSql("INSERT INTO `carriers` (`uuid`, `default_language_id`, `billing_carrier_id`, `operator_id`, `name`, `country_code`, `isp`, `published`, `trial_initializer`, `is_campaigns_on_pause`, `subscribe_attempts`, `number_of_allowed_subscriptions_by_constraint`, `redirect_url`, `flush_date`, `is_cap_alert_dispatch`, `is_clickable_sub_image`, `is_one_click_flow`) VALUES
('3de06e41-f889-4f95-aac3-ebd31d97d9a4', NULL, 2321, 0, 'Telekom DE', 'DE', 'Deutsche Telekom AG|T-Mobile Deutschland GmbH|Deutsche Telekom AG (DE)', 1, 'carrier', 0, 0, 0, 'https://www.google.com/', NULL, 0, 1, 0);");
        $this->addSql("INSERT INTO `subscription_packs` (`uuid`, `country_id`, `carrier_uuid`, `status`, `name`, `description`, `is_resub_allowed`, `tier_price`, `tier_currency`, `display_currency`, `credits`, `periodicity`, `custom_renew_period`, `grace_period`, `unlimited_grace_period`, `preferred_renewal_start`, `preferred_renewal_end`, `welcome_sms_text`, `renewal_sms_text`, `unsubscribe_sms_text`, `tier_id`, `buy_strategy_id`, `renew_strategy_id`, `unlimited`, `is_first_subscription_free_multiple`, `is_first_subscription_free`, `allow_bonus_credit`, `allow_bonus_credit_multiple`, `bonus_credit`, `provider_managed_subscriptions`, `created`, `updated`, `zero_credit_sub_available`, `track_affiliate_on_zero_credit_sub`, `trial_period`) VALUES
('27261246-9034-438f-ba37-274dd6dk9a83', '5103cc4f-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', 1, 'Telekom DE', '', 0, '50.00', 'EUR', '€', 2, 7, 0, 0, 0, '02:00:00', '21:00:00', '', '', '', 2, 1, 1, 0, 0, 0, 0, 0, 0, 1, '2020-01-13 13:55:10', '2020-01-13 11:56:06', 0, 0, 0);
");

        $this->addSql("INSERT INTO `carriers` (`uuid`, `default_language_id`, `billing_carrier_id`, `operator_id`, `name`, `country_code`, `isp`, `published`, `trial_initializer`, `is_campaigns_on_pause`, `subscribe_attempts`, `number_of_allowed_subscriptions_by_constraint`, `redirect_url`, `flush_date`, `is_cap_alert_dispatch`, `is_clickable_sub_image`, `is_one_click_flow`) VALUES
('3de06e41-f889-4f95-aac3-ebd317d9adg4', NULL, 2322, 0, 'O2 DE', 'DE', 'O2 Deutschland|O2 Deutschland|Telefonica Germany (DE)', 1, 'carrier', 0, 0, 0, 'https://www.google.com/', NULL, 0, 1, 0);");
        $this->addSql("INSERT INTO `subscription_packs` (`uuid`, `country_id`, `carrier_uuid`, `status`, `name`, `description`, `is_resub_allowed`, `tier_price`, `tier_currency`, `display_currency`, `credits`, `periodicity`, `custom_renew_period`, `grace_period`, `unlimited_grace_period`, `preferred_renewal_start`, `preferred_renewal_end`, `welcome_sms_text`, `renewal_sms_text`, `unsubscribe_sms_text`, `tier_id`, `buy_strategy_id`, `renew_strategy_id`, `unlimited`, `is_first_subscription_free_multiple`, `is_first_subscription_free`, `allow_bonus_credit`, `allow_bonus_credit_multiple`, `bonus_credit`, `provider_managed_subscriptions`, `created`, `updated`, `zero_credit_sub_available`, `track_affiliate_on_zero_credit_sub`, `trial_period`) VALUES
('27261246-9034-438f-ba37-274dd6k9a8d3', '5103cc4f-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', 1, 'O2 DE', '', 0, '50.00', 'EUR', '€', 2, 7, 0, 0, 0, '02:00:00', '21:00:00', '', '', '', 2, 1, 1, 0, 0, 0, 0, 0, 0, 1, '2020-01-13 13:55:10', '2020-01-13 11:56:06', 0, 0, 0);
");

        $this->addSql("INSERT INTO `carriers` (`uuid`, `default_language_id`, `billing_carrier_id`, `operator_id`, `name`, `country_code`, `isp`, `published`, `trial_initializer`, `is_campaigns_on_pause`, `subscribe_attempts`, `number_of_allowed_subscriptions_by_constraint`, `redirect_url`, `flush_date`, `is_cap_alert_dispatch`, `is_clickable_sub_image`, `is_one_click_flow`) VALUES
('3de06e41-f889-4f95-aac3-ebd31d7d9ag4', NULL, 2323, 0, 'Debitel DE', 'DE', 'freenet Datenkommunikations GmbH|mobilcom-debitel GmbH|freenet Datenkommunikations GmbH', 1, 'carrier', 0, 0, 0, 'https://www.google.com/', NULL, 0, 1, 0);");
        $this->addSql("INSERT INTO `subscription_packs` (`uuid`, `country_id`, `carrier_uuid`, `status`, `name`, `description`, `is_resub_allowed`, `tier_price`, `tier_currency`, `display_currency`, `credits`, `periodicity`, `custom_renew_period`, `grace_period`, `unlimited_grace_period`, `preferred_renewal_start`, `preferred_renewal_end`, `welcome_sms_text`, `renewal_sms_text`, `unsubscribe_sms_text`, `tier_id`, `buy_strategy_id`, `renew_strategy_id`, `unlimited`, `is_first_subscription_free_multiple`, `is_first_subscription_free`, `allow_bonus_credit`, `allow_bonus_credit_multiple`, `bonus_credit`, `provider_managed_subscriptions`, `created`, `updated`, `zero_credit_sub_available`, `track_affiliate_on_zero_credit_sub`, `trial_period`) VALUES
('27261246-9034-438f-ba37-274ddk9a84d3', '5103cc4f-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', 1, 'Debitel DE', '', 0, '50.00', 'EUR', '€', 2, 7, 0, 0, 0, '02:00:00', '21:00:00', '', '', '', 2, 1, 1, 0, 0, 0, 0, 0, 0, 1, '2020-01-13 13:55:10', '2020-01-13 11:56:06', 0, 0, 0);
");

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM carrier WHERE uuid = '3de06e41-f889-4f95-aac3-ebd31d9d7d9a'");
        $this->addSql("DELETE FROM carrier WHERE uuid = '3de06e41-f889-4f95-aac3-ebd31d97d9a4'");
        $this->addSql("DELETE FROM carrier WHERE uuid = '3de06e41-f889-4f95-aac3-ebd317d9adg4'");
        $this->addSql("DELETE FROM carrier WHERE uuid = '3de06e41-f889-4f95-aac3-ebd31d7d9ag4'");
        $this->addSql("DELETE FROM subscription_packs WHERE uuid = '27261246-9034-438f-ba37-274dd6d5k9a8'");
        $this->addSql("DELETE FROM subscription_packs WHERE uuid = '27261246-9034-438f-ba37-274dd6dk9a83'");
        $this->addSql("DELETE FROM subscription_packs WHERE uuid = '27261246-9034-438f-ba37-274dd6k9a8d3'");
        $this->addSql("DELETE FROM subscription_packs WHERE uuid = '27261246-9034-438f-ba37-274ddk9a84d3'");
    }
}
