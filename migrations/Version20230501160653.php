<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230501160653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classroom (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, reserver TINYINT(1) NOT NULL, materiels VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, classroom_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, duree INT NOT NULL, background_color VARCHAR(7) NOT NULL, INDEX IDX_3BAE0AA741807E1D (teacher_id), INDEX IDX_3BAE0AA76278D5A8 (classroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_classes_ecole (event_id INT NOT NULL, classes_ecole_id INT NOT NULL, INDEX IDX_874FAABE71F7E88B (event_id), INDEX IDX_874FAABE3D1AFD07 (classes_ecole_id), PRIMARY KEY(event_id, classes_ecole_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_users (event_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_559814C571F7E88B (event_id), INDEX IDX_559814C567B3B43D (users_id), PRIMARY KEY(event_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classes_ecole_users ADD CONSTRAINT FK_54B98EBC3D1AFD07 FOREIGN KEY (classes_ecole_id) REFERENCES classes_ecole (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classes_ecole_users ADD CONSTRAINT FK_54B98EBC67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_event ADD CONSTRAINT FK_BC883717ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_event ADD CONSTRAINT FK_BC8837171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA741807E1D FOREIGN KEY (teacher_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id)');
        $this->addSql('ALTER TABLE event_classes_ecole ADD CONSTRAINT FK_874FAABE71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_classes_ecole ADD CONSTRAINT FK_874FAABE3D1AFD07 FOREIGN KEY (classes_ecole_id) REFERENCES classes_ecole (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C567B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classes_ecole_users DROP FOREIGN KEY FK_54B98EBC3D1AFD07');
        $this->addSql('ALTER TABLE classes_ecole_users DROP FOREIGN KEY FK_54B98EBC67B3B43D');
        $this->addSql('ALTER TABLE cours_event DROP FOREIGN KEY FK_BC883717ECF78B0');
        $this->addSql('ALTER TABLE cours_event DROP FOREIGN KEY FK_BC8837171F7E88B');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA741807E1D');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76278D5A8');
        $this->addSql('ALTER TABLE event_classes_ecole DROP FOREIGN KEY FK_874FAABE71F7E88B');
        $this->addSql('ALTER TABLE event_classes_ecole DROP FOREIGN KEY FK_874FAABE3D1AFD07');
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C571F7E88B');
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C567B3B43D');
        $this->addSql('DROP TABLE classes_ecole');
        $this->addSql('DROP TABLE classes_ecole_users');
        $this->addSql('DROP TABLE classroom');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE cours_event');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_classes_ecole');
        $this->addSql('DROP TABLE event_users');
    }
}
