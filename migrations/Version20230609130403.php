<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609130403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_users (event_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_559814C571F7E88B (event_id), INDEX IDX_559814C567B3B43D (users_id), PRIMARY KEY(event_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C567B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD room_id INT NOT NULL, ADD teacher_id INT NOT NULL, ADD student_class_id INT DEFAULT NULL, ADD start DATETIME NOT NULL, ADD duration INT NOT NULL, ADD end DATETIME NOT NULL, ADD comment LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA754177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA741807E1D FOREIGN KEY (teacher_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7598B478B FOREIGN KEY (student_class_id) REFERENCES student_class (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA754177093 ON event (room_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA741807E1D ON event (teacher_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7598B478B ON event (student_class_id)');
        $this->addSql('ALTER TABLE room ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE student_class ADD name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C571F7E88B');
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C567B3B43D');
        $this->addSql('DROP TABLE event_users');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA754177093');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA741807E1D');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7598B478B');
        $this->addSql('DROP INDEX IDX_3BAE0AA754177093 ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA741807E1D ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA7598B478B ON event');
        $this->addSql('ALTER TABLE event DROP room_id, DROP teacher_id, DROP student_class_id, DROP start, DROP duration, DROP end, DROP comment');
        $this->addSql('ALTER TABLE room DROP name');
        $this->addSql('ALTER TABLE student_class DROP name');
    }
}
