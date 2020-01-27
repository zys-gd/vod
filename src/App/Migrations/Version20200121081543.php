<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use ExtrasBundle\Utils\UuidGenerator;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200121081543 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.user.title', 
                'Benutzer');
        ");

        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 
                'about_us.title', 
                'Über uns');
        ");

        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)',
                '3de06e41-f889-4f95-aac3-ebd31d9d7d9a');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)',
                '3de06e41-f889-4f95-aac3-ebd31d97d9a4');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)',
                '3de06e41-f889-4f95-aac3-ebd317d9adg4');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)',
                '3de06e41-f889-4f95-aac3-ebd31d7d9ag4');
        ");

        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)',
                '3de06e41-f889-4f95-aac3-ebd31d9d7d9a');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)',
                '3de06e41-f889-4f95-aac3-ebd31d97d9a4');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)',
                '3de06e41-f889-4f95-aac3-ebd317d9adg4');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)',
                '3de06e41-f889-4f95-aac3-ebd31d7d9ag4');
        ");

        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '" . UuidGenerator::generate() . "',
                '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 
                'contact_us.text.3', 
                'Helpline: 08000000557 (EUR 0,00/Min.)');
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
