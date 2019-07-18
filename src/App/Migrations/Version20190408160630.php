<?php declare(strict_types=1);

namespace DoctrineMigrations;

use ExtrasBundle\Utils\UuidGenerator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190408160630 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO video_partners (uuid, `name`) VALUES ('7ed37c75-da2d-4842-89ff-e0af8f8ddfea' ,'Red Bull')");
        $this->addSql("INSERT INTO video_partners (uuid, `name`) VALUES ('afed2496-38f0-4a02-ab25-a6bc18aeefed', 'SNTV')");
    }

    public function down(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM video_partners WHERE uuid = '7ed37c75-da2d-4842-89ff-e0af8f8ddfea'");
        $this->addSql("DELETE FROM video_partners WHERE uuid = 'afed2496-38f0-4a02-ab25-a6bc18aeefed'");
    }
}
