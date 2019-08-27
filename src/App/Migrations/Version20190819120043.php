<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190819120043 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `subscription_packs` (`uuid`, `country_id`, `carrier_uuid`, `status`, `name`, `description`, `is_resub_allowed`, `tier_price`, `tier_currency`, `display_currency`, `credits`, `periodicity`, `custom_renew_period`, `grace_period`, `unlimited_grace_period`, `preferred_renewal_start`, `preferred_renewal_end`, `welcome_sms_text`, `renewal_sms_text`, `unsubscribe_sms_text`, `tier_id`, `buy_strategy_id`, `renew_strategy_id`, `unlimited`, `is_first_subscription_free_multiple`, `is_first_subscription_free`, `allow_bonus_credit`, `allow_bonus_credit_multiple`, `bonus_credit`, `provider_managed_subscriptions`, `created`, `updated`, `zero_credit_sub_available`) VALUES ('6a26b883-a448-4929-a147-0f45fb31f514', '51045d4c-ebd4-11e8-95c4-02bb250f0f22', '21f27f1f-884c-4b11-9216-c8cf07241bf1', 1, 'Orange Tunisia via MondiaMedia', NULL, 0, '0.50', 'TND', NULL, 0, 1, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 2, 1, '1', 0, 0, 0, 0, 0, 0, 0, '2019-08-19 14:57:48', '2019-08-19 14:57:48', 0);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM `subscription_packs` where `uuid` = '6a26b883-a448-4929-a147-0f45fb31f514'");
    }
}
