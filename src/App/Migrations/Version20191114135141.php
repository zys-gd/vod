<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191114135141 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE translations SET translation = 'The Videos service constitutes an additional benefit to the telecommunications service provided within the value added service and is a weekly subscription service allowing for premium games that don’t have any in-app purchases or ads on it (the “100% Sport”).' WHERE `key` = 'disclaimer.landing.3g' AND language_id = '5179f17c-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET translation = 'Serwis Filmy stanowi świadczenie dodatkowe w ramach usługi o podwyższonej opłacie i jest co tydzień usługą subskrypcyjną, która umożliwia pozwalając na gry premium, w których nie ma żadnych zakupów ani reklam (“100% Sport”).' WHERE `key` = 'disclaimer.landing.3g' AND language_id = '5179fd32-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("UPDATE translations SET translation = 'Tygodniowa subskrypcja użytkownika zostanie automatycznie odnowiona, chyba że użytkownik anuluje subskrypcję. Użytkownik może anulować subskrypcję w dowolnej chwili poprzez osobiste menu, klikając łącze Opuść 100% Sport. 100% Sport zastrzega sobie prawo do zakończenia subskrypcji w dowolnym momencie. <br/>Możesz dezaktywować poprzez: STOP 100SPORT na 80425 (0 zł).' WHERE `key` = 'terms.block_3.text.3' AND language_id = '5179fd32-ebd4-11e8-95c4-02bb250f0f22'");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('61572419-4d62-42f7-9023-69f3ad5a5410', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'terms.block_4.text.2', '<br/>Infolinia: 800 00 7173 (stawka lokalna).<br/>Wsparcie emailowe: info-pl@mobileinfo.biz lub support@origin-data.com.')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
