<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602070321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE mouvement_produit (id SERIAL NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, type VARCHAR(10) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, motif TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E6653930F347EFB ON mouvement_produit (produit_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE produit (id SERIAL NOT NULL, user_r_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, stock INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_29A5EC273F3E7E0 ON produit (user_r_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mouvement_produit ADD CONSTRAINT FK_E6653930F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD CONSTRAINT FK_29A5EC273F3E7E0 FOREIGN KEY (user_r_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mouvement_produit DROP CONSTRAINT FK_E6653930F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP CONSTRAINT FK_29A5EC273F3E7E0
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mouvement_produit
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE produit
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
    }
}
