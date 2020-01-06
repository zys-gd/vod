<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219122040 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `translations` (`uuid`, `language_id`, `carrier_id`, `key`, `translation`) VALUES
('05818906-e5c9-4785-aec8-1b21f94eb0c0', '5179f269-ebd4-11e8-95c4-02bb250f0f22', NULL, 'messages.action.unsubscribe.button.close', 'Fermer'),
('31d7904e-ed8b-4130-b1b4-24e8f48b4f48', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', NULL, 'messages.action.unsubscribe.button.close', 'Жабық'),
('766f0c94-67b5-462a-8ac0-d995a05cbbdb', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', NULL, 'messages.action.unsubscribe.button.close', 'Zamknij'),
('a7bc8022-899d-4517-88e2-9fd9f2f6d8b2', '5179f466-ebd4-11e8-95c4-02bb250f0f22', NULL, 'messages.action.unsubscribe.button.close', 'Dekat'),
('d01d68b4-0f82-4195-928f-5586cb084617', '5179ee29-ebd4-11e8-95c4-02bb250f0f22', NULL, 'messages.action.unsubscribe.button.close', 'أغلق'),
('d421012b-461d-4244-a375-eed000c733f7', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', NULL, 'messages.action.unsubscribe.button.close', 'Close');");


    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
