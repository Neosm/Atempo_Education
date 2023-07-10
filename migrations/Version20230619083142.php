<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619083142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notes (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, matiere_id INT DEFAULT NULL, note INT NOT NULL, INDEX IDX_11BA68CA76ED395 (user_id), INDEX IDX_11BA68CF46CD258 (matiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CF46CD258 FOREIGN KEY (matiere_id) REFERENCES matieres (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CA76ED395');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CF46CD258');
        $this->addSql('DROP TABLE notes');
    }
}
