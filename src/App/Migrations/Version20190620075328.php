<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190620075328 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('4d58f74b-f755-4a9f-a344-a3f512ss2y63', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '7c8385df-8d56-464a-98ff-66c55a7a5741', 'copyright', '©ORIGINDATA 2019 All rights reserved')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a2d58f7das52', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', '7c8385df-8d56-464a-98ff-66c55a7a5741', 'copyright', '©ORIGINDATA 2019 جميع الحقوق محفوظة')");

        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('4d58f9d5-f755-4a9f-a344-a3f512ss2y63', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '99a362ea-72cd-45d5-bbcc-18f16b8451ed', 'copyright', '©ORIGINDATA 2019 All rights reserved')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a2iu7d58f7d8', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', '99a362ea-72cd-45d5-bbcc-18f16b8451ed', 'copyright', '©ORIGINDATA 2019 جميع الحقوق محفوظة')");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM translations WHERE uuid IN ('4d58f74b-f755-4a9f-a344-a3f512ss2y63', '4aos524b-f755-4a9f-a344-a2d58f7das52', '4d58f9d5-f755-4a9f-a344-a3f512ss2y63', '4aos524b-f755-4a9f-a344-a2iu7d58f7d8')");
    }
}
