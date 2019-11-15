<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191115105523 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("
        alter table carriers drop column is_confirmation_click;
        alter table carriers drop column is_lp_off;
        alter table carriers drop column is_confirmation_popup;
        ");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
