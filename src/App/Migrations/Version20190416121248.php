<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416121248 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
            ALTER TABLE subscription_packs ADD carrier_uuid CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)' AFTER carrier_id;
            ALTER TABLE subscription_packs ADD CONSTRAINT FK_43C6615483122C21 FOREIGN KEY (carrier_uuid) REFERENCES carriers (uuid);
            ALTER TABLE subscription_packs ADD INDEX IDX_43C6615483122C21(carrier_uuid);
        ");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE subscription_packs DROP FOREIGN KEY FK_43C6615483122C21;");
        $this->addSql("ALTER TABLE subscription_packs DROP INDEX IDX_43C6615483122C21;");
        $this->addSql("ALTER TABLE subscription_packs DROP COLUMN carrier_uuid;");
    }
}
