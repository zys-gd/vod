<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * For testing default lang switcher
 * For JazzPakistan add Arabic title
 */
final class Version20190409085753 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("
            INSERT IGNORE INTO translations (`uuid`,`key`,`translation`,`language_id`,`carrier_id`) VALUES ('c6ded84f-99c0-4186-bbf1-d8a0835bb4d0','subscription.status.not_subscribe','مرحبا بكم في 100 ٪ رياضة!
    <small> احصل على مقاطع فيديو غير محدودة لكل٪ period٪ مقابل٪ price فقط٪ /٪ currency٪ (عرض محدود: 1st٪ period٪ FREE) </ small>',(SELECT `uuid` FROM `languages` WHERE `uuid` = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'),(SELECT `uuid` FROM `carriers` WHERE `uuid` = '5142f9ec-ebd4-11e8-95c4-02bb250f0f22'));
        ");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("
            DELETE FROM translations where `uuid` = 'c6ded84f-99c0-4186-bbf1-d8a0835bb4d0';
        ");
    }
}
