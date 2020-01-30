<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200128085437 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `carrier_id`, `key`, `translation`) VALUES ('4cfd115b-f117-49a9-ae6c-8292221c6372', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'message.error.invalid_pin', 'You have entered a wrong PIN, please try again')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM translations WHERE uuid = '4cfd115b-f117-49a9-ae6c-8292221c6372'");
    }
}
