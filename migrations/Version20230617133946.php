<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230617133946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE absence (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, student_id INT DEFAULT NULL, absence_date DATETIME NOT NULL, INDEX IDX_765AE0C971F7E88B (event_id), INDEX IDX_765AE0C9CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delay (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, student_id INT DEFAULT NULL, delay_time DATETIME NOT NULL, INDEX IDX_B29A809B71F7E88B (event_id), INDEX IDX_B29A809BCB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C971F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9CB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE delay ADD CONSTRAINT FK_B29A809B71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE delay ADD CONSTRAINT FK_B29A809BCB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C971F7E88B');
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C9CB944F1A');
        $this->addSql('ALTER TABLE delay DROP FOREIGN KEY FK_B29A809B71F7E88B');
        $this->addSql('ALTER TABLE delay DROP FOREIGN KEY FK_B29A809BCB944F1A');
        $this->addSql('DROP TABLE absence');
        $this->addSql('DROP TABLE delay');
    }
}
