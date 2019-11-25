<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125111846 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb259d9d8d', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', '77055247101')");
        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb2597a6a5', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', '77055247078')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb259d9d8d'");
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb2597a6a5'");
    }
}
