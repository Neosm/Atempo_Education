<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210831083021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE programmes (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lecons ADD programmes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E0A0A1C920 FOREIGN KEY (programmes_id) REFERENCES programmes (id)');
        $this->addSql('CREATE INDEX IDX_C74C24E0A0A1C920 ON lecons (programmes_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0A0A1C920');
        $this->addSql('DROP TABLE programmes');
        $this->addSql('DROP INDEX IDX_C74C24E0A0A1C920 ON lecons');
        $this->addSql('ALTER TABLE lecons DROP programmes_id');
    }
}
