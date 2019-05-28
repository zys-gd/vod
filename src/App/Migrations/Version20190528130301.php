<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190528130301 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("
INSERT IGNORE INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('7b3115d1-04db-40a3-838c-2e01d5b34872','messages.error.subscription_restricted','Subscription Restricted placeholder AR',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''))
INSERT IGNORE INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('42a13111-2caa-4075-9226-13c4b9d2c412','messages.error.subscription_restricted','Subscription Restricted placeholder EN',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179f17c-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = ''))
");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("
        DELETE FROM translations where `uuid` = '7b3115d1-04db-40a3-838c-2e01d5b34872';
        DELETE FROM translations where `uuid` = '42a13111-2caa-4075-9226-13c4b9d2c412'
        ");

    }
}
