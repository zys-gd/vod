<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200124122741 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE translations SET translation = 'Name: ORIGINDATA SAS<br/>Adresse: 14 Bis rue Daru. 75008, Paris, France.<br/>Umsatzsteuer-Identifikationsnummer: FR 48849375662<br/>Handelsregisternummer: 849375662' WHERE uuid IN ('a1a1daee-efbf-4b7r-9693-7cd0e0dcc1ba', 'a1a1daee-efbf-197r-9693-7cd0e0dcc1ba', 'a1a1daee-efbf-l27r-9693-7cd0e0dcc1ba', 'a1a1daee-efbf-777r-9693-7cd0e0dcc1ba')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
