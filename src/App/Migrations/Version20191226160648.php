<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191226160648 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation, carrier_id) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text_telenor_pk', 'This is a subscription service with auto renewal', '97400b8f-722b-4339-a48e-a51419bbb143')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
