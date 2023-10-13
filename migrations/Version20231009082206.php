<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009082206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD parent_event_id INT DEFAULT NULL, ADD recurrence JSON DEFAULT NULL, ADD recurrence_end DATETIME DEFAULT NULL, ADD recurrence_frequency VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7EE3A445A FOREIGN KEY (parent_event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7EE3A445A ON event (parent_event_id)');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7EE3A445A');
        $this->addSql('DROP INDEX IDX_3BAE0AA7EE3A445A ON event');
        $this->addSql('ALTER TABLE event DROP parent_event_id, DROP recurrence, DROP recurrence_end, DROP recurrence_frequency');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
