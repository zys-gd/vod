<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200127115729 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IF EXISTS  affiliate_banned_publisher_key ON affiliate_banned_publisher');
        $this->addSql('CREATE UNIQUE INDEX affiliate_banned_publisher_key ON affiliate_banned_publisher (carrier_id, affiliate_id, publisher_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IF EXISTS  affiliate_banned_publisher_key ON affiliate_banned_publisher');
        $this->addSql('CREATE UNIQUE INDEX affiliate_banned_publisher_key ON affiliate_banned_publisher (affiliate_id, publisher_id)');
    }
}
