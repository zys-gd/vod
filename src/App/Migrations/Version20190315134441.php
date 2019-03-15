<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190315134441 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE languages (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) DEFAULT \'\', code VARCHAR(2) NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carriers (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', default_language_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', billing_carrier_id INT NOT NULL, operator_id INT DEFAULT 0 NOT NULL, name VARCHAR(255) NOT NULL, country_code VARCHAR(2) NOT NULL, isp VARCHAR(255) DEFAULT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, lp_otp TINYINT(1) DEFAULT \'0\' NOT NULL, pin_ident_support TINYINT(1) DEFAULT \'0\' NOT NULL, trial_initializer VARCHAR(10) DEFAULT \'carrier\' NOT NULL, trial_period INT DEFAULT 0 NOT NULL, subscription_period INT DEFAULT 7 NOT NULL, resub_allowed TINYINT(1) DEFAULT \'0\' NOT NULL, is_campaigns_on_pause TINYINT(1) DEFAULT \'0\' NOT NULL, number_of_allowed_subscription INT DEFAULT 1 NOT NULL, is_unlimited_subscription_attempts_allowed TINYINT(1) DEFAULT \'1\' NOT NULL, is_captcha TINYINT(1) DEFAULT \'0\' NOT NULL, number_of_allowed_subscriptions_by_constraint INT DEFAULT NULL, redirect_url VARCHAR(255) DEFAULT NULL, counter INT DEFAULT 0 NOT NULL, flush_date DATE DEFAULT NULL, is_cap_alert_dispatch TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_F48AAB5ADB8A806 (billing_carrier_id), INDEX IDX_F48AAB55602A942 (default_language_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admins (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_A2E0150F92FC23A8 (username_canonical), UNIQUE INDEX UNIQ_A2E0150FA0D96FBF (email_canonical), UNIQUE INDEX UNIQ_A2E0150FC05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE affiliates (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', country_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, type INT NOT NULL, url VARCHAR(255) DEFAULT NULL, postback_url VARCHAR(255) NOT NULL, commercial_contact VARCHAR(255) DEFAULT NULL, technical_contact VARCHAR(255) DEFAULT NULL, skype_id VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, sub_price_name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_108C6A8F5E237E06 (name), INDEX IDX_108C6A8FF92F3E70 (country_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE affiliate_constants (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', affiliate_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_720BED969F12C49A (affiliate_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE affiliate_parameters (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', affiliate_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', input_name VARCHAR(255) NOT NULL, output_name VARCHAR(255) NOT NULL, INDEX IDX_371C00039F12C49A (affiliate_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE black_list (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', billing_carrier_id INT DEFAULT NULL, alias VARCHAR(255) NOT NULL, is_blocked_manually TINYINT(1) DEFAULT \'1\' NOT NULL, added_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX alias_index (alias), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaigns (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', affiliate_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', main_category_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', image_name VARCHAR(255) NOT NULL, bg_color VARCHAR(7) DEFAULT \'#000000\', text_color VARCHAR(7) DEFAULT \'#000000\', campaign_token VARCHAR(255) NOT NULL, test_url VARCHAR(255) DEFAULT NULL, is_pause TINYINT(1) DEFAULT \'0\' NOT NULL, counter INT DEFAULT 0 NOT NULL, flush_date DATE DEFAULT NULL, ppd NUMERIC(10, 2) DEFAULT NULL, sub NUMERIC(10, 2) DEFAULT NULL, click NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_E37374709F12C49A (affiliate_id), INDEX IDX_E3737470C6C55574 (main_category_id), INDEX campaign_token_index (campaign_token), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaign_carrier (campaign_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_EF836D30F639F774 (campaign_id), INDEX IDX_EF836D3021DFC797 (carrier_id), PRIMARY KEY(campaign_id, carrier_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE countries (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', country_code VARCHAR(2) NOT NULL, country_name VARCHAR(100) NOT NULL, currency_code VARCHAR(3) DEFAULT NULL, iso_numeric VARCHAR(4) DEFAULT NULL, iso_alpha3 VARCHAR(3) DEFAULT NULL, UNIQUE INDEX UNIQ_5D66EBADF026BB7C (country_code), INDEX country_code_idx (country_code), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country_category_priority_overrides (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', main_category_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', country_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', menu_priority INT DEFAULT 0 NOT NULL, INDEX IDX_AA2DAE42C6C55574 (main_category_id), INDEX IDX_AA2DAE42F92F3E70 (country_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE developers (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_displays (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', developer_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', tier_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, icon VARCHAR(255) NOT NULL, thumbnail VARCHAR(255) NOT NULL, rating INT NOT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, is_bookmark TINYINT(1) DEFAULT \'0\' NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_FF232B3164DD9267 (developer_id), INDEX IDX_FF232B31A354F9DC (tier_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_builds (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', game_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', os_type INT NOT NULL, min_os_version VARCHAR(255) NOT NULL, game_apk VARCHAR(255) DEFAULT NULL, apk_size INT DEFAULT NULL, version INT DEFAULT NULL, apk_version VARCHAR(255) DEFAULT NULL, INDEX IDX_7787C96DE48FD905 (game_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_builds_device_displays (game_build_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', device_display_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_440E3E6EBC85C0AD (game_build_id), INDEX IDX_440E3E6ED6919CF5 (device_display_id), PRIMARY KEY(game_build_id, device_display_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_images (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', game_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, INDEX IDX_9D2A13A2E48FD905 (game_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_categories (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, menu_priority INT DEFAULT 0 NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subcategories (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', parent CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, INDEX IDX_6562A1CB3D8E604F (parent), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translations (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', language_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', `key` VARCHAR(255) NOT NULL, translation LONGTEXT NOT NULL, INDEX IDX_C6B7DA8782F1BAF4 (language_id), INDEX IDX_C6B7DA8721DFC797 (carrier_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE uploaded_video (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', subcategory_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', status SMALLINT NOT NULL, remote_url VARCHAR(255) NOT NULL, remote_id VARCHAR(255) NOT NULL, created_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, expired_date DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, thumbnails LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_749275C02A3E9C94 (remote_id), INDEX IDX_749275C05DC6FE57 (subcategory_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', identifier VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, ip VARCHAR(15) NOT NULL, affiliate_token LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', url_id VARCHAR(100) DEFAULT NULL, short_url_id VARCHAR(100) DEFAULT NULL, added DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, identification_process_id VARCHAR(255) DEFAULT NULL, identification_token VARCHAR(255) DEFAULT NULL, connection_type VARCHAR(255) DEFAULT NULL, device_model VARCHAR(255) DEFAULT NULL, device_manufacturer VARCHAR(255) DEFAULT NULL, identification_url LONGTEXT DEFAULT NULL, INDEX IDX_8D93D64921DFC797 (carrier_id), INDEX added_index (added), INDEX identifier_index (identifier), INDEX identification_token_index (identification_token), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE otppin_code (id INT AUTO_INCREMENT NOT NULL, pin VARCHAR(255) NOT NULL, added_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_CB387437B5852DF3 (pin), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_packs (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', country_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', status TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_resub_allowed TINYINT(1) DEFAULT \'0\' NOT NULL, carrier_name VARCHAR(255) NOT NULL, carrier_id INT NOT NULL, tier_name VARCHAR(255) NOT NULL, tier_id INT NOT NULL, tier_price NUMERIC(14, 2) DEFAULT NULL, tier_currency VARCHAR(3) DEFAULT \'\' NOT NULL, display_currency VARCHAR(10) DEFAULT \'\', credits BIGINT NOT NULL, periodicity SMALLINT NOT NULL, custom_renew_period SMALLINT NOT NULL, grace_period SMALLINT UNSIGNED DEFAULT 0 NOT NULL, unlimited_grace_period TINYINT(1) DEFAULT \'0\' NOT NULL, preferred_renewal_start TIME DEFAULT NULL, preferred_renewal_end TIME DEFAULT NULL, welcome_sms_text LONGTEXT DEFAULT NULL, renewal_sms_text LONGTEXT DEFAULT NULL, unsubscribe_sms_text LONGTEXT DEFAULT NULL, buy_strategy_name VARCHAR(255) NOT NULL, buy_strategy_id INT NOT NULL, renew_strategy_name VARCHAR(255) NOT NULL, renew_strategy_id INT NOT NULL, unlimited TINYINT(1) NOT NULL, is_first_subscription_free_multiple TINYINT(1) DEFAULT \'0\', is_first_subscription_free TINYINT(1) DEFAULT \'0\', allow_bonus_credit TINYINT(1) DEFAULT \'0\', allow_bonus_credit_multiple TINYINT(1) DEFAULT \'0\', bonus_credit SMALLINT DEFAULT 0, provider_managed_subscriptions TINYINT(1) DEFAULT \'0\', created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_43C661545E237E06 (name), INDEX IDX_43C66154F92F3E70 (country_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_tasks (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', cron_name VARCHAR(100) NOT NULL, is_running SMALLINT NOT NULL, UNIQUE INDEX UNIQ_BEEF9151E7B69404 (cron_name), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriptions (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', subscription_pack_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', credits BIGINT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, status SMALLINT NOT NULL, renew_date DATETIME DEFAULT NULL, last_renew_alert_date DATETIME DEFAULT NULL, current_stage SMALLINT NOT NULL, redirect_url LONGTEXT DEFAULT NULL, affiliate_token LONGTEXT DEFAULT NULL, error VARCHAR(255) DEFAULT NULL, INDEX IDX_4778A01A76ED395 (user_id), INDEX IDX_4778A016A0C528C (subscription_pack_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE affiliate_log (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', campaign_token VARCHAR(255) NOT NULL, user_msisdn VARCHAR(255) DEFAULT NULL, event SMALLINT NOT NULL, status SMALLINT NOT NULL, added_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, user_ip VARCHAR(255) NOT NULL, device_model VARCHAR(255) DEFAULT NULL, device_manufacturer VARCHAR(255) DEFAULT NULL, device_marketing_name VARCHAR(255) DEFAULT NULL, device_atlas_id VARCHAR(255) DEFAULT NULL, connection_type VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, log LONGTEXT DEFAULT NULL, url LONGTEXT DEFAULT NULL, campaign_params LONGTEXT DEFAULT NULL, subscription_id VARCHAR(255) DEFAULT NULL, INDEX user_msisdn_index (user_msisdn), INDEX added_at_index (added_at), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_running_history (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', last_running_hour DATETIME NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscribed_games (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', game_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', subscription_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', first_download DATETIME NOT NULL, last_download DATETIME NOT NULL, INDEX IDX_C08D0022E48FD905 (game_id), INDEX IDX_C08D00229A1887DC (subscription_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_process_log (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', message LONGTEXT NOT NULL, context LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', level SMALLINT NOT NULL, level_name VARCHAR(50) NOT NULL, extra LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, subscription_id INT NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refunds (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', bf_charge_process_id INT DEFAULT NULL, bf_refund_process_id INT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, error VARCHAR(255) DEFAULT NULL, attempt_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, refund_value INT DEFAULT NULL, INDEX IDX_7EE53AD9A76ED395 (user_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE strategies (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, bf_strategy_id INT DEFAULT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tiers (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, bf_tier_id INT DEFAULT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tiers_values (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', tier_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', strategy_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', carrier_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', value DOUBLE PRECISION NOT NULL, currency VARCHAR(3) NOT NULL, description VARCHAR(500) DEFAULT NULL, INDEX IDX_E2BC1256A354F9DC (tier_id), INDEX IDX_E2BC1256D5CAD932 (strategy_id), INDEX IDX_E2BC125621DFC797 (carrier_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange_rates (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', currency_code VARCHAR(255) NOT NULL, currency_name VARCHAR(255) NOT NULL, exchange_rate NUMERIC(12, 2) NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carriers ADD CONSTRAINT FK_F48AAB55602A942 FOREIGN KEY (default_language_id) REFERENCES languages (uuid)');
        $this->addSql('ALTER TABLE affiliates ADD CONSTRAINT FK_108C6A8FF92F3E70 FOREIGN KEY (country_id) REFERENCES countries (uuid)');
        $this->addSql('ALTER TABLE affiliate_constants ADD CONSTRAINT FK_720BED969F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliates (uuid)');
        $this->addSql('ALTER TABLE affiliate_parameters ADD CONSTRAINT FK_371C00039F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliates (uuid)');
        $this->addSql('ALTER TABLE campaigns ADD CONSTRAINT FK_E37374709F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliates (uuid)');
        $this->addSql('ALTER TABLE campaigns ADD CONSTRAINT FK_E3737470C6C55574 FOREIGN KEY (main_category_id) REFERENCES main_categories (uuid)');
        $this->addSql('ALTER TABLE campaign_carrier ADD CONSTRAINT FK_EF836D30F639F774 FOREIGN KEY (campaign_id) REFERENCES campaigns (uuid)');
        $this->addSql('ALTER TABLE campaign_carrier ADD CONSTRAINT FK_EF836D3021DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
        $this->addSql('ALTER TABLE country_category_priority_overrides ADD CONSTRAINT FK_AA2DAE42C6C55574 FOREIGN KEY (main_category_id) REFERENCES main_categories (uuid)');
        $this->addSql('ALTER TABLE country_category_priority_overrides ADD CONSTRAINT FK_AA2DAE42F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (uuid)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3164DD9267 FOREIGN KEY (developer_id) REFERENCES developers (uuid)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31A354F9DC FOREIGN KEY (tier_id) REFERENCES tiers (uuid)');
        $this->addSql('ALTER TABLE game_builds ADD CONSTRAINT FK_7787C96DE48FD905 FOREIGN KEY (game_id) REFERENCES games (uuid)');
        $this->addSql('ALTER TABLE game_builds_device_displays ADD CONSTRAINT FK_440E3E6EBC85C0AD FOREIGN KEY (game_build_id) REFERENCES game_builds (uuid)');
        $this->addSql('ALTER TABLE game_builds_device_displays ADD CONSTRAINT FK_440E3E6ED6919CF5 FOREIGN KEY (device_display_id) REFERENCES device_displays (uuid)');
        $this->addSql('ALTER TABLE game_images ADD CONSTRAINT FK_9D2A13A2E48FD905 FOREIGN KEY (game_id) REFERENCES games (uuid)');
        $this->addSql('ALTER TABLE subcategories ADD CONSTRAINT FK_6562A1CB3D8E604F FOREIGN KEY (parent) REFERENCES main_categories (uuid)');
        $this->addSql('ALTER TABLE translations ADD CONSTRAINT FK_C6B7DA8782F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (uuid)');
        $this->addSql('ALTER TABLE translations ADD CONSTRAINT FK_C6B7DA8721DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
        $this->addSql('ALTER TABLE uploaded_video ADD CONSTRAINT FK_749275C05DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategories (uuid)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64921DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
        $this->addSql('ALTER TABLE subscription_packs ADD CONSTRAINT FK_43C66154F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (uuid)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01A76ED395 FOREIGN KEY (user_id) REFERENCES user (uuid)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A016A0C528C FOREIGN KEY (subscription_pack_id) REFERENCES subscription_packs (uuid)');
        $this->addSql('ALTER TABLE subscribed_games ADD CONSTRAINT FK_C08D0022E48FD905 FOREIGN KEY (game_id) REFERENCES games (uuid)');
        $this->addSql('ALTER TABLE subscribed_games ADD CONSTRAINT FK_C08D00229A1887DC FOREIGN KEY (subscription_id) REFERENCES subscriptions (uuid)');
        $this->addSql('ALTER TABLE refunds ADD CONSTRAINT FK_7EE53AD9A76ED395 FOREIGN KEY (user_id) REFERENCES user (uuid)');
        $this->addSql('ALTER TABLE tiers_values ADD CONSTRAINT FK_E2BC1256A354F9DC FOREIGN KEY (tier_id) REFERENCES tiers (uuid)');
        $this->addSql('ALTER TABLE tiers_values ADD CONSTRAINT FK_E2BC1256D5CAD932 FOREIGN KEY (strategy_id) REFERENCES strategies (uuid)');
        $this->addSql('ALTER TABLE tiers_values ADD CONSTRAINT FK_E2BC125621DFC797 FOREIGN KEY (carrier_id) REFERENCES carriers (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE carriers DROP FOREIGN KEY FK_F48AAB55602A942');
        $this->addSql('ALTER TABLE translations DROP FOREIGN KEY FK_C6B7DA8782F1BAF4');
        $this->addSql('ALTER TABLE campaign_carrier DROP FOREIGN KEY FK_EF836D3021DFC797');
        $this->addSql('ALTER TABLE translations DROP FOREIGN KEY FK_C6B7DA8721DFC797');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64921DFC797');
        $this->addSql('ALTER TABLE tiers_values DROP FOREIGN KEY FK_E2BC125621DFC797');
        $this->addSql('ALTER TABLE affiliate_constants DROP FOREIGN KEY FK_720BED969F12C49A');
        $this->addSql('ALTER TABLE affiliate_parameters DROP FOREIGN KEY FK_371C00039F12C49A');
        $this->addSql('ALTER TABLE campaigns DROP FOREIGN KEY FK_E37374709F12C49A');
        $this->addSql('ALTER TABLE campaign_carrier DROP FOREIGN KEY FK_EF836D30F639F774');
        $this->addSql('ALTER TABLE affiliates DROP FOREIGN KEY FK_108C6A8FF92F3E70');
        $this->addSql('ALTER TABLE country_category_priority_overrides DROP FOREIGN KEY FK_AA2DAE42F92F3E70');
        $this->addSql('ALTER TABLE subscription_packs DROP FOREIGN KEY FK_43C66154F92F3E70');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3164DD9267');
        $this->addSql('ALTER TABLE game_builds_device_displays DROP FOREIGN KEY FK_440E3E6ED6919CF5');
        $this->addSql('ALTER TABLE game_builds DROP FOREIGN KEY FK_7787C96DE48FD905');
        $this->addSql('ALTER TABLE game_images DROP FOREIGN KEY FK_9D2A13A2E48FD905');
        $this->addSql('ALTER TABLE subscribed_games DROP FOREIGN KEY FK_C08D0022E48FD905');
        $this->addSql('ALTER TABLE game_builds_device_displays DROP FOREIGN KEY FK_440E3E6EBC85C0AD');
        $this->addSql('ALTER TABLE campaigns DROP FOREIGN KEY FK_E3737470C6C55574');
        $this->addSql('ALTER TABLE country_category_priority_overrides DROP FOREIGN KEY FK_AA2DAE42C6C55574');
        $this->addSql('ALTER TABLE subcategories DROP FOREIGN KEY FK_6562A1CB3D8E604F');
        $this->addSql('ALTER TABLE uploaded_video DROP FOREIGN KEY FK_749275C05DC6FE57');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A01A76ED395');
        $this->addSql('ALTER TABLE refunds DROP FOREIGN KEY FK_7EE53AD9A76ED395');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A016A0C528C');
        $this->addSql('ALTER TABLE subscribed_games DROP FOREIGN KEY FK_C08D00229A1887DC');
        $this->addSql('ALTER TABLE tiers_values DROP FOREIGN KEY FK_E2BC1256D5CAD932');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31A354F9DC');
        $this->addSql('ALTER TABLE tiers_values DROP FOREIGN KEY FK_E2BC1256A354F9DC');
        $this->addSql('DROP TABLE languages');
        $this->addSql('DROP TABLE carriers');
        $this->addSql('DROP TABLE admins');
        $this->addSql('DROP TABLE affiliates');
        $this->addSql('DROP TABLE affiliate_constants');
        $this->addSql('DROP TABLE affiliate_parameters');
        $this->addSql('DROP TABLE black_list');
        $this->addSql('DROP TABLE campaigns');
        $this->addSql('DROP TABLE campaign_carrier');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE country_category_priority_overrides');
        $this->addSql('DROP TABLE developers');
        $this->addSql('DROP TABLE device_displays');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE game_builds');
        $this->addSql('DROP TABLE game_builds_device_displays');
        $this->addSql('DROP TABLE game_images');
        $this->addSql('DROP TABLE main_categories');
        $this->addSql('DROP TABLE subcategories');
        $this->addSql('DROP TABLE translations');
        $this->addSql('DROP TABLE uploaded_video');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE otppin_code');
        $this->addSql('DROP TABLE subscription_packs');
        $this->addSql('DROP TABLE cron_tasks');
        $this->addSql('DROP TABLE subscriptions');
        $this->addSql('DROP TABLE affiliate_log');
        $this->addSql('DROP TABLE cron_running_history');
        $this->addSql('DROP TABLE subscribed_games');
        $this->addSql('DROP TABLE subscription_process_log');
        $this->addSql('DROP TABLE refunds');
        $this->addSql('DROP TABLE strategies');
        $this->addSql('DROP TABLE tiers');
        $this->addSql('DROP TABLE tiers_values');
        $this->addSql('DROP TABLE exchange_rates');
    }
}
