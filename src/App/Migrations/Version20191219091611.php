<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219091611 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // ENGLISH
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'ee6c1535-3d9c-4f37-bf8c-21ab63cb1a54', 
                '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 
                'err_handle.title', 
                'Error');
        ");

        // POLISH
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'a31521b5-fa88-4f97-81bb-7a84aaeb7d19', 
                '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 
                'err_handle.title', 
                'Błąd');
        ");

        // KAZAKH
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'be0777c0-00f0-4c37-a2ec-483026646cf5', 
                '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 
                'err_handle.title', 
                'Қате');
        ");

        // GREEK
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '3354569b-0f78-4c17-86b1-d6a8c8ceab0a', 
                '5179fad1-ebd4-11e8-95c4-02bb250f0f22', 
                'err_handle.title', 
                'Λάθος');
        ");

        // INDONESIAN
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'd36f34b4-3040-4113-9c53-d3ac1b10e457', 
                '5179f466-ebd4-11e8-95c4-02bb250f0f22', 
                'err_handle.title', 
                'Kesalahan');
        ");

        // FRENCH
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '4fe04263-34bf-4b7a-b5da-d16b66bd6741', 
                '5179f269-ebd4-11e8-95c4-02bb250f0f22', 
                'err_handle.title', 
                'Erreur');
        ");

        // ARABIC
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'cab8f64c-3368-475e-b34a-b4e1556e9e72', 
                '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 
                'err_handle.title', 
                'خطأ'
                );
        ");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
