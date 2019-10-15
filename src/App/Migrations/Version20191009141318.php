<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191009141318 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("
        UPDATE subscription_packs SET `buy_strategy_id` = '11' WHERE `subscription_packs`.`uuid` = '472347c2-ebd4-11e8-95c4-02bb250d5d33';
        UPDATE subscription_packs SET `preferred_renewal_start` = '1970-01-01T17:11:16+02:00',`preferred_renewal_end` = '1970-01-01T17:11:16+02:00',`is_first_subscription_free` = '0' WHERE `subscription_packs`.`uuid` = '6a26b883-a448-4929-a147-0f45fb31f514';
        UPDATE subscription_packs SET `preferred_renewal_start` = '1970-01-01T17:11:16+02:00',`preferred_renewal_end` = '1970-01-01T17:11:16+02:00' WHERE `subscription_packs`.`uuid` = '7ae2c0e1-c83b-49e1-81e5-8049027515f3';
        UPDATE subscription_packs SET `preferred_renewal_start` = '1970-01-01T17:11:16+02:00',`preferred_renewal_end` = '1970-01-01T17:11:16+02:00' WHERE `subscription_packs`.`uuid` = 'a8cc8e95-22c7-4faf-8bd5-1c507f4b4914';
        UPDATE subscription_packs SET `buy_strategy_id` = '59' WHERE `subscription_packs`.`uuid` = 'c9f648b4-ac5a-43d5-9ca2-33dac677d03d';
        UPDATE subscription_packs SET `buy_strategy_id` = '41' WHERE `subscription_packs`.`uuid` = 'f5a21dba-2bdc-4159-a7e6-6d7c86d9fe94';
        UPDATE subscription_packs SET `is_resub_allowed` = '0',`tier_price` = '1.00',`tier_currency` = 'SAR',`display_currency` = 'SAR',`custom_renew_period` = '3',`grace_period` = '7',`unlimited_grace_period` = '0',`preferred_renewal_start` = '1970-01-01T06:00:00+02:00',`preferred_renewal_end` = '1970-01-01T21:00:00+02:00',`welcome_sms_text` = '',`renewal_sms_text` = '',`unsubscribe_sms_text` = '',`tier_id` = '2',`buy_strategy_id` = '60',`renew_strategy_id` = '60',`is_first_subscription_free_multiple` = '0',`is_first_subscription_free` = '0',`allow_bonus_credit` = '0',`allow_bonus_credit_multiple` = '0',`bonus_credit` = '0',`provider_managed_subscriptions` = '0',`created` = '2019-08-16T09:51:28+03:00',`updated` = '2019-08-16T09:51:28+03:00',`zero_credit_sub_available` = '0' WHERE `subscription_packs`.`uuid` = 'f5dj8r74-2bdc-dj74-a7e6-6d7c86flg7r6'        
        ");

    }

    public function down(Schema $schema) : void
    {
        $this->addSql("        
        UPDATE subscription_packs SET `buy_strategy_id` = '13' WHERE `subscription_packs`.`uuid` = '472347c2-ebd4-11e8-95c4-02bb250d5d33';
        UPDATE subscription_packs SET `preferred_renewal_start` = '1970-01-01T12:50:36+02:00',`preferred_renewal_end` = '1970-01-01T12:50:36+02:00',`is_first_subscription_free` = '1' WHERE `subscription_packs`.`uuid` = '6a26b883-a448-4929-a147-0f45fb31f514';
        UPDATE subscription_packs SET `preferred_renewal_start` = '1970-01-01T12:50:36+02:00',`preferred_renewal_end` = '1970-01-01T12:50:36+02:00' WHERE `subscription_packs`.`uuid` = '7ae2c0e1-c83b-49e1-81e5-8049027515f3';
        UPDATE subscription_packs SET `preferred_renewal_start` = '1970-01-01T12:50:36+02:00',`preferred_renewal_end` = '1970-01-01T12:50:36+02:00' WHERE `subscription_packs`.`uuid` = 'a8cc8e95-22c7-4faf-8bd5-1c507f4b4914';
        UPDATE subscription_packs SET `buy_strategy_id` = '58' WHERE `subscription_packs`.`uuid` = 'c9f648b4-ac5a-43d5-9ca2-33dac677d03d';
        UPDATE subscription_packs SET `buy_strategy_id` = '44' WHERE `subscription_packs`.`uuid` = 'f5a21dba-2bdc-4159-a7e6-6d7c86d9fe94';
        UPDATE subscription_packs SET `is_resub_allowed` = NULL,`tier_price` = NULL,`tier_currency` = NULL,`display_currency` = NULL,`custom_renew_period` = NULL,`grace_period` = NULL,`unlimited_grace_period` = NULL,`preferred_renewal_start` = NULL,`preferred_renewal_end` = NULL,`welcome_sms_text` = NULL,`renewal_sms_text` = NULL,`unsubscribe_sms_text` = NULL,`tier_id` = NULL,`buy_strategy_id` = NULL,`renew_strategy_id` = NULL,`is_first_subscription_free_multiple` = NULL,`is_first_subscription_free` = NULL,`allow_bonus_credit` = NULL,`allow_bonus_credit_multiple` = NULL,`bonus_credit` = NULL,`provider_managed_subscriptions` = NULL,`created` = '2019-08-16 09:51:28',`updated` = '2019-08-16 09:51:28',`zero_credit_sub_available` = NULL WHERE `subscription_packs`.`uuid` = 'f5dj8r74-2bdc-dj74-a7e6-6d7c86flg7r6'
        ");

    }
}
