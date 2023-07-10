<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210831101142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecons ADD type_cours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E0B3305F4C FOREIGN KEY (type_cours_id) REFERENCES type_cours (id)');
        $this->addSql('CREATE INDEX IDX_C74C24E0B3305F4C ON lecons (type_cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0B3305F4C');
        $this->addSql('DROP INDEX IDX_C74C24E0B3305F4C ON lecons');
        $this->addSql('ALTER TABLE lecons DROP type_cours_id');
    }
}
