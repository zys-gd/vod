<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190911143542 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO cron_tasks (uuid, cron_name, is_running) VALUES ('2084d3b3-9683-4d22-8c3e-4a0cfe5d8d3d', 'hutchIndonesiaMassRenewCronTask', 0);");

        $this->addSql("INSERT INTO cron_tasks (uuid, cron_name, is_running) VALUES ('6fd7f071-4b21-4a90-8fd9-e70017a23e97', 'zainKSAMassRenewCronTask', 0);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
