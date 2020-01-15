<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200110124707 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE `translations` SET `translation` = '©ORIGINDATA 2019 All rights reserved' WHERE `uuid` = '5cb1d28f-735e-4f20-8cd4-1585289c5c67'");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE `translations` SET `translation` = '©ORIGINDATA 2019 All rights reserved © Red Bull Media House' WHERE `uuid` = '5cb1d28f-735e-4f20-8cd4-1585289c5c67'");
    }
}
