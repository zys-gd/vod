<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106145208 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd17d5d4', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'whoops', 'Niestety w tym momencie nie możemy przetworzyć Twojego żądania. Spróbuj ponownie później')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd7d5d4u', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'resub_not_allowed', 'Nie możesz subskrybować 100% Sportu, ponieważ już nie subskrybujesz. <br> Jeśli chcesz zasubskrybować od nowa, <a class=\"contact-us-link red\" href=\"%contact_us_url%\">skontaktuj się z nami</a>')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793b7d5d4su', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'resub_not_allowed', 'You can not subscribe to 100% Sport because you have already been unsubscribed. <br> If you want to subscribe anew, please <a class=\"contact-us-link red\" href=\"%contact_us_url%\">Contact Us</a>')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd17d5d4'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd7d5d4u'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793b7d5d4su'");
    }
}
