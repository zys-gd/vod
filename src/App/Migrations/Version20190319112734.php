<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190319112734 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE translations SET translation = "Watch unlimited videos of your favorite sports and teams!<br/>Only %price% %currency% per %period%" WHERE uuid = "0f5cf82e-b95b-45d1-80aa-e971aef6f2ac"');
        $this->addSql('UPDATE translations SET translation = "Watch unlimited sports videos for only %price% %currency% per %period%. Access to hundreds of sport videos and news every day. Stay tuned!" WHERE uuid = "1afc25c9-e1bd-4520-ae58-c917f3434e65"');
        $this->addSql('UPDATE translations SET translation = "Watch unlimited videos of your favorite sports and teams!<br/>Only %price% %currency% per %period%" WHERE uuid = "51136644-9be4-4eac-b466-f617e7238d00"');
        $this->addSql('UPDATE translations SET translation = "Watch unlimited sport videos for only %price% %currency% per %period%" WHERE uuid = "7e52ca82-5c67-4df9-80b5-4131a56f7bbb"');
        $this->addSql('UPDATE translations SET translation = "Welcome to 100% Sport!\n<small>Get unlimited videos each %period% for only %price%/%currency%</small>" WHERE uuid = "c4a1eb00-b547-47e6-8cb5-61901c4e9585"');
        $this->addSql('UPDATE translations SET translation = "Get unlimited videos each %period% for only %price%/%currency%" WHERE uuid = "27f643fc-3e18-47d1-bbd7-8a3a14660af4"');
        $this->addSql('UPDATE translations SET translation = "%price% %currency% per %period%" WHERE uuid = "14c4c9bb-efbe-4ddb-a639-0f5ac27d4c88"');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE translations SET translation = "Watch unlimited videos of your favorite sports and teams!<br/>Only %price% %currency% per %period% (tax included)" WHERE uuid = "0f5cf82e-b95b-45d1-80aa-e971aef6f2ac"');
        $this->addSql('UPDATE translations SET translation = "Watch unlimited sports videos for only %price% %currency% per %period% (tax included). Access to hundreds of sport videos and news every day. Stay tuned!" WHERE uuid = "1afc25c9-e1bd-4520-ae58-c917f3434e65"');
        $this->addSql('UPDATE translations SET translation = "Watch unlimited videos of your favorite sports and teams!<br/>Only %price% %currency% per %period% (tax included)" WHERE uuid = "51136644-9be4-4eac-b466-f617e7238d00"');
        $this->addSql('UPDATE translations SET translation = "Watch unlimited sport videos for only %price% %currency% per %period% (tax included)" WHERE uuid = "7e52ca82-5c67-4df9-80b5-4131a56f7bbb"');
        $this->addSql('UPDATE translations SET translation = "Welcome to 100% Sport!\n<small>Get unlimited videos each %period% for only %price%/%currency% (tax included)</small>" WHERE uuid = "c4a1eb00-b547-47e6-8cb5-61901c4e9585"');
        $this->addSql('UPDATE translations SET translation = "Get unlimited videos each %period% for only %price%/%currency% (tax included)" WHERE uuid = "27f643fc-3e18-47d1-bbd7-8a3a14660af4"');
        $this->addSql('UPDATE translations SET translation = "%price% %currency% per %period% (tax included)" WHERE uuid = "14c4c9bb-efbe-4ddb-a639-0f5ac27d4c88"');
    }
}
