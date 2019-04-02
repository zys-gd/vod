<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190402141109 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE languages_audit');
        $this->addSql('DROP TABLE carriers_audit');
        $this->addSql('DROP TABLE admins_audit');
        $this->addSql('DROP TABLE affiliates_audit');
        $this->addSql('DROP TABLE campaigns_audit');
        $this->addSql('DROP TABLE country_category_priority_overrides_audit');
        $this->addSql('DROP TABLE developers_audit');
        $this->addSql('DROP TABLE games_audit');
        $this->addSql('DROP TABLE game_builds_audit');
        $this->addSql('DROP TABLE game_images_audit');
        $this->addSql('DROP TABLE main_categories_audit');
        $this->addSql('DROP TABLE subcategories_audit');
        $this->addSql('DROP TABLE translations_audit');
        $this->addSql('DROP TABLE uploaded_video_audit');
        $this->addSql('DROP TABLE user_audit');
        $this->addSql('DROP TABLE subscription_packs_audit');
        $this->addSql('DROP TABLE subscriptions_audit');
        $this->addSql('DROP TABLE black_list_audit');
        $this->addSql('DROP TABLE refunds_audit');
        $this->addSql('DROP TABLE revisions');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
