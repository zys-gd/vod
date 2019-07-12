<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190710114232 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('6fdf584g-494a-4b09-8b08-b573e7a8860c', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'account.header-text', 'معلومات الاشتراك')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('al98s74g-494a-4b09-8b08-b573e7a8812a', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'account.header-text', 'Subscription info')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('6fdf584g-494a-4b09-8b08-b573e7dk8jf6', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', 'terms.title', '100% Sport <br/> شروط وأحكام')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('al98s74g-29d7-4b09-8b08-b573e7dk8jf1', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'terms.title', '100% Sport <br/> Terms and Conditions')");

        $this->addSql("UPDATE translations SET translation = 'مرحبا بكم في 100 ٪ رياضة! احصل على مقاطع فيديو غير محدودة كل يوم مقابل 2.5 جنيه فقط' WHERE `key` = 'subscription.status.not_subscribe' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");

        $this->addSql("UPDATE translations SET translation = 'شاهد مقاطع فيديو الألعاب الرياضية غير المحدودة مقابل %price% جنيه مصري فقط في اليوم الواحدإمكانية الوصول إلى مئات من مقاطع فيديو الألعاب الرياضية والأخبار كل يوم. ترقب معنا!' WHERE `key` = 'annotation_block.text.2' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22' ");

        $this->addSql("UPDATE translations SET translation = 'يمكنك إلغاء اشتراكك في أي وقت من قسم \"حسابي\" أو عن طريق إرسال UNSUB 100 إلى 5030' WHERE `key` = 'annotation_block.text.3' AND carrier_id = '7c8385df-8d56-464a-98ff-66c55a7a5741' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET translation = 'يمكنك إلغاء اشتراكك في أي وقت من قسم \"\"حسابي\"\" أو عن طريق إرسال UNSUB 100 إلى 6699' WHERE `key` = 'annotation_block.text.3' AND carrier_id = '99a362ea-72cd-45d5-bbcc-18f16b8451ed' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");

        $this->addSql("UPDATE translations SET translation = 'You can cancel your subscription anytime from My Account section or by sending UNSUB 100 to 5030' WHERE `key` = 'annotation_block.text.3' AND carrier_id = '7c8385df-8d56-464a-98ff-66c55a7a5741' AND language_id = '5179f17c-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET translation = 'You can cancel your subscription anytime from My Account section or by sending UNSUB 100 to 6699' WHERE `key` = 'annotation_block.text.3' AND carrier_id = '99a362ea-72cd-45d5-bbcc-18f16b8451ed' AND language_id = '5179f17c-ebd4-11e8-95c4-02bb250f0f22'");

        $this->addSql("UPDATE translations SET translation = 'الأسئلة الشائعة' WHERE `key` = 'menu.footer.faq' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");

        $this->addSql("UPDATE translations SET translation = 'الأفراد الذين يبلغون السن القانونية والقصر الأحرار الذين أعمارهم (أكثر من 14 سنة) فقط يمكن أن ينطبق عليهم والتعاقد على خدمات 100sport. القصر غير الأحرار يمكنهم فقط استخدام خدمات 100sportتحت ترخيص من والديهم أو الأوصياء القانونيين، الذي سيكون في أي حال مسئولين عن أفعال عائلاتهم. 100sportلا يمكنها أن تحل بأي حال من الأحوال محل الرقابة الأبوية للمعلمين القانونيين، لذلك سوف يعتبر صاحب خط الهاتف الجوال مسئولا عن جميع الآثار.' WHERE `key` = 'terms.block_6.text.1' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");

        $this->addSql("UPDATE translations SET translation = 'أنت مشترك sport 100% %price% %currency%/%period%.استمتع بمحتوى غير محدود!' WHERE `key` = 'subscription.status.subscribed' AND carrier_id = '99a362ea-72cd-45d5-bbcc-18f16b8451ed' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET translation = 'أنت مشترك sport 100% %price% %currency%/%period%.استمتع بمحتوى غير محدود!' WHERE `key` = 'subscription.status.subscribed' AND carrier_id = '7c8385df-8d56-464a-98ff-66c55a7a5741' AND language_id = '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
