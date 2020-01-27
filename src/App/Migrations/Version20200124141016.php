<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200124141016 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO cron_tasks (uuid, cron_name, is_running) VALUES ('1527aca0-3f67-460f-a68f-654f7879e1b8', 'delayedConfirmationsCronTask', 0);");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM cron_tasks WHERE uuid = '1527aca0-3f67-460f-a68f-654f7879e1b8'");
    }
}
