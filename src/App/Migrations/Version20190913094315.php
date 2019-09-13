<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190913094315 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("
        INSERT IGNORE INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES (
        '3df66c6d-f478-4f58-9964-af76c5e18480',
        'messages.error.already_subscribed_on_another_service',
        'Dear customer, we are sorry but you are already subscribed to some other subscription service. If you want to subscribe to Playwing, please contact us.',
        (SELECT `uuid` FROM `languages` WHERE `uuid` = '5179f17c-ebd4-11e8-95c4-02bb250f0f22'),
        null)
        ");

    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM translations where `uuid` = '3df66c6d-f478-4f58-9964-af76c5e18480'");

    }
}
