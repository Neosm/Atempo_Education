<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210831102134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lecons_users (lecons_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_F99CBC734121F94A (lecons_id), INDEX IDX_F99CBC7367B3B43D (users_id), PRIMARY KEY(lecons_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lecons_users ADD CONSTRAINT FK_F99CBC734121F94A FOREIGN KEY (lecons_id) REFERENCES lecons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lecons_users ADD CONSTRAINT FK_F99CBC7367B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0B3305F4C');
        $this->addSql('DROP INDEX IDX_C74C24E0B3305F4C ON lecons');
        $this->addSql('ALTER TABLE lecons DROP type_cours_id');
        $this->addSql('ALTER TABLE users ADD type_cours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9B3305F4C FOREIGN KEY (type_cours_id) REFERENCES type_cours (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9B3305F4C ON users (type_cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE lecons_users');
        $this->addSql('ALTER TABLE lecons ADD type_cours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E0B3305F4C FOREIGN KEY (type_cours_id) REFERENCES type_cours (id)');
        $this->addSql('CREATE INDEX IDX_C74C24E0B3305F4C ON lecons (type_cours_id)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9B3305F4C');
        $this->addSql('DROP INDEX IDX_1483A5E9B3305F4C ON users');
        $this->addSql('ALTER TABLE users DROP type_cours_id');
    }
}
