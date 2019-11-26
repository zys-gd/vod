<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191126131514 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO languages (uuid, name, code) VALUES ('5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'Kazakh', 'kk')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM languages WHERE uuid = '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4'");
    }
}
