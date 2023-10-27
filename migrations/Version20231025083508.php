<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231025083508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C91D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_765AE0C91D096F5D ON absence (ecoles_id)');
        $this->addSql('ALTER TABLE articles ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31681D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_BFDD31681D096F5D ON articles (ecoles_id)');
        $this->addSql('ALTER TABLE categories ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF346681D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_3AF346681D096F5D ON categories (ecoles_id)');
        $this->addSql('ALTER TABLE delay ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE delay ADD CONSTRAINT FK_B29A809B1D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_B29A809B1D096F5D ON delay (ecoles_id)');
        $this->addSql('ALTER TABLE event ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA71D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA71D096F5D ON event (ecoles_id)');
        $this->addSql('ALTER TABLE lecons ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E01D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_C74C24E01D096F5D ON lecons (ecoles_id)');
        $this->addSql('ALTER TABLE materials ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE materials ADD CONSTRAINT FK_9B1716B51D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_9B1716B51D096F5D ON materials (ecoles_id)');
        $this->addSql('ALTER TABLE matieres ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE matieres ADD CONSTRAINT FK_8D9773D21D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_8D9773D21D096F5D ON matieres (ecoles_id)');
        $this->addSql('ALTER TABLE notes ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68C1D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_11BA68C1D096F5D ON notes (ecoles_id)');
        $this->addSql('ALTER TABLE programmes ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE programmes ADD CONSTRAINT FK_3631FC3F1D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_3631FC3F1D096F5D ON programmes (ecoles_id)');
        $this->addSql('ALTER TABLE room ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B1D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_729F519B1D096F5D ON room (ecoles_id)');
        $this->addSql('ALTER TABLE users ADD ecoles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E91D096F5D FOREIGN KEY (ecoles_id) REFERENCES ecoles (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E91D096F5D ON users (ecoles_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C91D096F5D');
        $this->addSql('DROP INDEX IDX_765AE0C91D096F5D ON absence');
        $this->addSql('ALTER TABLE absence DROP ecoles_id');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31681D096F5D');
        $this->addSql('DROP INDEX IDX_BFDD31681D096F5D ON articles');
        $this->addSql('ALTER TABLE articles DROP ecoles_id');
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF346681D096F5D');
        $this->addSql('DROP INDEX IDX_3AF346681D096F5D ON categories');
        $this->addSql('ALTER TABLE categories DROP ecoles_id');
        $this->addSql('ALTER TABLE delay DROP FOREIGN KEY FK_B29A809B1D096F5D');
        $this->addSql('DROP INDEX IDX_B29A809B1D096F5D ON delay');
        $this->addSql('ALTER TABLE delay DROP ecoles_id');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA71D096F5D');
        $this->addSql('DROP INDEX IDX_3BAE0AA71D096F5D ON event');
        $this->addSql('ALTER TABLE event DROP ecoles_id');
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E01D096F5D');
        $this->addSql('DROP INDEX IDX_C74C24E01D096F5D ON lecons');
        $this->addSql('ALTER TABLE lecons DROP ecoles_id');
        $this->addSql('ALTER TABLE materials DROP FOREIGN KEY FK_9B1716B51D096F5D');
        $this->addSql('DROP INDEX IDX_9B1716B51D096F5D ON materials');
        $this->addSql('ALTER TABLE materials DROP ecoles_id');
        $this->addSql('ALTER TABLE matieres DROP FOREIGN KEY FK_8D9773D21D096F5D');
        $this->addSql('DROP INDEX IDX_8D9773D21D096F5D ON matieres');
        $this->addSql('ALTER TABLE matieres DROP ecoles_id');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68C1D096F5D');
        $this->addSql('DROP INDEX IDX_11BA68C1D096F5D ON notes');
        $this->addSql('ALTER TABLE notes DROP ecoles_id');
        $this->addSql('ALTER TABLE programmes DROP FOREIGN KEY FK_3631FC3F1D096F5D');
        $this->addSql('DROP INDEX IDX_3631FC3F1D096F5D ON programmes');
        $this->addSql('ALTER TABLE programmes DROP ecoles_id');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B1D096F5D');
        $this->addSql('DROP INDEX IDX_729F519B1D096F5D ON room');
        $this->addSql('ALTER TABLE room DROP ecoles_id');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E91D096F5D');
        $this->addSql('DROP INDEX IDX_1483A5E91D096F5D ON users');
        $this->addSql('ALTER TABLE users DROP ecoles_id');
    }
}
