<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920100939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_BFDD31682B36786BFEC530A9 ON articles');
        $this->addSql('ALTER TABLE articles CHANGE categories_id categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images CHANGE articles_id articles_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD telephone VARCHAR(10) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles CHANGE categories_id categories_id INT NOT NULL');
        $this->addSql('CREATE FULLTEXT INDEX IDX_BFDD31682B36786BFEC530A9 ON articles (title, content)');
        $this->addSql('ALTER TABLE images CHANGE articles_id articles_id INT NOT NULL');
        $this->addSql('ALTER TABLE users DROP telephone');
    }
}
