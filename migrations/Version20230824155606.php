<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824155606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ecoles ADD numero VARCHAR(255) NOT NULL, ADD ville VARCHAR(255) NOT NULL, ADD campus VARCHAR(255) NOT NULL, DROP siret, DROP siren');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ecoles ADD siret VARCHAR(255) NOT NULL, ADD siren VARCHAR(255) NOT NULL, DROP numero, DROP ville, DROP campus');
    }
}
