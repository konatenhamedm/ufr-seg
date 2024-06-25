<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240616094525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE semestre DROP FOREIGN KEY FK_71688FBC9331C741');
        $this->addSql('DROP TABLE semestre');
        $this->addSql('DROP INDEX IDX_9E30205E26ED0855 ON evaluation_valeur_note');
        $this->addSql('ALTER TABLE evaluation_valeur_note DROP note, CHANGE note_entity_id note_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evaluation_valeur_note ADD CONSTRAINT FK_9E30205E26ED0855 FOREIGN KEY (note_id) REFERENCES note (id)');
        $this->addSql('ALTER TABLE evaluation_valeur_note ADD CONSTRAINT FK_9E30205EFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user_utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_9E30205E26ED0855 ON evaluation_valeur_note (note_id)');
        $this->addSql('ALTER TABLE frais_bloc ADD CONSTRAINT FK_3280333272AE4A38 FOREIGN KEY (type_frais_id) REFERENCES param_type_frais (id)');
        $this->addSql('ALTER TABLE frais_bloc ADD CONSTRAINT FK_32803332757F25FD FOREIGN KEY (bloc_echeancier_id) REFERENCES bloc_echeancier (id)');
        $this->addSql('ALTER TABLE matiere_ue CHANGE visible visible TINYINT(1) DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE param_niveau ADD CONSTRAINT FK_130FB0DD53C59D72 FOREIGN KEY (responsable_id) REFERENCES user_employe (id)');
        $this->addSql('CREATE INDEX IDX_130FB0DD53C59D72 ON param_niveau (responsable_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE semestre (id INT AUTO_INCREMENT NOT NULL, annee_scolaire_id INT DEFAULT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, coef INT NOT NULL, bloque VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, actif TINYINT(1) NOT NULL, INDEX IDX_71688FBC9331C741 (annee_scolaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE semestre ADD CONSTRAINT FK_71688FBC9331C741 FOREIGN KEY (annee_scolaire_id) REFERENCES annee_scolaire (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_valeur_note DROP FOREIGN KEY FK_9E30205E26ED0855');
        $this->addSql('ALTER TABLE evaluation_valeur_note DROP FOREIGN KEY FK_9E30205EFB88E14F');
        $this->addSql('DROP INDEX IDX_9E30205E26ED0855 ON evaluation_valeur_note');
        $this->addSql('ALTER TABLE evaluation_valeur_note ADD note VARCHAR(50) DEFAULT NULL, CHANGE note_id note_entity_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_9E30205E26ED0855 ON evaluation_valeur_note (note_entity_id)');
        $this->addSql('ALTER TABLE frais_bloc DROP FOREIGN KEY FK_3280333272AE4A38');
        $this->addSql('ALTER TABLE frais_bloc DROP FOREIGN KEY FK_32803332757F25FD');
        $this->addSql('ALTER TABLE matiere_ue CHANGE visible visible TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE param_niveau DROP FOREIGN KEY FK_130FB0DD53C59D72');
        $this->addSql('DROP INDEX IDX_130FB0DD53C59D72 ON param_niveau');
    }
}
