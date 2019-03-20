<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190320120349 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Full price", "51c2ea9a-ebd4-11e8-95c4-02bb250f0f22", 1)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Rip the bucket with retry", "51c2ec8c-ebd4-11e8-95c4-02bb250f0f22", 10)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Pakistan Strategy", "51c2ed3f-ebd4-11e8-95c4-02bb250f0f22", 11)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Pakistan Zong", "51c2ed92-ebd4-11e8-95c4-02bb250f0f22", 14)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Mobilink instant response", "51c2edd4-ebd4-11e8-95c4-02bb250f0f22", 13)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Sudan MTN", "51c2ee19-ebd4-11e8-95c4-02bb250f0f22", 16)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Sri Lanka Dialog", "51c2ee5a-ebd4-11e8-95c4-02bb250f0f22", 15)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Sudan Zain", "51c2ee9d-ebd4-11e8-95c4-02bb250f0f22", 21)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Full price Telenor PK", "51c2eeda-ebd4-11e8-95c4-02bb250f0f22", 23)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Malaysia Celcom", "51c2ef19-ebd4-11e8-95c4-02bb250f0f22", 55)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("RTB Dot Smartfren IND", "51c2ef58-ebd4-11e8-95c4-02bb250f0f22", 27)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Brazil Tim with retry", "51c2efa6-ebd4-11e8-95c4-02bb250f0f22", 10)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Kenya Telkom", "51c2efe6-ebd4-11e8-95c4-02bb250f0f22", 32)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Full price HN", "51c2f020-ebd4-11e8-95c4-02bb250f0f22", 34)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Robi Bangladesh", "51c2f05f-ebd4-11e8-95c4-02bb250f0f22", 35)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("RTB Cellcard", "51c2f09c-ebd4-11e8-95c4-02bb250f0f22", 49)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Claro NI Full price", "51c2f0d8-ebd4-11e8-95c4-02bb250f0f22", 38)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Hutch3 fullprice", "51c2f117-ebd4-11e8-95c4-02bb250f0f22", 41)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Full price KW", "51c2f156-ebd4-11e8-95c4-02bb250f0f22", 39)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Ooredoo Oman", "51c2f193-ebd4-11e8-95c4-02bb250f0f22", 47)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("RTB Ooredoo Qatar", "51c2f1d0-ebd4-11e8-95c4-02bb250f0f22", 46)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Ooredoo Myanmar", "51c2f20e-ebd4-11e8-95c4-02bb250f0f22", 52)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Indosat fullprice", "51c2f253-ebd4-11e8-95c4-02bb250f0f22", 45)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("RTB Du UAE", "51c2f293-ebd4-11e8-95c4-02bb250f0f22", 53)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Ooredoo Myanmar Weekly", "60c02dd8-cb71-4994-9601-f613eded42dd", 54)');
        $this->addSql('INSERT INTO strategies (name, uuid, bf_strategy_id) VALUES ("Movistar NI Full price", "db86f815-21fb-4478-9d53-942bd8938316", 57)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DELETE FROM strategies WHERE uuid IN (
                                                              "51c2ea9a-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2ec8c-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2ed3f-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2ed92-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2edd4-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2ee19-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2ee5a-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2ee9d-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2eeda-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2ef19-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2ef58-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2efa6-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2efe6-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f020-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f05f-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f09c-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f0d8-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f117-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f156-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f193-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f1d0-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f20e-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f253-ebd4-11e8-95c4-02bb250f0f22",
                                                              "51c2f293-ebd4-11e8-95c4-02bb250f0f22",
                                                              "60c02dd8-cb71-4994-9601-f613eded42dd",
                                                              "db86f815-21fb-4478-9d53-942bd8938316"
                    )');
    }
}