<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230618144200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD matieres_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA782350831 FOREIGN KEY (matieres_id) REFERENCES matieres (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA782350831 ON event (matieres_id)');
        $this->addSql('ALTER TABLE matieres DROP FOREIGN KEY FK_8D9773D271F7E88B');
        $this->addSql('DROP INDEX IDX_8D9773D271F7E88B ON matieres');
        $this->addSql('ALTER TABLE matieres DROP event_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA782350831');
        $this->addSql('DROP INDEX IDX_3BAE0AA782350831 ON event');
        $this->addSql('ALTER TABLE event DROP matieres_id');
        $this->addSql('ALTER TABLE matieres ADD event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE matieres ADD CONSTRAINT FK_8D9773D271F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_8D9773D271F7E88B ON matieres (event_id)');
    }
}
