<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629144102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0F5E7B85A');
        $this->addSql('ALTER TABLE programmes DROP FOREIGN KEY FK_3631FC3FD04C7E15');
        $this->addSql('CREATE TABLE programmes_lecons (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE type_programmes_lecons');
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0F5E7B85A');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E0F5E7B85A FOREIGN KEY (type_lecons_id) REFERENCES programmes_lecons (id)');
        $this->addSql('ALTER TABLE programmes DROP FOREIGN KEY FK_3631FC3FD04C7E15');
        $this->addSql('ALTER TABLE programmes ADD CONSTRAINT FK_3631FC3FD04C7E15 FOREIGN KEY (type_programmes_id) REFERENCES programmes_lecons (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0F5E7B85A');
        $this->addSql('ALTER TABLE programmes DROP FOREIGN KEY FK_3631FC3FD04C7E15');
        $this->addSql('CREATE TABLE type_programmes_lecons (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE programmes_lecons');
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0F5E7B85A');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E0F5E7B85A FOREIGN KEY (type_lecons_id) REFERENCES type_programmes_lecons (id)');
        $this->addSql('ALTER TABLE programmes DROP FOREIGN KEY FK_3631FC3FD04C7E15');
        $this->addSql('ALTER TABLE programmes ADD CONSTRAINT FK_3631FC3FD04C7E15 FOREIGN KEY (type_programmes_id) REFERENCES type_programmes_lecons (id)');
    }
}
