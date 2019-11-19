<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191119110947 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // general kazakh
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f8d6', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '', '')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f8d6', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '', '')");

        // beeline kazakh
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f8d6', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'buttons.subscribe', 'ТӨЛЕУСІЗ ҚОСЫЛУ')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f8d6', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'buttons.confirm', 'ЖАЗЫЛЫСТЫ РАСТАУ')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f8d6', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");

        // beeline english
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f8d6', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'buttons.subscribe', 'JOIN FOR FREE')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f8d6', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'buttons.confirm', 'CONFIRM SUBSCRIPTION')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f8d6', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
