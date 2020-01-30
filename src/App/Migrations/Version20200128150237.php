<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200128150237 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE translations SET translation = 'Informationen über das Unternehmen' WHERE uuid IN ('a1a1daee-efbf-4b38-7r93-7cd0e0dcc1ba', '1a1daee-efbf-4198-7r93-7cd0e0dcc1ba', 'a1a1daee-efbf-4l28-7r93-7cd0e0dcc1ba', 'a1a1daee-efbf-4778-7r93-7cd0e0dcc1ba')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
