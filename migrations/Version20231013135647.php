<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231013135647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE absence (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, student_id INT DEFAULT NULL, absence_date DATETIME NOT NULL, INDEX IDX_765AE0C971F7E88B (event_id), INDEX IDX_765AE0C9CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, categories_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', active TINYINT(1) NOT NULL, illustrations VARCHAR(255) NOT NULL, INDEX IDX_BFDD316867B3B43D (users_id), INDEX IDX_BFDD3168A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_users (articles_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_FC618D1D1EBAF6CC (articles_id), INDEX IDX_FC618D1D67B3B43D (users_id), PRIMARY KEY(articles_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delay (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, student_id INT DEFAULT NULL, delay_minutes INT NOT NULL, INDEX IDX_B29A809B71F7E88B (event_id), INDEX IDX_B29A809BCB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ecoles (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, numero VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, campus VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, student_class_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, matieres_id INT DEFAULT NULL, parent_event_id INT DEFAULT NULL, start DATETIME NOT NULL, duration INT NOT NULL, end DATETIME NOT NULL, comment LONGTEXT DEFAULT NULL, title VARCHAR(255) NOT NULL, id_unique VARCHAR(255) NOT NULL, objectif LONGTEXT DEFAULT NULL, zoom_link VARCHAR(255) DEFAULT NULL, recurrence TINYINT(1) DEFAULT NULL, recurrence_end DATETIME DEFAULT NULL, recurrence_frequency VARCHAR(255) DEFAULT NULL, INDEX IDX_3BAE0AA754177093 (room_id), INDEX IDX_3BAE0AA7598B478B (student_class_id), INDEX IDX_3BAE0AA741807E1D (teacher_id), INDEX IDX_3BAE0AA782350831 (matieres_id), INDEX IDX_3BAE0AA7EE3A445A (parent_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_users (event_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_559814C571F7E88B (event_id), INDEX IDX_559814C567B3B43D (users_id), PRIMARY KEY(event_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_programmes (event_id INT NOT NULL, programmes_id INT NOT NULL, INDEX IDX_6EDF251071F7E88B (event_id), INDEX IDX_6EDF2510A0A1C920 (programmes_id), PRIMARY KEY(event_id, programmes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_lecons (event_id INT NOT NULL, lecons_id INT NOT NULL, INDEX IDX_F5D553B271F7E88B (event_id), INDEX IDX_F5D553B24121F94A (lecons_id), PRIMARY KEY(event_id, lecons_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, articles_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6A1EBAF6CC (articles_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lecons (id INT AUTO_INCREMENT NOT NULL, programmes_id INT DEFAULT NULL, type_lecons_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, thumbnails VARCHAR(255) DEFAULT NULL, audio VARCHAR(255) DEFAULT NULL, pdf VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, contenu LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C74C24E0A0A1C920 (programmes_id), INDEX IDX_C74C24E0F5E7B85A (type_lecons_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lecons_users (lecons_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_F99CBC734121F94A (lecons_id), INDEX IDX_F99CBC7367B3B43D (users_id), PRIMARY KEY(lecons_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE materials (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matieres (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notes (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, matiere_id INT DEFAULT NULL, note INT NOT NULL, date DATE DEFAULT NULL, coefficient INT NOT NULL, INDEX IDX_11BA68CA76ED395 (user_id), INDEX IDX_11BA68CF46CD258 (matiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programmes (id INT AUTO_INCREMENT NOT NULL, programmes_lecons_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_3631FC3F675372E7 (programmes_lecons_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programmes_users (programmes_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_8E2247B7A0A1C920 (programmes_id), INDEX IDX_8E2247B767B3B43D (users_id), PRIMARY KEY(programmes_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programmes_lecons (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_materials (room_id INT NOT NULL, materials_id INT NOT NULL, INDEX IDX_7BEF6DC54177093 (room_id), INDEX IDX_7BEF6DC3A9FC940 (materials_id), PRIMARY KEY(room_id, materials_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_class (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, student_class_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, online TINYINT(1) NOT NULL, thumbnail VARCHAR(255) NOT NULL, telephone VARCHAR(10) NOT NULL, date_of_birth DATE NOT NULL, id_unique VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX IDX_1483A5E9598B478B (student_class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C971F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9CB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316867B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE articles_users ADD CONSTRAINT FK_FC618D1D1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_users ADD CONSTRAINT FK_FC618D1D67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE delay ADD CONSTRAINT FK_B29A809B71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE delay ADD CONSTRAINT FK_B29A809BCB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA754177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7598B478B FOREIGN KEY (student_class_id) REFERENCES student_class (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA741807E1D FOREIGN KEY (teacher_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA782350831 FOREIGN KEY (matieres_id) REFERENCES matieres (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7EE3A445A FOREIGN KEY (parent_event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C567B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_programmes ADD CONSTRAINT FK_6EDF251071F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_programmes ADD CONSTRAINT FK_6EDF2510A0A1C920 FOREIGN KEY (programmes_id) REFERENCES programmes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_lecons ADD CONSTRAINT FK_F5D553B271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_lecons ADD CONSTRAINT FK_F5D553B24121F94A FOREIGN KEY (lecons_id) REFERENCES lecons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E0A0A1C920 FOREIGN KEY (programmes_id) REFERENCES programmes (id)');
        $this->addSql('ALTER TABLE lecons ADD CONSTRAINT FK_C74C24E0F5E7B85A FOREIGN KEY (type_lecons_id) REFERENCES programmes_lecons (id)');
        $this->addSql('ALTER TABLE lecons_users ADD CONSTRAINT FK_F99CBC734121F94A FOREIGN KEY (lecons_id) REFERENCES lecons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lecons_users ADD CONSTRAINT FK_F99CBC7367B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CF46CD258 FOREIGN KEY (matiere_id) REFERENCES matieres (id)');
        $this->addSql('ALTER TABLE programmes ADD CONSTRAINT FK_3631FC3F675372E7 FOREIGN KEY (programmes_lecons_id) REFERENCES programmes_lecons (id)');
        $this->addSql('ALTER TABLE programmes_users ADD CONSTRAINT FK_8E2247B7A0A1C920 FOREIGN KEY (programmes_id) REFERENCES programmes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE programmes_users ADD CONSTRAINT FK_8E2247B767B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE room_materials ADD CONSTRAINT FK_7BEF6DC54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_materials ADD CONSTRAINT FK_7BEF6DC3A9FC940 FOREIGN KEY (materials_id) REFERENCES materials (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9598B478B FOREIGN KEY (student_class_id) REFERENCES student_class (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C971F7E88B');
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C9CB944F1A');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316867B3B43D');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A21214B7');
        $this->addSql('ALTER TABLE articles_users DROP FOREIGN KEY FK_FC618D1D1EBAF6CC');
        $this->addSql('ALTER TABLE articles_users DROP FOREIGN KEY FK_FC618D1D67B3B43D');
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE delay DROP FOREIGN KEY FK_B29A809B71F7E88B');
        $this->addSql('ALTER TABLE delay DROP FOREIGN KEY FK_B29A809BCB944F1A');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA754177093');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7598B478B');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA741807E1D');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA782350831');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7EE3A445A');
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C571F7E88B');
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C567B3B43D');
        $this->addSql('ALTER TABLE event_programmes DROP FOREIGN KEY FK_6EDF251071F7E88B');
        $this->addSql('ALTER TABLE event_programmes DROP FOREIGN KEY FK_6EDF2510A0A1C920');
        $this->addSql('ALTER TABLE event_lecons DROP FOREIGN KEY FK_F5D553B271F7E88B');
        $this->addSql('ALTER TABLE event_lecons DROP FOREIGN KEY FK_F5D553B24121F94A');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A1EBAF6CC');
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0A0A1C920');
        $this->addSql('ALTER TABLE lecons DROP FOREIGN KEY FK_C74C24E0F5E7B85A');
        $this->addSql('ALTER TABLE lecons_users DROP FOREIGN KEY FK_F99CBC734121F94A');
        $this->addSql('ALTER TABLE lecons_users DROP FOREIGN KEY FK_F99CBC7367B3B43D');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CA76ED395');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CF46CD258');
        $this->addSql('ALTER TABLE programmes DROP FOREIGN KEY FK_3631FC3F675372E7');
        $this->addSql('ALTER TABLE programmes_users DROP FOREIGN KEY FK_8E2247B7A0A1C920');
        $this->addSql('ALTER TABLE programmes_users DROP FOREIGN KEY FK_8E2247B767B3B43D');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE room_materials DROP FOREIGN KEY FK_7BEF6DC54177093');
        $this->addSql('ALTER TABLE room_materials DROP FOREIGN KEY FK_7BEF6DC3A9FC940');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9598B478B');
        $this->addSql('DROP TABLE absence');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE articles_users');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE delay');
        $this->addSql('DROP TABLE ecoles');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_users');
        $this->addSql('DROP TABLE event_programmes');
        $this->addSql('DROP TABLE event_lecons');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE lecons');
        $this->addSql('DROP TABLE lecons_users');
        $this->addSql('DROP TABLE materials');
        $this->addSql('DROP TABLE matieres');
        $this->addSql('DROP TABLE notes');
        $this->addSql('DROP TABLE programmes');
        $this->addSql('DROP TABLE programmes_users');
        $this->addSql('DROP TABLE programmes_lecons');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_materials');
        $this->addSql('DROP TABLE student_class');
        $this->addSql('DROP TABLE users');
    }
}
