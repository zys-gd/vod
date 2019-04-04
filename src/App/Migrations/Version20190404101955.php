<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404101955 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('4681eae4-cb8a-4219-b41d-92e68d5e8d14', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '97400b8f-722b-4339-a48e-a51419bbb143', 'faq.a.1', 'You can terminate your subscription whenever you want by entering in your personal menu (icon at top right of the display) and clicking on Leave 100% sport link or by sending SMS with keyword DJ STOP 100 to 5716')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('b982739b-3af5-441c-8c13-2e43a9de3cc8', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '97400b8f-722b-4339-a48e-a51419bbb143', 'annotation_block.text.3', 'You can cancel your subscription anytime from My Account section or by sending DJ STOP 100 to 5716')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '4681eae4-cb8a-4219-b41d-92e68d5e8d14'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = 'b982739b-3af5-441c-8c13-2e43a9de3cc8'");
    }
}
