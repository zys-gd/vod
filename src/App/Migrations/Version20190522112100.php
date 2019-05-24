<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190522112100 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('810aos5s-0eb7-4357-bfdc-f906144e2489', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', '99a362ea-72cd-45d5-bbcc-18f16b8451ed', 'messages.error.already_subscribed', '<a href=\"#\" id=\"send-reminder\">هنا</a> لديك بالفعل اشتراك نشط. لتلقي تفاصيل الاشتراك انقر فوق')");

        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('8a5sde56-0eb7-4357-bfdc-f9061d58s489', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', '7c8385df-8d56-464a-98ff-66c55a7a5741', 'messages.error.already_subscribed', '<a href=\"#\" id=\"send-reminder\">هنا</a> لديك بالفعل اشتراك نشط. لتلقي تفاصيل الاشتراك انقر فوق')");

        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aas3d74c-9df9-4fe7-9508-fa54da7as58d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '99a362ea-72cd-45d5-bbcc-18f16b8451ed', 'messages.error.already_subscribed', 'You already have an active subscription. To receive the details of subscription click <a href=\"#\" id=\"send-reminder\">here</a>')");

        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('8ea5dd5e-72af-4f7b-b089-bas58ds7a252', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '7c8385df-8d56-464a-98ff-66c55a7a5741', 'messages.error.already_subscribed', 'You already have an active subscription. To receive the details of subscription click <a href=\"#\" id=\"send-reminder\">here</a>')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
