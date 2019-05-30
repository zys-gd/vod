<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190529142720 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('8a3sj82e-72af-4f7b-b089-bb47ay48do40', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'messages.error.wrong_phone_number', 'You entered the wrong phone number. Please enter the correct phone number with international calling code')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('0050124b-f755-4a9f-a344-e331asy64jd9', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'messages.error.wrong_phone_number', 'لقد أدخلت رقم الهاتف الخطأ. يرجى إدخال رقم الهاتف الصحيح مع رمز الاتصال الدولي')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
