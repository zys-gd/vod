<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190829125941 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('e493273d-3256-4561-8eff-8c27e5ce5b1e', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'messages.error.subscribe_error', 'Sorry, but you got error during subscribe')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
