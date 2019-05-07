<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190507142021 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // new keys for categories
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8123', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.15157409-49a4-4823-a7f9-654ac1d7c12f', 'Viral videos')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8121', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.244b3d58-fb86-4ba1-b221-9a0f3916306d', 'Combat')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8dsd', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.4ec02ca3-779f-454a-8280-f59cd9600c2a', 'Golf')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8sdf', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.51d606c2-ab33-44ec-8098-05994b0e75e5', 'Extreme sports')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8sda', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.70b66027-8e25-400b-8e44-cc4dc399f350', 'Athletics')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8drt', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.74558a73-902b-46cc-ab90-48a104585d38', 'Moto sports')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8a5s', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.986e13fa-9dfe-42e6-92f2-2cff6f60b42e', 'Cricket')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8c25', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.ab6ad264-bd64-4fd0-9010-9c8e503a1ad3', 'Football')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl83a5', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.bb215764-2aad-4ca5-83cf-78f841a230e8', 'Qualifier')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8a4s', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.d25f4027-8e25-400b-8e44-cc4dc39fg52f', 'Winter sports')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8a5a', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.dbeb8e19-ea7f-4443-970d-b113ecf819d9', 'Tennis')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl887d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.e14034f0-ac4d-4925-94d4-cbf9d61fae93', 'Others')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl8s51', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.f49a555c-d10e-45c6-a6cb-de09cb59658f', 'Basketball')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl83f6', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'category.games', 'Sport games')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512sl4521', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'buttons.yes', 'Yes')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512s3as52', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'buttons.no', 'No')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
