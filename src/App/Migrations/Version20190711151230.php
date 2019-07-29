<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190711151230 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aa5s23b-d93j-4a9f-a344-a3f512s1dj47', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'messages.error.blacklisted', 'You can not subscribe to 100% Sport.Your phone number has been blacklisted from subscribing.For more info please <a class=\"contact-us-link red\" href=\"%contact-us-url%\">Contact Us</a>')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aak6syh-d93j-4a9f-a344-a3f512s1dj41', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'messages.error.blacklisted', 'لا يمكنك الاشتراك في 100٪ Sport. لقد تم إدراج رقم هاتفك في القائمة السوداء من الاشتراك.')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aa5s23b-d93j-4a9f-a344-a3f5121298hf', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'messages.error.wifi_blacklisted', 'You can not subscribe to 100% Sport.Your phone number has been blacklisted from subscribing.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('4aak6syh-d93j-4a9f-a344-a3f5aj74hfs1', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'messages.error.wifi_blacklisted', 'لا يمكنك الاشتراك في 100٪ Sport. لقد تم إدراج رقم هاتفك في القائمة السوداء من الاشتراك.')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
