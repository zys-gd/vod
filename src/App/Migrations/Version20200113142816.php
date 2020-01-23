<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200113142816 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb259dkd9d', '3de06e41-f889-4f95-aac3-ebd31d9d7d9a', '491711047493')");
        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb259kd9d5', '3de06e41-f889-4f95-aac3-ebd31d97d9a4', '491711049392')");
        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb25kd9da5', '3de06e41-f889-4f95-aac3-ebd317d9adg4', '4917627819270')");
        $this->addSql("INSERT INTO test_user (uuid, carrier_id, user_identifier) VALUES ('d1dj8f7d-ebd4-12e8-91c4-02bb2kd9d6a5', '3de06e41-f889-4f95-aac3-ebd31d7d9ag4', '491711044938')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb259dkd9d'");
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb259kd9d5'");
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb25kd9da5'");
        $this->addSql("DELETE FROM test_user WHERE uuid = 'd1dj8f7d-ebd4-12e8-91c4-02bb2kd9d6a5'");
    }
}
