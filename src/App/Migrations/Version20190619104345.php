<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190619104345 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT IGNORE INTO `translations` (`uuid`, `language_id`, `key`, `translation`) VALUES('d088a993-7dc3-4d6a-b926-e2dd0683b511', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'not-found-error', 'Sorry, this URL doesn\'t exist or is no longer available. If you want to go back to the 100%sport please press <a href=\"%url%\">here</a>.')");
        $this->addSql("INSERT IGNORE INTO `translations` (`uuid`, `language_id`, `key`, `translation`) VALUES('2c0f2ee3-698a-436b-a0ab-adfc05dda8ae', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'internal-server-error', 'Sorry, the service is unavailable now. We are doing our best to resolve it!  If you want to go back to 100%sport please click <a href=\"%url%\">here</a>.')");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM `translations` WHERE `uuid`='d088a993-7dc3-4d6a-b926-e2dd0683b511'");
        $this->addSql("DELETE FROM `translations` WHERE `uuid`='2c0f2ee3-698a-436b-a0ab-adfc05dda8ae'");
    }
}
