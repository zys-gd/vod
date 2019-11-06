<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106141642 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd1dak8s', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'buttons.download', 'Ściągnij')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd1ak8su', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'player.related_videos', 'Powiązane wideo')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793b1ak8sfu', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'player.close', 'Zamknij')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bak8s8fu', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'game.other_games', 'Inne gry')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd1dak8s'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd1ak8su'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793b1ak8sfu'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bak8s8fu'");
    }
}
