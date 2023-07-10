<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230611141215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD student_class_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9598B478B FOREIGN KEY (student_class_id) REFERENCES student_class (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9598B478B ON users (student_class_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9598B478B');
        $this->addSql('DROP INDEX IDX_1483A5E9598B478B ON users');
        $this->addSql('ALTER TABLE users DROP student_class_id');
    }
}
