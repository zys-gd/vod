<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191112111402 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('0cabad02-5273-48aa-9ef7-913d16aae686','wrong_cookie_page.backlink_title','ici',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179f269-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''));
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('195f9634-e56f-4794-b4c9-f59550d1bfde','wrong_cookie_page.text','عفوًا ، يبدو أن قبول ملفات تعريف الارتباط في وضع إيقاف في إعدادات المتصفح.يرجى السماح لها والاستمتاع بخدمتنا: الإعدادات> الخصوصية> السماح بملفات تعريف الارتباط. للعودة ، يرجى النقر على %backLink%',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''));
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('1a170783-91b5-43d8-9984-0be70bb73bc7','wrong_cookie_page.backlink_title','هنا',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''));
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('7ea53328-d3f2-44fa-8a29-7babdd27052f','wrong_cookie_page.text','Whoops, apparently cookies acceptance is turned off in your browser settings. Please, allow it and enjoy our service: Settings > Site settings > Allow cookies. To go back, please click %backLink%.',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179f17c-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''));
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('8614688e-8c8c-45a4-b600-4b9aa442d14a','wrong_cookie_page.backlink_title','here',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179f17c-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''));
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('a8d1a389-52f8-443a-bfa0-213583e94b41','wrong_cookie_page.text','Oups, apparemment, l''acceptation des cookies est désactivée dans les paramètres de votre navigateur.Autorisez-le et profitez de notre service: Paramètres> Confidentialité> Autoriser les cookies. Pour revenir en arrière, veuillez cliquer %backLink%',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179f269-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''));
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('af60cc81-8565-402f-b6d6-d6c7be1dc3ed','wrong_cookie_page.backlink_title','sini',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179f810-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''));
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('f9d6a7d9-7f62-4742-a0af-1cda32e096c2','wrong_cookie_page.text','Ups, tampaknya penerimaan cookie dimatikan dalam pengaturan browser Anda.Tolong, izinkan, dan nikmati layanan kami: Pengaturan> Privasi> Izinkan cookie. Untuk kembali, silakan klik di %backLink%',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179f810-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''))
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('375116b1-6d25-44cb-acb4-6f5df6d3910d','wrong_cookie_page.text','Ups, najwyraźniej akceptacja plików cookie jest wyłączona w ustawieniach przeglądarki.Pozwól na to i skorzystaj z naszej usługi: Ustawienia> Ustawienia strony> Zezwalaj na pliki cookie. Aby wrócić, kliknij %backLink%.',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179fd32-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''));
            INSERT INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('88842557-c5b4-4fd1-85fb-7a9804cf37c3','wrong_cookie_page.backlink_title','tutaj',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179fd32-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''))
        ");

    }

    public function down(Schema $schema) : void
    {
        $this->addSql("
            DELETE FROM translations where `uuid` = '0cabad02-5273-48aa-9ef7-913d16aae686';
            DELETE FROM translations where `uuid` = '195f9634-e56f-4794-b4c9-f59550d1bfde';
            DELETE FROM translations where `uuid` = '1a170783-91b5-43d8-9984-0be70bb73bc7';
            DELETE FROM translations where `uuid` = '7ea53328-d3f2-44fa-8a29-7babdd27052f';
            DELETE FROM translations where `uuid` = '8614688e-8c8c-45a4-b600-4b9aa442d14a';
            DELETE FROM translations where `uuid` = 'a8d1a389-52f8-443a-bfa0-213583e94b41';
            DELETE FROM translations where `uuid` = 'af60cc81-8565-402f-b6d6-d6c7be1dc3ed';
            DELETE FROM translations where `uuid` = 'f9d6a7d9-7f62-4742-a0af-1cda32e096c2';
            DELETE FROM translations where `uuid` = '375116b1-6d25-44cb-acb4-6f5df6d3910d';
            DELETE FROM translations where `uuid` = '88842557-c5b4-4fd1-85fb-7a9804cf37c3';
        ");
    }
}
