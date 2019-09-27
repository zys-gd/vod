<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190927132542 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("
UPDATE translations SET `translation` = 'Dear customer, we are sorry but you are already subscribed to some other subscription service. If you want to subscribe to 100sport.tv, please contact us.' WHERE `translations`.`uuid` = '3df66c6d-f478-4f58-9964-af76c5e18480'        
        ");

    }

    public function down(Schema $schema) : void
    {
        $this->addSql("
        UPDATE translations SET `translation` = 'Dear customer, we are sorry but you are already subscribed to some other subscription service. If you want to subscribe to Playwing, please contact us.' WHERE `translations`.`uuid` = '3df66c6d-f478-4f58-9964-af76c5e18480'");

    }
}
