<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231025091344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_class ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student_class ADD CONSTRAINT FK_657C60021D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_657C60021D096F5D ON student_class (ecoles_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_class DROP FOREIGN KEY FK_657C60021D096F5D');
        $this->addSql('DROP INDEX IDX_657C60021D096F5D ON student_class');
        $this->addSql('ALTER TABLE student_class DROP ecoles_id');
    }
}
