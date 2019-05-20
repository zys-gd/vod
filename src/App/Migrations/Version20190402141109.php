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

        $this->addSql('DROP TABLE IF EXISTS languages_audit');
        $this->addSql('DROP TABLE IF EXISTS carriers_audit');
        $this->addSql('DROP TABLE IF EXISTS admins_audit');
        $this->addSql('DROP TABLE IF EXISTS affiliates_audit');
        $this->addSql('DROP TABLE IF EXISTS campaigns_audit');
        $this->addSql('DROP TABLE IF EXISTS country_category_priority_overrides_audit');
        $this->addSql('DROP TABLE IF EXISTS developers_audit');
        $this->addSql('DROP TABLE IF EXISTS games_audit');
        $this->addSql('DROP TABLE IF EXISTS game_builds_audit');
        $this->addSql('DROP TABLE IF EXISTS game_images_audit');
        $this->addSql('DROP TABLE IF EXISTS main_categories_audit');
        $this->addSql('DROP TABLE IF EXISTS subcategories_audit');
        $this->addSql('DROP TABLE IF EXISTS translations_audit');
        $this->addSql('DROP TABLE IF EXISTS uploaded_video_audit');
        $this->addSql('DROP TABLE IF EXISTS user_audit');
        $this->addSql('DROP TABLE IF EXISTS subscription_packs_audit');
        $this->addSql('DROP TABLE IF EXISTS subscriptions_audit');
        $this->addSql('DROP TABLE IF EXISTS black_list_audit');
        $this->addSql('DROP TABLE IF EXISTS refunds_audit');
        $this->addSql('DROP TABLE IF EXISTS revisions');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
