<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191126131513 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb259d9d8d', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '77055247101')");
        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb2597a6a5', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '77055247078')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb259d9d8d'");
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb2597a6a5'");
    }
}
