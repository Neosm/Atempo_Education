<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211221090341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9D04C7E15');
        $this->addSql('DROP INDEX IDX_1483A5E9D04C7E15 ON users');
        $this->addSql('ALTER TABLE users DROP type_programmes_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD type_programmes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D04C7E15 FOREIGN KEY (type_programmes_id) REFERENCES type_programmes (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9D04C7E15 ON users (type_programmes_id)');
    }
}
