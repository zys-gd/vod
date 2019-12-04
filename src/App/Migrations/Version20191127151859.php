<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191127151859 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj92j9', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'wifi.warning', 'Ой! Пайдаланушыңызды анықтай алмадық. Еліңіз бен операторыңызды таңдаңыз да, ұялы телефон нөміріңізді теріңіз. Сіз СМС арқылы ДСН-кодын аласыз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj2j98', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'wifi.offer', 'Күніне 50 теңге')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25d2j9d8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'buttons.subscribe', 'Жазылу')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb252j9fd8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'wifi.terms_confirmation', 'Мен <a href=\"%terms_url%\">шарттары мен ережелерін</a> оқып, келісемін')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb22j99fd8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'buttons.wifi.change_number', 'Нөмірді өзгерту')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb2j9j9fd8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'buttons.wifi.resend_pin', 'ПИН-коды бар SMS-ті қайта жіберіңіз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02b2j9dj9fd8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'wifi.pin_notif', 'Қош келдіңіз: %wifi_phone%! SMS арқылы алынған PIN кодты теріп, жазылуды басыңыз!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-022j95dj9fd8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'buttons.unsubscribe', '100% sports қызметінен шығу')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02j925dj9fd8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.related_videos', 'Ұқсас бейнелер')");

        $this->addSql("UPDATE translations SET translation = 'Сайтымызға кіруіңізді бастапқы бетте жалғастырыңыз' WHERE uuid = '51d899d0-ebd4-11e8-95c4-08d9d5dj2k2i'");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj92j9'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj2j98'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25d2j9d8'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb252j9fd8'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb22j99fd8'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb2j9j9fd8'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02b2j9dj9fd8'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-022j95dj9fd8'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02j925dj9fd8'");
    }
}
