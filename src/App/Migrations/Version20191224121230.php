<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191224121230 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO cron_tasks (uuid, cron_name, is_running) VALUES ('6fd7f071-4b21-4a90-8fd9-e70017dhf8d6', 'beelineKZDotMassRemindCronTask', 0);");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM cron_tasks WHERE uuid = '6fd7f071-4b21-4a90-8fd9-e70017dhf8d6'");
    }
}
