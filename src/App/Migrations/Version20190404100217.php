<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404100217 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('052dc244-38d2-460f-8c2e-d03b6a45ffda', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', NULL, 'messages.error.already_subscribed', 'You already have an existing subscription')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('4f4e41ce-d182-4b7b-8103-2cb636bccdd7', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', NULL, 'messages.error.postpaid_restricted', 'This offer is for prepaid customers only')");
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES('ab5b1a23-f26a-4c87-9e55-54278bf70eed', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5142f9ec-ebd4-11e8-95c4-02bb250f0f22', 'messages.error.postpaid_restricted', 'This offer is for Jazz prepaid customers only')");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '052dc244-38d2-460f-8c2e-d03b6a45ffda'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = '4f4e41ce-d182-4b7b-8103-2cb636bccdd7'");
        $this->addSql("DELETE FROM `translations` WHERE `translations`.`uuid` = 'ab5b1a23-f26a-4c87-9e55-54278bf70eed'");
    }
}
