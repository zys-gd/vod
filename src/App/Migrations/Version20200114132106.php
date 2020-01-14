<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200114132106 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dck9d8', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'navbar.menu.terms', 'AGB')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dk9d8a', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'menu.hamburger.terms', 'AGB')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dkf9f8', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'menu.footer.terms', 'AGB')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0k9d8ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'menu.hamburger.contact_us', 'Kontakt')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0k9d8d7', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'menu.footer.contact_us', 'Kontakt')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dc1j17', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.1', 'Die Kosten werden deiner Handy-Rechnung hinzugefügt')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0d1j17a', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.2', 'Schau unbegrenzt Sportvideos für nur %price% %currency% pro Woche inkl. Mehrwertsteuer an')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e01j17ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.3', 'Du kannst dein Abo im Bereich Konto jederzeit kündigen')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e1j171ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.4', 'Für das Herunterladen von Daten fallen Gebühren gemäß Vertrag oder Paket an')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dc101s', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'wifi.button', 'ABONNIEREN!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0101sba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.subscribe', 'ABONNIEREN!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0ddf9da', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'offer.landing', 'Abonnement: %price% %currency% inkl. MwSt. / %period% (Beinhaltet unbegrenzt viele Videos)')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dck9d8'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dk9d8a'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0k9d8ba'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dkf9f8'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0k9d8d7'");
        $this->addSql("DELETE FROM translations WHERE uuid = '5179fa73-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("DELETE FROM translations WHERE uuid = '5179fa73-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("DELETE FROM translations WHERE uuid = '5179fa73-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("DELETE FROM translations WHERE uuid = '5179fa73-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dc101s'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0101sba'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0ddf9da'");
       // $this->addSql("DELETE FROM translations WHERE uuid = ''");
    }
}
