<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190925080748 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("DELETE FROM translations WHERE `key` = 'subscription.status.subscribed' AND carrier_id = '99a362ea-72cd-45d5-bbcc-18f16b8451ed' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("DELETE FROM translations WHERE `key` = 'buttons.unsubscribe' AND carrier_id = '99a362ea-72cd-45d5-bbcc-18f16b8451ed' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET carrier_id = NULL WHERE `key` = 'subscription.status.subscribed' AND carrier_id = '7c8385df-8d56-464a-98ff-66c55a7a5741' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET carrier_id = NULL WHERE `key` = 'buttons.unsubscribe' AND carrier_id = '7c8385df-8d56-464a-98ff-66c55a7a5741' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, `translation`) VALUES ('oa8da666-0eb7-4357-b1dc-f9061445478d', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'buttons.join.now', 'انضم الآن')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, `translation`) VALUES ('1adj83jd-0eb7-4357-b1dc-f90614454722', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.3', 'يمكنك إلغاء اشتراكك في أي وقت من قسم حسابي')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, `translation`) VALUES ('810ea666-0eb7-4357-bfdc-f906144e2489', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', '99a362ea-72cd-45d5-bbcc-18f16b8451ed', 'subscription.status.subscribed', 'أنت مشترك sport 100% %price% %currency%/%period%.استمتع بمحتوى غير محدود!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, `translation`) VALUES ('3a52s427-3f40-41a2-99d9-6800s52db39f', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', '99a362ea-72cd-45d5-bbcc-18f16b8451ed', 'buttons.unsubscribe', 'إلغاء الاشتراك من %100 sport')");
        $this->addSql("UPDATE translations SET carrier_id = '7c8385df-8d56-464a-98ff-66c55a7a5741' WHERE `key` = 'subscription.status.subscribed' AND carrier_id IS NULL AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET carrier_id = '7c8385df-8d56-464a-98ff-66c55a7a5741' WHERE `key` = 'buttons.unsubscribe' AND carrier_id IS NULL AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'oa8da666-0eb7-4357-b1dc-f9061445478d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '1adj83jd-0eb7-4357-b1dc-f90614454722'");
    }
}