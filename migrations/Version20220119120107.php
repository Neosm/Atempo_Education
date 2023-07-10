<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119120107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecons ADD type_lecons_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E0F5E7B85A FOREIGN KEY (type_lecons_id) REFERENCES type_programmes (id)');
        $this->addSql('CREATE INDEX IDX_C74C24E0F5E7B85A ON lecons (type_lecons_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0F5E7B85A');
        $this->addSql('DROP INDEX IDX_C74C24E0F5E7B85A ON lecons');
        $this->addSql('ALTER TABLE lecons DROP type_lecons_id');
    }
}
