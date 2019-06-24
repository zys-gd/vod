<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190619114418 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a3f512ss2y63', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'messages.info.not_enough_credit', 'Sorry, but you do not have sufficient balance to subscribe, please recharge your balance and try again')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aos524b-f755-4a9f-a344-a2iu7rs3as52', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'messages.info.not_enough_credit', 'عذرا ، لكن ليس لديك رصيد كاف للاشتراك ، يرجى إعادة شحن رصيدك وإعادة المحاولة')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM translations WHERE uuid in ('4aos524b-f755-4a9f-a344-a3f512ss2y63', '4aos524b-f755-4a9f-a344-a2iu7rs3as52')");
    }
}
