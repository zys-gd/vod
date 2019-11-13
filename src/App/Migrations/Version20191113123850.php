<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191113123850 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES
('bffb7ed4-ba33-4062-844a-ba83bc7bae69', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'buttons.subscribe', 'JOIN FOR FREE!');");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES
('519f7698-1ad5-44de-9eb0-377ecae5e68c', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'offer.landing.clickbait', '1ST DAY <red>FREE <br/><large>LIMITED OFFER!</large></red>'");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES
('1117b1d0-4fd3-4226-a343-f1d4e3318324', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'offer.landing.clickbait_small', 'For only  %price% %currency% per %period% - <b>1st %period% FREE!</b>';");
        $this->addSql("UPDATE `translations` SET `translation` = 'Watch unlimited videos of your favorite sports and teams!' WHERE `translations`.`uuid` = '4550c254-0ebf-43d8-9730-d793bd5939ee'");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
