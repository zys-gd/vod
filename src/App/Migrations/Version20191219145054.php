<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219145054 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT IGNORE INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('b9d7ce60-dbf4-4097-9659-90989ba4e4ab', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', null, 'terms.block_4.text.2', '')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
