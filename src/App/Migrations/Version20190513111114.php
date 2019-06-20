<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190513111114 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aa5s23b-f755-4a9f-a344-a3f512sl8123', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'period.day', 'day')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4aas-a344-a3f512sl8a53', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'period.week', 'week')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f51s58a653', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'period.day', 'يوم')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('as85a44b-f755-4a9f-a344-a3f512sl8123', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'period.week', 'أسبوع')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
