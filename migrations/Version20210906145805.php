<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210906145805 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livres (id INT AUTO_INCREMENT NOT NULL, isbn VARCHAR(255) DEFAULT NULL, ean VARCHAR(255) NOT NULL, langue VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, editeur VARCHAR(255) NOT NULL, annee SMALLINT NOT NULL, format VARCHAR(255) NOT NULL, auteur_nom VARCHAR(255) NOT NULL, auteur_prenom VARCHAR(255) NOT NULL, coauteur_nom VARCHAR(255) DEFAULT NULL, coauteur_prenom VARCHAR(255) DEFAULT NULL, cote VARCHAR(50) NOT NULL, type_document VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, resume LONGTEXT NOT NULL, image_livre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE livres');
    }
}
