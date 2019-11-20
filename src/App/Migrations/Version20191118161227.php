<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191118161227 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE translations SET translation = 'Serwis Wideo stanowi świadczenie dodatkowe w ramach usługi o podwyższonej opłacie i jest co tydzień usługą subskrypcyjną, która umożliwia pozwalając na wideo premium, w których nie ma żadnych zakupów ani reklam (“100% Sport”).' WHERE `key` = 'disclaimer.landing.3g' AND language_id = '5179fd32-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET translation = 'The Videos service constitutes an additional benefit to the telecommunications service provided within the value added service and is a weekly subscription service allowing for premium videos that don’t have any in-app purchases or ads on it (the “100% Sport”).' WHERE `key` = 'disclaimer.landing.3g' AND language_id = '5179f17c-ebd4-11e8-95c4-02bb250f0f22'");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
