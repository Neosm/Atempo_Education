<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230817143032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_programmes (event_id INT NOT NULL, programmes_id INT NOT NULL, INDEX IDX_6EDF251071F7E88B (event_id), INDEX IDX_6EDF2510A0A1C920 (programmes_id), PRIMARY KEY(event_id, programmes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_lecons (event_id INT NOT NULL, lecons_id INT NOT NULL, INDEX IDX_F5D553B271F7E88B (event_id), INDEX IDX_F5D553B24121F94A (lecons_id), PRIMARY KEY(event_id, lecons_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_programmes ADD CONSTRAINT FK_6EDF251071F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_programmes ADD CONSTRAINT FK_6EDF2510A0A1C920 FOREIGN KEY (programmes_id) REFERENCES programmes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_lecons ADD CONSTRAINT FK_F5D553B271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_lecons ADD CONSTRAINT FK_F5D553B24121F94A FOREIGN KEY (lecons_id) REFERENCES lecons (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_programmes DROP FOREIGN KEY FK_6EDF251071F7E88B');
        $this->addSql('ALTER TABLE event_programmes DROP FOREIGN KEY FK_6EDF2510A0A1C920');
        $this->addSql('ALTER TABLE event_lecons DROP FOREIGN KEY FK_F5D553B271F7E88B');
        $this->addSql('ALTER TABLE event_lecons DROP FOREIGN KEY FK_F5D553B24121F94A');
        $this->addSql('DROP TABLE event_programmes');
        $this->addSql('DROP TABLE event_lecons');
    }
}
