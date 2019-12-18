<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191218092058 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // ENGLISH
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'aa4ec1ab-5c9b-424b-a5bb-104b2fe19978', 
                '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.thanks', 
                'Thanks for your interest on 100% sport!');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '086ca9b0-4920-4939-a883-1dd886fec051', 
                '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.promise', 
                'We will contact you soon.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '637a4d8e-f26f-443d-bbaa-44ad59b11f9a', 
                '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.link.text', 
                'HOMEPAGE');
        ");

        // POLISH
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '110fb715-f1e4-4a4e-a843-6aff1d93d01e', 
                '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.thanks', 
                'Dziękujemy za zainteresowanie w 100% sportem!');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '57669c3c-de86-4274-b562-150e31d501b6', 
                '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.promise', 
                'Skontaktujemy się z Tobą wkrótce.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'f33904eb-b277-4300-b277-7f2465274ce1', 
                '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.link.text', 
                'Strona główna');
        ");

        // KAZAKH
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '7cc32b1b-3373-437c-a7a8-2a7d40c9a0d0', 
                '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 
                'contact_us.thanks', 
                '100% спортқа деген қызығушылығыңыз үшін рахмет!');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '4b6cf952-bc65-4110-bea3-6db1f300b576', 
                '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 
                'contact_us.promise', 
                'Жақында сізбен хабарласамыз.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '98f97b49-7679-44b9-989f-1e9da3fbb065', 
                '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 
                'contact_us.link.text', 
                'Басты бет');
        ");

        // GREEK
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '608f9945-4e02-4a34-9c44-85c5be909aa2', 
                '5179fad1-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.thanks', 
                'Ευχαριστώ για το ενδιαφέρον σας για 100% αθλητισμό!');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '9d2b8be1-56d1-4127-a253-ff506b5d280b', 
                '5179fad1-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.promise', 
                'Θα επικοινωνήσουμε μαζί σας σύντομα.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'efe22fcc-bdfe-42f1-8831-bbf16ece2454', 
                '5179fad1-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.link.text', 
                'Αρχική σελίδα');
        ");

        // INDONESIAN
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '1b7e86c4-42c5-4159-8201-e0d6ef0129e6', 
                '5179f466-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.thanks', 
                'Terima kasih atas minat Anda pada olahraga 100%!');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '7652c8aa-3cae-4f38-a614-9385ab52f470', 
                '5179f466-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.promise', 
                'Kami akan segera menghubungi Anda.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'd888385b-3e09-4d71-91fe-e10c691bc6b0', 
                '5179f466-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.link.text', 
                'Beranda');
        ");

        // FRENCH
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '6f1c02d3-e663-4bc2-9d66-b25a9fec7026', 
                '5179f269-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.thanks', 
                'Merci de votre intérêt pour le sport à 100%!');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'c73dbbaf-95f6-483a-bd14-ca96b7a3c578', 
                '5179f269-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.promise', 
                'Nous vous contacterons bientôt.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '87921702-15f4-472b-a807-389eec38cf30', 
                '5179f269-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.link.text', 
                'Page d′accueil');
        ");

        // ARABIC
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '4068bca5-d2a4-49d0-9ea9-7bb4773ce4a2', 
                '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.thanks', 
                'شكرا لاهتمامك في الرياضة 100 ٪!'
                );
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'f6f205d5-9e8f-4248-bdc7-3c977b6a7bba', 
                '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.promise', 
                'سوف نتصل بك قريبا.'
                );
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'b8d6cf6e-52f5-4b6b-93e3-1b4f48595f80', 
                '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.link.text', 
                'الصفحة الرئيسية'
                );
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
