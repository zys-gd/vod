<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190528133627 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('8a3sj82e-72af-4f7b-b089-bb474064a252', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'messages.info.remind_credentials', 'SMS with the details was sent successfully to your device')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('0050124b-f755-4a9f-a344-e33asi8e58d9', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'messages.info.remind_credentials', 'تم إرسال الرسائل القصيرة مع التفاصيل بنجاح إلى جهازك')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
