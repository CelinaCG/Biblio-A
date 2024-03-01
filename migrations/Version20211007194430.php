<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211007194430 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, isbn VARCHAR(100) DEFAULT NULL, ean VARCHAR(100) NOT NULL, langue VARCHAR(100) NOT NULL, titre VARCHAR(255) NOT NULL, editeur VARCHAR(100) NOT NULL, annee INT NOT NULL, format VARCHAR(255) NOT NULL, auteur VARCHAR(255) NOT NULL, coauteur VARCHAR(255) DEFAULT NULL, cote VARCHAR(50) NOT NULL, type_document VARCHAR(100) NOT NULL, categorie VARCHAR(255) NOT NULL, resume LONGTEXT NOT NULL, image_livre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE book');
    }
}
