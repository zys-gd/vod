<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190527113323 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('8a58s72e-72af-4f7b-b089-bb474064a252', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'message.error.invalid_pin', 'The PIN code is invalid')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('8ed5s8de-72af-4f7b-b089-bb474064a252', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'message.error.pin_request_limit_exceeded', 'The limit for PIN request is reached. Please try again later!')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('0050124b-f755-4a9f-a344-e3f594as58d9', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'message.error.invalid_pin', 'رمز PIN غير صالح')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('0050124b-f755-4a9f-a344-e3f59a5s8749', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'message.error.pin_request_limit_exceeded', 'تم الوصول إلى الحد الأقصى لطلب PIN. الرجاء معاودة المحاولة في وقت لاحق! ')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
