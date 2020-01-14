<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200114140331 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // O2 DE
        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dc0a9a', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', 'menu.footer.imprint', 'Impressum')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0d0a9aa', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', 'menu.footer.cancel', 'Kündigung')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e00a9aba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', 'menu.footer.withdrawal', 'Widerrufsformular')");
        //$this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', '', '')");

        // O2 EN
        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0a9a1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', 'menu.footer.imprint', 'Impressum')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd00a9ac1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', 'menu.footer.cancel', 'Cancel')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0a9acc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', 'menu.footer.withdrawal', 'Widerrufsformular')");
        //$this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd317d9adg4', '', '')");

//        // Vodafone DE
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '', '')");
//
//        // Vodafone EN
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '', '')");
//
//        // Telekom DE
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '', '')");
//
//        // Telekom EN
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '', '')");
//
//        // Debitel DE
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '', '')");
//
//        // Debitel EN
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '', '')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dc0a9a'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0d0a9aa'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e00a9aba'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0a9a1ba'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd00a9ac1ba'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0a9acc1ba'");
//        $this->addSql("DELETE FROM translations WHERE uuid = ''");
//        $this->addSql("DELETE FROM translations WHERE uuid = ''");
//        $this->addSql("DELETE FROM translations WHERE uuid = ''");
//        $this->addSql("DELETE FROM translations WHERE uuid = ''");
    }
}
