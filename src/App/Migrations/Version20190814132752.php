<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190814132752 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `carriers` (`uuid`, `default_language_id`, `billing_carrier_id`, `operator_id`, `name`, `country_code`, `isp`, `published`, `is_confirmation_click`, `is_confirmation_popup`, `trial_initializer`, `trial_period`, `subscription_period`, `resub_allowed`, `is_campaigns_on_pause`, `subscribe_attempts`, `is_unlimited_subscription_attempts_allowed`, `number_of_allowed_subscriptions_by_constraint`, `redirect_url`, `flush_date`, `is_cap_alert_dispatch`, `is_lp_off`, `is_clickable_sub_image`, `track_affiliate_on_zero_credit_sub`) VALUES ('21f27f1f-884c-4b11-9216-c8cf07241bf1', '5179f269-ebd4-11e8-95c4-02bb250f0f22', '2256', '0', 'Orange TN MM', 'TN', 'Orange Internet|Orange Tunisia|Tunisia Telecom|Orange Tunisia Smartphone customer|Tunisie Telecom|ORANGE|Orange Tunisie', '1', '1', '0', 'carrier', '0', '1', '0', '0', '1', '1', NULL, NULL, NULL, '0', '0', '1', '0');");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM `carriers` WHERE `carriers`.`uuid` = '21f27f1f-884c-4b11-9216-c8cf07241bf1'");
    }
}
