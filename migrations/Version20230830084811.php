<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230830084811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D1BCFAAD7');
        $this->addSql('DROP INDEX IDX_6EEAA67D1BCFAAD7 ON commande');
        $this->addSql('ALTER TABLE commande ADD date_enregistrement DATETIME NOT NULL, CHANGE c_commande_id commande_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D82EA2E54 FOREIGN KEY (commande_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D82EA2E54 ON commande (commande_id)');
        $this->addSql('ALTER TABLE produit ADD date_enregistrement DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD date_enregistrement DATETIME NOT NULL, DROP mdp, DROP statut');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D82EA2E54');
        $this->addSql('DROP INDEX IDX_6EEAA67D82EA2E54 ON commande');
        $this->addSql('ALTER TABLE commande DROP date_enregistrement, CHANGE commande_id c_commande_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D1BCFAAD7 FOREIGN KEY (c_commande_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D1BCFAAD7 ON commande (c_commande_id)');
        $this->addSql('ALTER TABLE produit DROP date_enregistrement');
        $this->addSql('ALTER TABLE user ADD mdp VARCHAR(255) NOT NULL, ADD statut INT NOT NULL, DROP date_enregistrement');
    }
}
