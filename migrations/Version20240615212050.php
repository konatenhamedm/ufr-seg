<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240615212050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96139DF194');
        $this->addSql('ALTER TABLE compta_preinscription DROP FOREIGN KEY FK_70C2C59B139DF194');
        $this->addSql('ALTER TABLE gestion_frais DROP FOREIGN KEY FK_685F237B139DF194');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6139DF194');
        $this->addSql('ALTER TABLE param_examen DROP FOREIGN KEY FK_99CCC5A139DF194');
        $this->addSql('ALTER TABLE param_info_niveau DROP FOREIGN KEY FK_6FCF4436139DF194');
        $this->addSql('ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4F139DF194');
        $this->addSql('ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4F5577AFDB');
        $this->addSql('ALTER TABLE evaluation_controle DROP FOREIGN KEY FK_37740483E90F137');
        $this->addSql('ALTER TABLE evaluation_controle DROP FOREIGN KEY FK_37740485577AFDB');
        $this->addSql('ALTER TABLE evaluation_controle DROP FOREIGN KEY FK_377404862E883B1');
        $this->addSql('ALTER TABLE evaluation_controle DROP FOREIGN KEY FK_37740488F5EA509');
        $this->addSql('ALTER TABLE evaluation_controle DROP FOREIGN KEY FK_37740489331C741');
        $this->addSql('ALTER TABLE evaluation_controle DROP FOREIGN KEY FK_3774048B7942F03');
        $this->addSql('ALTER TABLE evaluation_controle DROP FOREIGN KEY FK_3774048F46CD258');
        $this->addSql('ALTER TABLE evaluation_controle DROP FOREIGN KEY FK_3774048FB88E14F');
        $this->addSql('ALTER TABLE evaluation_examen_controle DROP FOREIGN KEY FK_A7703382139DF194');
        $this->addSql('ALTER TABLE evaluation_examen_controle DROP FOREIGN KEY FK_A77033823E90F137');
        $this->addSql('ALTER TABLE evaluation_examen_controle DROP FOREIGN KEY FK_A7703382613FECDF');
        $this->addSql('ALTER TABLE evaluation_examen_controle DROP FOREIGN KEY FK_A770338262E883B1');
        $this->addSql('ALTER TABLE evaluation_examen_decision DROP FOREIGN KEY FK_2DE5B4A4139DF194');
        $this->addSql('ALTER TABLE evaluation_examen_decision DROP FOREIGN KEY FK_2DE5B4A4613FECDF');
        $this->addSql('ALTER TABLE evaluation_examen_decision DROP FOREIGN KEY FK_2DE5B4A4DDEAB1A3');
        $this->addSql('ALTER TABLE evaluation_examen_groupe_type DROP FOREIGN KEY FK_7121104DCF55B5C6');
        $this->addSql('ALTER TABLE evaluation_examen_groupe_type DROP FOREIGN KEY FK_7121104DFB88E14F');
        $this->addSql('ALTER TABLE evaluation_examen_note DROP FOREIGN KEY FK_43FF8A7ACF55B5C6');
        $this->addSql('ALTER TABLE evaluation_examen_note DROP FOREIGN KEY FK_43FF8A7ADDEAB1A3');
        $this->addSql('ALTER TABLE evaluation_examen_valeur_note DROP FOREIGN KEY FK_987E8CFA968298C2');
        $this->addSql('ALTER TABLE evaluation_examen_valeur_note DROP FOREIGN KEY FK_987E8CFAFB88E14F');
        $this->addSql('ALTER TABLE evaluation_groupe_type DROP FOREIGN KEY FK_776FBCE93581E173');
        $this->addSql('ALTER TABLE evaluation_groupe_type DROP FOREIGN KEY FK_776FBCE9758926A6');
        $this->addSql('ALTER TABLE evaluation_moyenne_matiere DROP FOREIGN KEY FK_F1D528B162E883B1');
        $this->addSql('ALTER TABLE evaluation_moyenne_matiere DROP FOREIGN KEY FK_F1D528B1DDEAB1A3');
        $this->addSql('ALTER TABLE evaluation_moyenne_matiere DROP FOREIGN KEY FK_F1D528B1F46CD258');
        $this->addSql('ALTER TABLE evaluation_note DROP FOREIGN KEY FK_82FBB5AC758926A6');
        $this->addSql('ALTER TABLE evaluation_note DROP FOREIGN KEY FK_82FBB5ACDDEAB1A3');
        $this->addSql('ALTER TABLE evaluation_valeur_note DROP FOREIGN KEY FK_9E30205E968298C2');
        $this->addSql('ALTER TABLE evaluation_valeur_note DROP FOREIGN KEY FK_9E30205EFB88E14F');
        $this->addSql('ALTER TABLE param_promotion DROP FOREIGN KEY FK_5D49313953C59D72');
        $this->addSql('ALTER TABLE param_promotion DROP FOREIGN KEY FK_5D4931399331C741');
        $this->addSql('ALTER TABLE param_promotion DROP FOREIGN KEY FK_5D493139B3E9C81');
        $this->addSql('ALTER TABLE param_semestre DROP FOREIGN KEY FK_2C22CE789331C741');
        $this->addSql('ALTER TABLE param_session DROP FOREIGN KEY FK_F21E132E139DF194');
        $this->addSql('DROP TABLE evaluation_controle');
        $this->addSql('DROP TABLE evaluation_examen_controle');
        $this->addSql('DROP TABLE evaluation_examen_decision');
        $this->addSql('DROP TABLE evaluation_examen_groupe_type');
        $this->addSql('DROP TABLE evaluation_examen_note');
        $this->addSql('DROP TABLE evaluation_examen_valeur_note');
        $this->addSql('DROP TABLE evaluation_groupe_type');
        $this->addSql('DROP TABLE evaluation_moyenne_matiere');
        $this->addSql('DROP TABLE evaluation_note');
        $this->addSql('DROP TABLE evaluation_type_controle');
        $this->addSql('DROP TABLE evaluation_type_evaluation');
        $this->addSql('DROP TABLE evaluation_valeur_note');
        $this->addSql('DROP TABLE param_promotion');
        $this->addSql('DROP TABLE param_semestre');
        $this->addSql('DROP TABLE param_session');
        $this->addSql('DROP INDEX IDX_8F87BF96139DF194 ON classe');
        $this->addSql('ALTER TABLE classe ADD annee_scolaire_id INT DEFAULT NULL, CHANGE promotion_id niveau_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96B3E9C81 FOREIGN KEY (niveau_id) REFERENCES param_niveau (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF969331C741 FOREIGN KEY (annee_scolaire_id) REFERENCES annee_scolaire (id)');
        $this->addSql('CREATE INDEX IDX_8F87BF96B3E9C81 ON classe (niveau_id)');
        $this->addSql('CREATE INDEX IDX_8F87BF969331C741 ON classe (annee_scolaire_id)');
        $this->addSql('DROP INDEX IDX_70C2C59B139DF194 ON compta_preinscription');
        $this->addSql('ALTER TABLE compta_preinscription ADD niveau_id INT NOT NULL, DROP promotion_id');
        $this->addSql('ALTER TABLE compta_preinscription ADD CONSTRAINT FK_70C2C59BB3E9C81 FOREIGN KEY (niveau_id) REFERENCES param_niveau (id)');
        $this->addSql('CREATE INDEX IDX_70C2C59BB3E9C81 ON compta_preinscription (niveau_id)');
        $this->addSql('DROP INDEX IDX_685F237B139DF194 ON gestion_frais');
        $this->addSql('ALTER TABLE gestion_frais CHANGE promotion_id niveau_id INT NOT NULL');
        $this->addSql('ALTER TABLE gestion_frais ADD CONSTRAINT FK_685F237BB3E9C81 FOREIGN KEY (niveau_id) REFERENCES param_niveau (id)');
        $this->addSql('CREATE INDEX IDX_685F237BB3E9C81 ON gestion_frais (niveau_id)');
        $this->addSql('DROP INDEX IDX_5E90F6D6139DF194 ON inscription');
        $this->addSql('ALTER TABLE inscription ADD niveau_id INT DEFAULT NULL, CHANGE promotion_id niveau_etudiant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6386B8328 FOREIGN KEY (niveau_etudiant_id) REFERENCES niveau_etudiant (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6B3E9C81 FOREIGN KEY (niveau_id) REFERENCES param_niveau (id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D6386B8328 ON inscription (niveau_etudiant_id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D6B3E9C81 ON inscription (niveau_id)');
        $this->addSql('ALTER TABLE matiere_ue ADD coef INT NOT NULL, ADD nombre_credit INT NOT NULL, CHANGE visible visible TINYINT(1) DEFAULT true NOT NULL');
        $this->addSql('DROP INDEX IDX_99CCC5A139DF194 ON param_examen');
        $this->addSql('ALTER TABLE param_examen CHANGE promotion_id niveau_id INT NOT NULL');
        $this->addSql('ALTER TABLE param_examen ADD CONSTRAINT FK_99CCC5AB3E9C81 FOREIGN KEY (niveau_id) REFERENCES param_niveau (id)');
        $this->addSql('CREATE INDEX IDX_99CCC5AB3E9C81 ON param_examen (niveau_id)');
        $this->addSql('DROP INDEX IDX_6FCF4436139DF194 ON param_info_niveau');
        $this->addSql('ALTER TABLE param_info_niveau CHANGE promotion_id niveau_id INT NOT NULL');
        $this->addSql('ALTER TABLE param_info_niveau ADD CONSTRAINT FK_6FCF4436B3E9C81 FOREIGN KEY (niveau_id) REFERENCES param_niveau (id)');
        $this->addSql('CREATE INDEX IDX_6FCF4436B3E9C81 ON param_info_niveau (niveau_id)');
        $this->addSql('ALTER TABLE param_niveau ADD responsable_id INT NOT NULL');
        $this->addSql('ALTER TABLE param_niveau ADD CONSTRAINT FK_130FB0DD53C59D72 FOREIGN KEY (responsable_id) REFERENCES user_employe (id)');
        $this->addSql('CREATE INDEX IDX_130FB0DD53C59D72 ON param_niveau (responsable_id)');
        $this->addSql('ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4F5577AFDB');
        $this->addSql('DROP INDEX IDX_46D07C4F139DF194 ON unite_enseignement');
        $this->addSql('ALTER TABLE unite_enseignement CHANGE promotion_id niveau_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4FB3E9C81 FOREIGN KEY (niveau_id) REFERENCES param_niveau (id)');
        $this->addSql('ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4F5577AFDB FOREIGN KEY (semestre_id) REFERENCES semestre (id)');
        $this->addSql('CREATE INDEX IDX_46D07C4FB3E9C81 ON unite_enseignement (niveau_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evaluation_controle (id INT AUTO_INCREMENT NOT NULL, cour_id INT DEFAULT NULL, ue_id INT DEFAULT NULL, semestre_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, matiere_id INT DEFAULT NULL, classe_id INT DEFAULT NULL, annee_scolaire_id INT DEFAULT NULL, type_controle_id INT DEFAULT NULL, date_saisie DATETIME NOT NULL, date_compo DATETIME NOT NULL, UNIQUE INDEX classe_matiere (classe_id, matiere_id, cour_id), INDEX IDX_37740483E90F137 (type_controle_id), INDEX IDX_37740485577AFDB (semestre_id), INDEX IDX_377404862E883B1 (ue_id), INDEX IDX_37740488F5EA509 (classe_id), INDEX IDX_37740489331C741 (annee_scolaire_id), INDEX IDX_3774048B7942F03 (cour_id), INDEX IDX_3774048F46CD258 (matiere_id), INDEX IDX_3774048FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_examen_controle (id INT AUTO_INCREMENT NOT NULL, promotion_id INT DEFAULT NULL, ue_id INT DEFAULT NULL, session_id INT DEFAULT NULL, type_controle_id INT DEFAULT NULL, INDEX IDX_A7703382139DF194 (promotion_id), INDEX IDX_A77033823E90F137 (type_controle_id), INDEX IDX_A7703382613FECDF (session_id), INDEX IDX_A770338262E883B1 (ue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_examen_decision (id INT AUTO_INCREMENT NOT NULL, etudiant_id INT DEFAULT NULL, promotion_id INT DEFAULT NULL, session_id INT DEFAULT NULL, note_examen VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, moyenne_controle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nombre_credit VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, moyenne_annuelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, decision VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2DE5B4A4139DF194 (promotion_id), INDEX IDX_2DE5B4A4613FECDF (session_id), INDEX IDX_2DE5B4A4DDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_examen_groupe_type (id INT AUTO_INCREMENT NOT NULL, controle_examen_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, date_compo DATETIME NOT NULL, max INT NOT NULL, date_saisie DATETIME NOT NULL, INDEX IDX_7121104DCF55B5C6 (controle_examen_id), INDEX IDX_7121104DFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_examen_note (id INT AUTO_INCREMENT NOT NULL, controle_examen_id INT DEFAULT NULL, etudiant_id INT DEFAULT NULL, rang VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, exposant VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, moyenne_ue VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, moyenne_conrole VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, decision VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_43FF8A7ACF55B5C6 (controle_examen_id), INDEX IDX_43FF8A7ADDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_examen_valeur_note (id INT AUTO_INCREMENT NOT NULL, note_entity_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, note VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_987E8CFA968298C2 (note_entity_id), INDEX IDX_987E8CFAFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_groupe_type (id INT AUTO_INCREMENT NOT NULL, controle_id INT DEFAULT NULL, type_evaluation_id INT DEFAULT NULL, date_note DATETIME DEFAULT NULL, coef INT NOT NULL, date_saisie DATETIME NOT NULL, INDEX IDX_776FBCE93581E173 (type_evaluation_id), INDEX IDX_776FBCE9758926A6 (controle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_moyenne_matiere (id INT AUTO_INCREMENT NOT NULL, matiere_id INT DEFAULT NULL, etudiant_id INT DEFAULT NULL, ue_id INT DEFAULT NULL, moyenne VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, valide VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_F1D528B162E883B1 (ue_id), INDEX IDX_F1D528B1DDEAB1A3 (etudiant_id), INDEX IDX_F1D528B1F46CD258 (matiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_note (id INT AUTO_INCREMENT NOT NULL, controle_id INT DEFAULT NULL, etudiant_id INT DEFAULT NULL, moyenne_matiere VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, rang VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, exposant VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_82FBB5AC758926A6 (controle_id), INDEX IDX_82FBB5ACDDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_type_controle (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, coef VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_type_evaluation (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_valeur_note (id INT AUTO_INCREMENT NOT NULL, note_entity_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, note VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9E30205E968298C2 (note_entity_id), INDEX IDX_9E30205EFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE param_promotion (id INT AUTO_INCREMENT NOT NULL, annee_scolaire_id INT DEFAULT NULL, responsable_id INT NOT NULL, niveau_id INT DEFAULT NULL, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, numero INT NOT NULL, INDEX IDX_5D49313953C59D72 (responsable_id), INDEX IDX_5D4931399331C741 (annee_scolaire_id), INDEX IDX_5D493139B3E9C81 (niveau_id), UNIQUE INDEX numero_niveau (numero, niveau_id), UNIQUE INDEX UNIQ_5D49313977153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE param_semestre (id INT AUTO_INCREMENT NOT NULL, annee_scolaire_id INT DEFAULT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, coef INT NOT NULL, bloque VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, actif TINYINT(1) NOT NULL, INDEX IDX_2C22CE789331C741 (annee_scolaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE param_session (id INT AUTO_INCREMENT NOT NULL, promotion_id INT DEFAULT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_debut DATETIME NOT NULL, numero INT NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_F21E132E139DF194 (promotion_id), UNIQUE INDEX numero_session (numero, promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE evaluation_controle ADD CONSTRAINT FK_37740483E90F137 FOREIGN KEY (type_controle_id) REFERENCES evaluation_type_controle (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_controle ADD CONSTRAINT FK_37740485577AFDB FOREIGN KEY (semestre_id) REFERENCES param_semestre (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_controle ADD CONSTRAINT FK_377404862E883B1 FOREIGN KEY (ue_id) REFERENCES unite_enseignement (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_controle ADD CONSTRAINT FK_37740488F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_controle ADD CONSTRAINT FK_37740489331C741 FOREIGN KEY (annee_scolaire_id) REFERENCES annee_scolaire (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_controle ADD CONSTRAINT FK_3774048B7942F03 FOREIGN KEY (cour_id) REFERENCES cours (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_controle ADD CONSTRAINT FK_3774048F46CD258 FOREIGN KEY (matiere_id) REFERENCES gestion_matiere (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_controle ADD CONSTRAINT FK_3774048FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user_utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_controle ADD CONSTRAINT FK_A7703382139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_controle ADD CONSTRAINT FK_A77033823E90F137 FOREIGN KEY (type_controle_id) REFERENCES evaluation_type_controle (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_controle ADD CONSTRAINT FK_A7703382613FECDF FOREIGN KEY (session_id) REFERENCES param_session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_controle ADD CONSTRAINT FK_A770338262E883B1 FOREIGN KEY (ue_id) REFERENCES unite_enseignement (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_decision ADD CONSTRAINT FK_2DE5B4A4139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_decision ADD CONSTRAINT FK_2DE5B4A4613FECDF FOREIGN KEY (session_id) REFERENCES param_session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_decision ADD CONSTRAINT FK_2DE5B4A4DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user_etudiant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_groupe_type ADD CONSTRAINT FK_7121104DCF55B5C6 FOREIGN KEY (controle_examen_id) REFERENCES evaluation_examen_controle (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_groupe_type ADD CONSTRAINT FK_7121104DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user_utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_note ADD CONSTRAINT FK_43FF8A7ACF55B5C6 FOREIGN KEY (controle_examen_id) REFERENCES evaluation_examen_controle (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_note ADD CONSTRAINT FK_43FF8A7ADDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user_etudiant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_valeur_note ADD CONSTRAINT FK_987E8CFA968298C2 FOREIGN KEY (note_entity_id) REFERENCES evaluation_examen_note (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_examen_valeur_note ADD CONSTRAINT FK_987E8CFAFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user_utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_groupe_type ADD CONSTRAINT FK_776FBCE93581E173 FOREIGN KEY (type_evaluation_id) REFERENCES evaluation_type_evaluation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_groupe_type ADD CONSTRAINT FK_776FBCE9758926A6 FOREIGN KEY (controle_id) REFERENCES evaluation_controle (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_moyenne_matiere ADD CONSTRAINT FK_F1D528B162E883B1 FOREIGN KEY (ue_id) REFERENCES unite_enseignement (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_moyenne_matiere ADD CONSTRAINT FK_F1D528B1DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user_etudiant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_moyenne_matiere ADD CONSTRAINT FK_F1D528B1F46CD258 FOREIGN KEY (matiere_id) REFERENCES gestion_matiere (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_note ADD CONSTRAINT FK_82FBB5AC758926A6 FOREIGN KEY (controle_id) REFERENCES evaluation_controle (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_note ADD CONSTRAINT FK_82FBB5ACDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user_etudiant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_valeur_note ADD CONSTRAINT FK_9E30205E968298C2 FOREIGN KEY (note_entity_id) REFERENCES evaluation_note (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation_valeur_note ADD CONSTRAINT FK_9E30205EFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user_utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE param_promotion ADD CONSTRAINT FK_5D49313953C59D72 FOREIGN KEY (responsable_id) REFERENCES user_employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE param_promotion ADD CONSTRAINT FK_5D4931399331C741 FOREIGN KEY (annee_scolaire_id) REFERENCES annee_scolaire (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE param_promotion ADD CONSTRAINT FK_5D493139B3E9C81 FOREIGN KEY (niveau_id) REFERENCES param_niveau (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE param_semestre ADD CONSTRAINT FK_2C22CE789331C741 FOREIGN KEY (annee_scolaire_id) REFERENCES annee_scolaire (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE param_session ADD CONSTRAINT FK_F21E132E139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96B3E9C81');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF969331C741');
        $this->addSql('DROP INDEX IDX_8F87BF96B3E9C81 ON classe');
        $this->addSql('DROP INDEX IDX_8F87BF969331C741 ON classe');
        $this->addSql('ALTER TABLE classe ADD promotion_id INT DEFAULT NULL, DROP niveau_id, DROP annee_scolaire_id');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8F87BF96139DF194 ON classe (promotion_id)');
        $this->addSql('ALTER TABLE compta_preinscription DROP FOREIGN KEY FK_70C2C59BB3E9C81');
        $this->addSql('DROP INDEX IDX_70C2C59BB3E9C81 ON compta_preinscription');
        $this->addSql('ALTER TABLE compta_preinscription ADD promotion_id INT DEFAULT NULL, DROP niveau_id');
        $this->addSql('ALTER TABLE compta_preinscription ADD CONSTRAINT FK_70C2C59B139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_70C2C59B139DF194 ON compta_preinscription (promotion_id)');
        $this->addSql('ALTER TABLE gestion_frais DROP FOREIGN KEY FK_685F237BB3E9C81');
        $this->addSql('DROP INDEX IDX_685F237BB3E9C81 ON gestion_frais');
        $this->addSql('ALTER TABLE gestion_frais CHANGE niveau_id promotion_id INT NOT NULL');
        $this->addSql('ALTER TABLE gestion_frais ADD CONSTRAINT FK_685F237B139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_685F237B139DF194 ON gestion_frais (promotion_id)');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6386B8328');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6B3E9C81');
        $this->addSql('DROP INDEX IDX_5E90F6D6386B8328 ON inscription');
        $this->addSql('DROP INDEX IDX_5E90F6D6B3E9C81 ON inscription');
        $this->addSql('ALTER TABLE inscription ADD promotion_id INT DEFAULT NULL, DROP niveau_etudiant_id, DROP niveau_id');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5E90F6D6139DF194 ON inscription (promotion_id)');
        $this->addSql('ALTER TABLE matiere_ue DROP coef, DROP nombre_credit, CHANGE visible visible TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE param_examen DROP FOREIGN KEY FK_99CCC5AB3E9C81');
        $this->addSql('DROP INDEX IDX_99CCC5AB3E9C81 ON param_examen');
        $this->addSql('ALTER TABLE param_examen CHANGE niveau_id promotion_id INT NOT NULL');
        $this->addSql('ALTER TABLE param_examen ADD CONSTRAINT FK_99CCC5A139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_99CCC5A139DF194 ON param_examen (promotion_id)');
        $this->addSql('ALTER TABLE param_info_niveau DROP FOREIGN KEY FK_6FCF4436B3E9C81');
        $this->addSql('DROP INDEX IDX_6FCF4436B3E9C81 ON param_info_niveau');
        $this->addSql('ALTER TABLE param_info_niveau CHANGE niveau_id promotion_id INT NOT NULL');
        $this->addSql('ALTER TABLE param_info_niveau ADD CONSTRAINT FK_6FCF4436139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6FCF4436139DF194 ON param_info_niveau (promotion_id)');
        $this->addSql('ALTER TABLE param_niveau DROP FOREIGN KEY FK_130FB0DD53C59D72');
        $this->addSql('DROP INDEX IDX_130FB0DD53C59D72 ON param_niveau');
        $this->addSql('ALTER TABLE param_niveau DROP responsable_id');
        $this->addSql('ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4FB3E9C81');
        $this->addSql('ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4F5577AFDB');
        $this->addSql('DROP INDEX IDX_46D07C4FB3E9C81 ON unite_enseignement');
        $this->addSql('ALTER TABLE unite_enseignement CHANGE niveau_id promotion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4F139DF194 FOREIGN KEY (promotion_id) REFERENCES param_promotion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4F5577AFDB FOREIGN KEY (semestre_id) REFERENCES param_semestre (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_46D07C4F139DF194 ON unite_enseignement (promotion_id)');
    }
}
