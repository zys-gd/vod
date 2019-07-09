<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190621112609 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT IGNORE INTO `translations` (`uuid`, `language_id`, `key`, `translation`) VALUES('83d256b3-30ec-449a-a44d-841ae2396551', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'whoops', 'Whoops. We can\'t proceed with your request. Please try later. If you want to go back to the 100%sport please press <a href=\"%url%\">here</a>.')");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM `translations` WHERE `uuid`='83d256b3-30ec-449a-a44d-841ae2396551'");
    }
}
