<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190711104143 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("DELETE FROM carriers WHERE billing_carrier_id = 30");
        $this->addSql("DELETE FROM carriers WHERE billing_carrier_id = 31");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
