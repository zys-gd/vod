<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190328094630 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('2f1c4c44-6896-4582-9bc3-7aa1ad91c6c7', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'terms.block_5.text.1', 'The total cost of this subscription service will be advertised in each page where you can sign up 100% sport services. Total or partial amounts of the contracted service will be charged each week or day as specified by your mobile operator. However, the maximum price for subscription will be %price% %currency% per %period% (limited offer: 1st %period% FREE)')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('3fe5a2b7-6229-4fcc-8cae-070826f70b03', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'offer.account', 'Watch unlimited videos of your favorite sports and teams!<br/>Only %price% %currency% per %period% (limited offer: 1st %period% FREE)')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('4550c254-0ebf-43d8-9730-d793bd5939ee', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'offer.landing', 'Watch unlimited videos of your favorite sports and teams!<br/>Only %price% %currency% per %period% (limited offer: 1st %period% FREE)')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('46957854-477b-4593-af80-4e6b4860cdcf', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'terms.block_3.text.1', 'This subscription service allows you to watch unlimited sports videos every %period% your phone from 100% sport catalogue for %price% %currency% (limited offer: 1st %period% FREE)(+ WAP connection charges). As long as you are subscribed to 100% sport, you will be able to watch unlimited content every %period%.')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('57429980-a57a-44a4-8803-fe4d8478d224', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'wifi.offer', '%price% %currency% per %period% (limited offer: 1st %period% FREE)')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('6fa6d69a-494a-4b09-8b08-b573e7a8860c', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'account.subscription.status.not_subscribe', 'Get unlimited videos each %period% for only %price%/%currency% (limited offer: 1st %period% FREE)')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('705c9fa1-cc1f-41c0-b55f-ef7e06b521b2', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'subscription.status.not_subscribe', 'Welcome to 100% Sport!\n<small>Get unlimited videos each %period% for only %price%/%currency% (limited offer: 1st %period% FREE)</small>')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('8f62fb49-3e16-4de1-8c66-a6750b9458d5', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.2', 'Watch unlimited sports videos for only %price% %currency% per %period% (limited offer: 1st %period% FREE). Access to hundreds of sport videos and news every day. Stay tuned!')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('abd95ef9-7123-431b-997f-deaeeba15f26', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'terms.block_3.text.1', 'This subscription service allows you to watch unlimited sports videos every %period% your phone from 100% sport catalogue for %price% %currency% (limited offer: 1st %period% FREE)(+ WAP connection charges). As long as you are subscribed to 100% sport, you will be able to watch unlimited content every %period%.')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('b82e39ac-67c3-4ace-b07e-284c2fe07c5d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'offer.annotation_block', 'Watch unlimited sport videos for only %price% %currency% per %period% (limited offer: 1st %period% FREE)')");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '2f1c4c44-6896-4582-9bc3-7aa1ad91c6c7'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '3fe5a2b7-6229-4fcc-8cae-070826f70b03'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '4550c254-0ebf-43d8-9730-d793bd5939ee'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '46957854-477b-4593-af80-4e6b4860cdcf'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '57429980-a57a-44a4-8803-fe4d8478d224'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '6fa6d69a-494a-4b09-8b08-b573e7a8860c'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '705c9fa1-cc1f-41c0-b55f-ef7e06b521b2'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '8f62fb49-3e16-4de1-8c66-a6750b9458d5'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = 'abd95ef9-7123-431b-997f-deaeeba15f26'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = 'b82e39ac-67c3-4ace-b07e-284c2fe07c5d'");
    }
}
