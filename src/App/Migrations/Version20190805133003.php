<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190805133003 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('5125f53c-3206-4665-afb3-eee9e5b15661', '5179f466-ebd4-11e8-95c4-02bb250f0f22', '038275ac-dae1-446a-8eac-17a26fb69ea2', 'messages.subscription_popup.confirmation_text', 'Hi %phone%, you will be subscribed to service 100% sport with rate %currency% %price%/%period%');");

        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('b00fe180-17e2-4663-a2ed-2278b707ea27', '5179f466-ebd4-11e8-95c4-02bb250f0f22', '038275ac-dae1-446a-8eac-17a26fb69ea2', 'messages.subscription_popup.confirmation_title', '');");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
