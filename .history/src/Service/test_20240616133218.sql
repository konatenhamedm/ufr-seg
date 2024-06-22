/*
SQLyog Professional v13.1.1 (64 bit)
MySQL - 8.0.30 : Database - db_willy
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_willy` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `db_willy`;

/*Table structure for table `annee_scolaire` */

DROP TABLE IF EXISTS `annee_scolaire`;

CREATE TABLE `annee_scolaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `actif` tinyint(1) DEFAULT NULL,
  `verrou` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `annee_scolaire` */

insert  into `annee_scolaire`(`id`,`libelle`,`date_debut`,`date_fin`,`actif`,`verrou`) values 
(1,'2024-2025','2024-01-01 00:00:00','2024-12-31 00:00:00',1,1),
(2,'2025-2026','2024-01-01 00:00:00','2025-12-31 00:00:00',0,0),
(3,'2026-2027','2024-03-13 00:00:00','2024-03-14 00:00:00',0,0);

/*Table structure for table `bloc_echeancier` */

DROP TABLE IF EXISTS `bloc_echeancier`;

CREATE TABLE `bloc_echeancier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `classe_id` int DEFAULT NULL,
  `etudiant_id` int DEFAULT NULL,
  `total` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_inscription` datetime DEFAULT NULL,
  `inscription_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A354E99A8F5EA509` (`classe_id`),
  KEY `IDX_A354E99ADDEAB1A3` (`etudiant_id`),
  KEY `IDX_A354E99A5DAC5993` (`inscription_id`),
  CONSTRAINT `FK_A354E99A5DAC5993` FOREIGN KEY (`inscription_id`) REFERENCES `inscription` (`id`),
  CONSTRAINT `FK_A354E99A8F5EA509` FOREIGN KEY (`classe_id`) REFERENCES `classe` (`id`),
  CONSTRAINT `FK_A354E99ADDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `bloc_echeancier` */

insert  into `bloc_echeancier`(`id`,`classe_id`,`etudiant_id`,`total`,`date_inscription`,`inscription_id`) values 
(28,10,75,'152000','2024-06-05 00:00:00',29);

/*Table structure for table `classe` */

DROP TABLE IF EXISTS `classe`;

CREATE TABLE `classe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `promotion_id` int DEFAULT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F87BF96139DF194` (`promotion_id`),
  CONSTRAINT `FK_8F87BF96139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `classe` */

insert  into `classe`(`id`,`promotion_id`,`libelle`) values 
(10,4,'classe 1'),
(11,5,'classe 2');

/*Table structure for table `compta_info_inscription` */

DROP TABLE IF EXISTS `compta_info_inscription`;

CREATE TABLE `compta_info_inscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `inscription_id` int DEFAULT NULL,
  `mode_paiement_id` int DEFAULT NULL,
  `date_paiement` datetime NOT NULL,
  `montant` decimal(10,0) NOT NULL,
  `caissiere_id` int DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_cheque` datetime DEFAULT NULL,
  `tireur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observation` longtext COLLATE utf8mb4_unicode_ci,
  `numero_cheque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_credit` datetime DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `type_frais_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E1A29692FB88E14F` (`utilisateur_id`),
  KEY `IDX_E1A296925DAC5993` (`inscription_id`),
  KEY `IDX_E1A29692438F5B63` (`mode_paiement_id`),
  KEY `IDX_E1A2969223BD48D5` (`caissiere_id`),
  KEY `IDX_E1A2969272AE4A38` (`type_frais_id`),
  CONSTRAINT `FK_E1A2969223BD48D5` FOREIGN KEY (`caissiere_id`) REFERENCES `user_utilisateur` (`id`),
  CONSTRAINT `FK_E1A29692438F5B63` FOREIGN KEY (`mode_paiement_id`) REFERENCES `param_nature_paiement` (`id`),
  CONSTRAINT `FK_E1A296925DAC5993` FOREIGN KEY (`inscription_id`) REFERENCES `inscription` (`id`),
  CONSTRAINT `FK_E1A2969272AE4A38` FOREIGN KEY (`type_frais_id`) REFERENCES `param_type_frais` (`id`),
  CONSTRAINT `FK_E1A29692FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `compta_info_inscription` */

insert  into `compta_info_inscription`(`id`,`utilisateur_id`,`inscription_id`,`mode_paiement_id`,`date_paiement`,`montant`,`caissiere_id`,`code`,`etat`,`banque`,`date_cheque`,`tireur`,`contact`,`observation`,`numero_cheque`,`date_credit`,`date_validation`,`type_frais_id`) values 
(102,1,29,5,'2024-06-08 00:00:00',50000,1,'N001-24-001','payer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-06-05 17:57:32',2),
(104,1,29,5,'2024-06-20 00:00:00',2000,1,'N001-24-001','payer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-06-05 18:08:56',3);

/*Table structure for table `compta_info_preinscription` */

DROP TABLE IF EXISTS `compta_info_preinscription`;

CREATE TABLE `compta_info_preinscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `preinscription_id` int NOT NULL,
  `montant` decimal(10,0) NOT NULL,
  `date_paiement` date NOT NULL,
  `mode_paiement_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BE2340EB8337288` (`preinscription_id`),
  KEY `IDX_BE2340EBFB88E14F` (`utilisateur_id`),
  KEY `IDX_BE2340EB438F5B63` (`mode_paiement_id`),
  CONSTRAINT `FK_BE2340EB438F5B63` FOREIGN KEY (`mode_paiement_id`) REFERENCES `param_nature_paiement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_BE2340EB8337288` FOREIGN KEY (`preinscription_id`) REFERENCES `compta_preinscription` (`id`),
  CONSTRAINT `FK_BE2340EBFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `compta_info_preinscription` */

/*Table structure for table `compta_paiement` */

DROP TABLE IF EXISTS `compta_paiement`;

CREATE TABLE `compta_paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_paiement` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3A874999FB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_3A874999FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `compta_paiement` */

/*Table structure for table `compta_preinscription` */

DROP TABLE IF EXISTS `compta_preinscription`;

CREATE TABLE `compta_preinscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `date_preinscription` date NOT NULL,
  `etat` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caissiere_id` int DEFAULT NULL,
  `etat_deliberation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `commentaire` longtext COLLATE utf8mb4_unicode_ci,
  `promotion_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_70C2C59BDDEAB1A3` (`etudiant_id`),
  KEY `IDX_70C2C59BFB88E14F` (`utilisateur_id`),
  KEY `IDX_70C2C59B23BD48D5` (`caissiere_id`),
  KEY `IDX_70C2C59B139DF194` (`promotion_id`),
  CONSTRAINT `FK_70C2C59B139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`),
  CONSTRAINT `FK_70C2C59B23BD48D5` FOREIGN KEY (`caissiere_id`) REFERENCES `user_utilisateur` (`id`),
  CONSTRAINT `FK_70C2C59BDDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`),
  CONSTRAINT `FK_70C2C59BFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `compta_preinscription` */

/*Table structure for table `compta_versement` */

DROP TABLE IF EXISTS `compta_versement`;

CREATE TABLE `compta_versement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `frais_inscription_id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `nature_id` int NOT NULL,
  `date_versement` date NOT NULL,
  `montant` decimal(10,0) NOT NULL,
  `reference` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_239DED712EAFCF0` (`frais_inscription_id`),
  KEY `IDX_239DED7FB88E14F` (`utilisateur_id`),
  KEY `IDX_239DED73BCB2E4B` (`nature_id`),
  CONSTRAINT `FK_239DED712EAFCF0` FOREIGN KEY (`frais_inscription_id`) REFERENCES `gestion_frais_inscription_etudiant` (`id`),
  CONSTRAINT `FK_239DED73BCB2E4B` FOREIGN KEY (`nature_id`) REFERENCES `param_nature_paiement` (`id`),
  CONSTRAINT `FK_239DED7FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `compta_versement` */

/*Table structure for table `cours` */

DROP TABLE IF EXISTS `cours`;

CREATE TABLE `cours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `classe_id` int DEFAULT NULL,
  `matiere_id` int DEFAULT NULL,
  `annee_scolaire_id` int DEFAULT NULL,
  `employe_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FDCA8C9CF46CD258` (`matiere_id`),
  KEY `IDX_FDCA8C9C9331C741` (`annee_scolaire_id`),
  KEY `IDX_FDCA8C9C1B65292` (`employe_id`),
  KEY `IDX_FDCA8C9C8F5EA509` (`classe_id`),
  CONSTRAINT `FK_FDCA8C9C1B65292` FOREIGN KEY (`employe_id`) REFERENCES `user_employe` (`id`),
  CONSTRAINT `FK_FDCA8C9C8F5EA509` FOREIGN KEY (`classe_id`) REFERENCES `classe` (`id`),
  CONSTRAINT `FK_FDCA8C9C9331C741` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annee_scolaire` (`id`),
  CONSTRAINT `FK_FDCA8C9CF46CD258` FOREIGN KEY (`matiere_id`) REFERENCES `gestion_matiere` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cours` */

insert  into `cours`(`id`,`classe_id`,`matiere_id`,`annee_scolaire_id`,`employe_id`) values 
(8,10,1,1,38),
(9,10,2,1,39);

/*Table structure for table `cursus_professionnel` */

DROP TABLE IF EXISTS `cursus_professionnel`;

CREATE TABLE `cursus_professionnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int DEFAULT NULL,
  `emploi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employeur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `activite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_847346D6DDEAB1A3` (`etudiant_id`),
  CONSTRAINT `FK_847346D6DDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cursus_professionnel` */

insert  into `cursus_professionnel`(`id`,`etudiant_id`,`emploi`,`employeur`,`contact`,`date_debut`,`date_fin`,`activite`) values 
(1,31,'ddd','dd','fff','2024-02-08 00:00:00','2024-02-15 00:00:00','dd'),
(2,45,'ddd','fd','fdd','2024-04-10 00:00:00','2024-04-17 00:00:00','dfff');

/*Table structure for table `cursus_universitaire` */

DROP TABLE IF EXISTS `cursus_universitaire`;

CREATE TABLE `cursus_universitaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int DEFAULT NULL,
  `etablissement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annee` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pays` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diplome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mention` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_diplome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_matricule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bac_id` int DEFAULT NULL,
  `releve_id` int DEFAULT NULL,
  `dernier_diplome` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A5BC82FFE03F15C0` (`bac_id`),
  KEY `IDX_A5BC82FF5712E726` (`releve_id`),
  KEY `IDX_A5BC82FFDDEAB1A3` (`etudiant_id`),
  CONSTRAINT `FK_A5BC82FF5712E726` FOREIGN KEY (`releve_id`) REFERENCES `param_fichier` (`id`),
  CONSTRAINT `FK_A5BC82FFDDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`),
  CONSTRAINT `FK_A5BC82FFE03F15C0` FOREIGN KEY (`bac_id`) REFERENCES `param_fichier` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cursus_universitaire` */

insert  into `cursus_universitaire`(`id`,`etudiant_id`,`etablissement`,`annee`,`ville`,`pays`,`diplome`,`mention`,`numero_diplome`,`numero_matricule`,`bac_id`,`releve_id`,`dernier_diplome`) values 
(1,27,'kik','2024','daloa','ci','bac','bien','','',NULL,NULL,NULL),
(2,31,'Lycée 2','2024','Abidjan','CI','BAC','Bien','7888','7888',49,50,NULL),
(3,57,'555','22','222','222','222','555','55','788',53,54,1);

/*Table structure for table `decision` */

DROP TABLE IF EXISTS `decision`;

CREATE TABLE `decision` (
  `id` int NOT NULL AUTO_INCREMENT,
  `preinscription_id` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `commentaire` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `decision` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_84ACBE488337288` (`preinscription_id`),
  KEY `IDX_84ACBE48FB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_84ACBE488337288` FOREIGN KEY (`preinscription_id`) REFERENCES `compta_preinscription` (`id`),
  CONSTRAINT `FK_84ACBE48FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `decision` */

/*Table structure for table `dir_deliberation` */

DROP TABLE IF EXISTS `dir_deliberation`;

CREATE TABLE `dir_deliberation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `examen_id` int NOT NULL,
  `mention_id` int NOT NULL,
  `date_examen` date NOT NULL,
  `moyenne` decimal(4,2) NOT NULL,
  `total` decimal(4,0) NOT NULL,
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_637C39995C8659A` (`examen_id`),
  KEY `IDX_637C39997A4147F0` (`mention_id`),
  CONSTRAINT `FK_637C39995C8659A` FOREIGN KEY (`examen_id`) REFERENCES `param_examen` (`id`),
  CONSTRAINT `FK_637C39997A4147F0` FOREIGN KEY (`mention_id`) REFERENCES `param_mention` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dir_deliberation` */

/*Table structure for table `dir_deliberation_preinscription` */

DROP TABLE IF EXISTS `dir_deliberation_preinscription`;

CREATE TABLE `dir_deliberation_preinscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `preinscription_id` int NOT NULL,
  `deliberation_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BE76BA088337288` (`preinscription_id`),
  UNIQUE KEY `UNIQ_BE76BA08A5788A80` (`deliberation_id`),
  CONSTRAINT `FK_BE76BA088337288` FOREIGN KEY (`preinscription_id`) REFERENCES `compta_preinscription` (`id`),
  CONSTRAINT `FK_BE76BA08A5788A80` FOREIGN KEY (`deliberation_id`) REFERENCES `dir_deliberation` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dir_deliberation_preinscription` */

/*Table structure for table `dir_ligne_deliberation` */

DROP TABLE IF EXISTS `dir_ligne_deliberation`;

CREATE TABLE `dir_ligne_deliberation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `deliberation_id` int NOT NULL,
  `matiere_examen_id` int NOT NULL,
  `note` decimal(4,2) NOT NULL,
  `coefficient` smallint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A091207CA5788A80` (`deliberation_id`),
  KEY `IDX_A091207CC0C636BC` (`matiere_examen_id`),
  CONSTRAINT `FK_A091207CA5788A80` FOREIGN KEY (`deliberation_id`) REFERENCES `dir_deliberation` (`id`),
  CONSTRAINT `FK_A091207CC0C636BC` FOREIGN KEY (`matiere_examen_id`) REFERENCES `dir_matiere_examen` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dir_ligne_deliberation` */

/*Table structure for table `dir_matiere_examen` */

DROP TABLE IF EXISTS `dir_matiere_examen`;

CREATE TABLE `dir_matiere_examen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matiere_id` int NOT NULL,
  `examen_id` int NOT NULL,
  `coefficient` smallint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BBE8612BF46CD258` (`matiere_id`),
  KEY `IDX_BBE8612B5C8659A` (`examen_id`),
  CONSTRAINT `FK_BBE8612B5C8659A` FOREIGN KEY (`examen_id`) REFERENCES `param_examen` (`id`),
  CONSTRAINT `FK_BBE8612BF46CD258` FOREIGN KEY (`matiere_id`) REFERENCES `gestion_matiere` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dir_matiere_examen` */

/*Table structure for table `echeancier` */

DROP TABLE IF EXISTS `echeancier`;

CREATE TABLE `echeancier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inscription_id` int DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `montant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tota_payer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4694F00C5DAC5993` (`inscription_id`),
  CONSTRAINT `FK_4694F00C5DAC5993` FOREIGN KEY (`inscription_id`) REFERENCES `inscription` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `echeancier` */

insert  into `echeancier`(`id`,`inscription_id`,`date_creation`,`montant`,`etat`,`tota_payer`) values 
(66,29,'2024-06-05 17:07:50','152000','pas_payer','52000');

/*Table structure for table `echeancier_provisoire` */

DROP TABLE IF EXISTS `echeancier_provisoire`;

CREATE TABLE `echeancier_provisoire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bloc_echeancier_id` int DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_versement` datetime NOT NULL,
  `montant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D9683A11757F25FD` (`bloc_echeancier_id`),
  CONSTRAINT `FK_D9683A11757F25FD` FOREIGN KEY (`bloc_echeancier_id`) REFERENCES `bloc_echeancier` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `echeancier_provisoire` */

insert  into `echeancier_provisoire`(`id`,`bloc_echeancier_id`,`numero`,`date_versement`,`montant`) values 
(37,28,'1','2024-06-20 00:00:00','152000');

/*Table structure for table `encart_bac` */

DROP TABLE IF EXISTS `encart_bac`;

CREATE TABLE `encart_bac` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bac_id` int DEFAULT NULL,
  `matricule` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `annee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etudiant_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9989D06E03F15C0` (`bac_id`),
  KEY `IDX_9989D06DDEAB1A3` (`etudiant_id`),
  CONSTRAINT `FK_9989D06DDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`),
  CONSTRAINT `FK_9989D06E03F15C0` FOREIGN KEY (`bac_id`) REFERENCES `param_fichier` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `encart_bac` */

insert  into `encart_bac`(`id`,`bac_id`,`matricule`,`numero`,`annee`,`serie`,`etudiant_id`) values 
(1,52,'cc','fff','fff','dff',57);

/*Table structure for table `evaluation_controle` */

DROP TABLE IF EXISTS `evaluation_controle`;

CREATE TABLE `evaluation_controle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cour_id` int DEFAULT NULL,
  `ue_id` int DEFAULT NULL,
  `semestre_id` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `matiere_id` int DEFAULT NULL,
  `classe_id` int DEFAULT NULL,
  `annee_scolaire_id` int DEFAULT NULL,
  `date_saisie` datetime NOT NULL,
  `date_compo` datetime NOT NULL,
  `type_controle_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classe_matiere` (`classe_id`,`matiere_id`,`cour_id`),
  KEY `IDX_3774048B7942F03` (`cour_id`),
  KEY `IDX_37740485577AFDB` (`semestre_id`),
  KEY `IDX_3774048FB88E14F` (`utilisateur_id`),
  KEY `IDX_3774048F46CD258` (`matiere_id`),
  KEY `IDX_37740488F5EA509` (`classe_id`),
  KEY `IDX_37740489331C741` (`annee_scolaire_id`),
  KEY `IDX_377404862E883B1` (`ue_id`),
  KEY `IDX_37740483E90F137` (`type_controle_id`),
  CONSTRAINT `FK_37740483E90F137` FOREIGN KEY (`type_controle_id`) REFERENCES `evaluation_type_controle` (`id`),
  CONSTRAINT `FK_37740485577AFDB` FOREIGN KEY (`semestre_id`) REFERENCES `param_semestre` (`id`),
  CONSTRAINT `FK_377404862E883B1` FOREIGN KEY (`ue_id`) REFERENCES `unite_enseignement` (`id`),
  CONSTRAINT `FK_37740488F5EA509` FOREIGN KEY (`classe_id`) REFERENCES `classe` (`id`),
  CONSTRAINT `FK_37740489331C741` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annee_scolaire` (`id`),
  CONSTRAINT `FK_3774048B7942F03` FOREIGN KEY (`cour_id`) REFERENCES `cours` (`id`),
  CONSTRAINT `FK_3774048F46CD258` FOREIGN KEY (`matiere_id`) REFERENCES `gestion_matiere` (`id`),
  CONSTRAINT `FK_3774048FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_controle` */

 

/*Table structure for table `evaluation_examen_controle` */

DROP TABLE IF EXISTS `evaluation_examen_controle`;

CREATE TABLE `evaluation_examen_controle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `promotion_id` int DEFAULT NULL,
  `ue_id` int DEFAULT NULL,
  `session_id` int DEFAULT NULL,
  `type_controle_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A7703382139DF194` (`promotion_id`),
  KEY `IDX_A770338262E883B1` (`ue_id`),
  KEY `IDX_A7703382613FECDF` (`session_id`),
  KEY `IDX_A77033823E90F137` (`type_controle_id`),
  CONSTRAINT `FK_A7703382139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`),
  CONSTRAINT `FK_A77033823E90F137` FOREIGN KEY (`type_controle_id`) REFERENCES `evaluation_type_controle` (`id`),
  CONSTRAINT `FK_A7703382613FECDF` FOREIGN KEY (`session_id`) REFERENCES `param_session` (`id`),
  CONSTRAINT `FK_A770338262E883B1` FOREIGN KEY (`ue_id`) REFERENCES `unite_enseignement` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_examen_controle` */

insert  into `evaluation_examen_controle`(`id`,`promotion_id`,`ue_id`,`session_id`,`type_controle_id`) values 
(4,4,4,1,2);

/*Table structure for table `evaluation_examen_decision` */

DROP TABLE IF EXISTS `evaluation_examen_decision`;

CREATE TABLE `evaluation_examen_decision` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int DEFAULT NULL,
  `note_examen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moyenne_controle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_credit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moyenne_annuelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `promotion_id` int DEFAULT NULL,
  `decision` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2DE5B4A4DDEAB1A3` (`etudiant_id`),
  KEY `IDX_2DE5B4A4139DF194` (`promotion_id`),
  KEY `IDX_2DE5B4A4613FECDF` (`session_id`),
  CONSTRAINT `FK_2DE5B4A4139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`),
  CONSTRAINT `FK_2DE5B4A4613FECDF` FOREIGN KEY (`session_id`) REFERENCES `param_session` (`id`),
  CONSTRAINT `FK_2DE5B4A4DDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_examen_decision` */

insert  into `evaluation_examen_decision`(`id`,`etudiant_id`,`note_examen`,`moyenne_controle`,`nombre_credit`,`moyenne_annuelle`,`promotion_id`,`decision`,`session_id`) values 
(13,75,'12','12.17','30','12.07',4,'Admis',1);

/*Table structure for table `evaluation_examen_groupe_type` */

DROP TABLE IF EXISTS `evaluation_examen_groupe_type`;

CREATE TABLE `evaluation_examen_groupe_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `controle_examen_id` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `date_compo` datetime NOT NULL,
  `max` int NOT NULL,
  `date_saisie` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7121104DCF55B5C6` (`controle_examen_id`),
  KEY `IDX_7121104DFB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_7121104DCF55B5C6` FOREIGN KEY (`controle_examen_id`) REFERENCES `evaluation_examen_controle` (`id`),
  CONSTRAINT `FK_7121104DFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_examen_groupe_type` */

insert  into `evaluation_examen_groupe_type`(`id`,`controle_examen_id`,`utilisateur_id`,`date_compo`,`max`,`date_saisie`) values 
(4,4,1,'2024-06-07 00:00:00',20,'2024-06-07 16:47:25');

/*Table structure for table `evaluation_examen_note` */

DROP TABLE IF EXISTS `evaluation_examen_note`;

CREATE TABLE `evaluation_examen_note` (
  `id` int NOT NULL AUTO_INCREMENT,
  `controle_examen_id` int DEFAULT NULL,
  `etudiant_id` int DEFAULT NULL,
  `rang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exposant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moyenne_ue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moyenne_conrole` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decision` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_43FF8A7ACF55B5C6` (`controle_examen_id`),
  KEY `IDX_43FF8A7ADDEAB1A3` (`etudiant_id`),
  CONSTRAINT `FK_43FF8A7ACF55B5C6` FOREIGN KEY (`controle_examen_id`) REFERENCES `evaluation_examen_controle` (`id`),
  CONSTRAINT `FK_43FF8A7ADDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_examen_note` */

insert  into `evaluation_examen_note`(`id`,`controle_examen_id`,`etudiant_id`,`rang`,`exposant`,`moyenne_ue`,`moyenne_conrole`,`decision`) values 
(1,4,75,'1','er','12.07','12.17','Admis');

/*Table structure for table `evaluation_examen_valeur_note` */

DROP TABLE IF EXISTS `evaluation_examen_valeur_note`;

CREATE TABLE `evaluation_examen_valeur_note` (
  `id` int NOT NULL AUTO_INCREMENT,
  `note_entity_id` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_987E8CFA968298C2` (`note_entity_id`),
  KEY `IDX_987E8CFAFB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_987E8CFA968298C2` FOREIGN KEY (`note_entity_id`) REFERENCES `evaluation_examen_note` (`id`),
  CONSTRAINT `FK_987E8CFAFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_examen_valeur_note` */

insert  into `evaluation_examen_valeur_note`(`id`,`note_entity_id`,`utilisateur_id`,`note`) values 
(1,1,1,'12');

/*Table structure for table `evaluation_groupe_type` */

DROP TABLE IF EXISTS `evaluation_groupe_type`;

CREATE TABLE `evaluation_groupe_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `controle_id` int DEFAULT NULL,
  `type_evaluation_id` int DEFAULT NULL,
  `date_note` datetime DEFAULT NULL,
  `coef` int NOT NULL,
  `date_saisie` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_776FBCE9758926A6` (`controle_id`),
  KEY `IDX_776FBCE93581E173` (`type_evaluation_id`),
  CONSTRAINT `FK_776FBCE93581E173` FOREIGN KEY (`type_evaluation_id`) REFERENCES `evaluation_type_evaluation` (`id`),
  CONSTRAINT `FK_776FBCE9758926A6` FOREIGN KEY (`controle_id`) REFERENCES `evaluation_controle` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_groupe_type` */

insert  into `evaluation_groupe_type`(`id`,`controle_id`,`type_evaluation_id`,`date_note`,`coef`,`date_saisie`) values 
(1,1,1,'2024-06-06 00:00:00',20,'2024-06-06 17:20:56'),
(2,2,1,'2024-06-06 00:00:00',10,'2024-06-06 17:59:35'),
(3,2,1,'2024-06-15 00:00:00',20,'2024-06-06 18:14:51');

/*Table structure for table `evaluation_moyenne_matiere` */

DROP TABLE IF EXISTS `evaluation_moyenne_matiere`;

CREATE TABLE `evaluation_moyenne_matiere` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matiere_id` int DEFAULT NULL,
  `etudiant_id` int DEFAULT NULL,
  `ue_id` int DEFAULT NULL,
  `moyenne` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valide` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F1D528B1F46CD258` (`matiere_id`),
  KEY `IDX_F1D528B1DDEAB1A3` (`etudiant_id`),
  KEY `IDX_F1D528B162E883B1` (`ue_id`),
  CONSTRAINT `FK_F1D528B162E883B1` FOREIGN KEY (`ue_id`) REFERENCES `unite_enseignement` (`id`),
  CONSTRAINT `FK_F1D528B1DDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`),
  CONSTRAINT `FK_F1D528B1F46CD258` FOREIGN KEY (`matiere_id`) REFERENCES `gestion_matiere` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_moyenne_matiere` */

insert  into `evaluation_moyenne_matiere`(`id`,`matiere_id`,`etudiant_id`,`ue_id`,`moyenne`,`valide`) values 
(1,1,75,4,'13.333333333333','Oui'),
(2,2,75,4,'11','Oui');

/*Table structure for table `evaluation_note` */

DROP TABLE IF EXISTS `evaluation_note`;

CREATE TABLE `evaluation_note` (
  `id` int NOT NULL AUTO_INCREMENT,
  `controle_id` int DEFAULT NULL,
  `etudiant_id` int DEFAULT NULL,
  `moyenne_matiere` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exposant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_82FBB5AC758926A6` (`controle_id`),
  KEY `IDX_82FBB5ACDDEAB1A3` (`etudiant_id`),
  CONSTRAINT `FK_82FBB5AC758926A6` FOREIGN KEY (`controle_id`) REFERENCES `evaluation_controle` (`id`),
  CONSTRAINT `FK_82FBB5ACDDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_note` */

insert  into `evaluation_note`(`id`,`controle_id`,`etudiant_id`,`moyenne_matiere`,`rang`,`exposant`) values 
(1,1,75,'11','1','er'),
(2,2,75,'13.333333333333','1','er');

/*Table structure for table `evaluation_type_controle` */

DROP TABLE IF EXISTS `evaluation_type_controle`;

CREATE TABLE `evaluation_type_controle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coef` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_type_controle` */

insert  into `evaluation_type_controle`(`id`,`libelle`,`code`,`coef`) values 
(1,'Controle continu','CC','40'),
(2,'Examen','EXA','60');

/*Table structure for table `evaluation_type_evaluation` */

DROP TABLE IF EXISTS `evaluation_type_evaluation`;

CREATE TABLE `evaluation_type_evaluation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_type_evaluation` */

insert  into `evaluation_type_evaluation`(`id`,`code`,`libelle`) values 
(1,'TD','Travaux dirigés'),
(2,'DV','Devoir');

/*Table structure for table `evaluation_valeur_note` */

DROP TABLE IF EXISTS `evaluation_valeur_note`;

CREATE TABLE `evaluation_valeur_note` (
  `id` int NOT NULL AUTO_INCREMENT,
  `note_entity_id` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9E30205E968298C2` (`note_entity_id`),
  KEY `IDX_9E30205EFB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_9E30205E968298C2` FOREIGN KEY (`note_entity_id`) REFERENCES `evaluation_note` (`id`),
  CONSTRAINT `FK_9E30205EFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evaluation_valeur_note` */

insert  into `evaluation_valeur_note`(`id`,`note_entity_id`,`utilisateur_id`,`note`) values 
(1,1,1,'11'),
(2,2,1,'8'),
(3,2,1,'12');

/*Table structure for table `frais_bloc` */

DROP TABLE IF EXISTS `frais_bloc`;

CREATE TABLE `frais_bloc` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_frais_id` int DEFAULT NULL,
  `bloc_echeancier_id` int DEFAULT NULL,
  `montant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3280333272AE4A38` (`type_frais_id`),
  KEY `IDX_32803332757F25FD` (`bloc_echeancier_id`),
  CONSTRAINT `FK_3280333272AE4A38` FOREIGN KEY (`type_frais_id`) REFERENCES `param_type_frais` (`id`),
  CONSTRAINT `FK_32803332757F25FD` FOREIGN KEY (`bloc_echeancier_id`) REFERENCES `bloc_echeancier` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `frais_bloc` */

insert  into `frais_bloc`(`id`,`type_frais_id`,`bloc_echeancier_id`,`montant`) values 
(47,1,28,'100000'),
(48,2,28,'50000'),
(49,3,28,'2000');

/*Table structure for table `gestion_frais` */

DROP TABLE IF EXISTS `gestion_frais`;

CREATE TABLE `gestion_frais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_frais_id` int NOT NULL,
  `promotion_id` int NOT NULL,
  `montant` decimal(9,0) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_685F237B72AE4A38` (`type_frais_id`),
  KEY `IDX_685F237B139DF194` (`promotion_id`),
  CONSTRAINT `FK_685F237B139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`),
  CONSTRAINT `FK_685F237B72AE4A38` FOREIGN KEY (`type_frais_id`) REFERENCES `param_type_frais` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `gestion_frais` */

insert  into `gestion_frais`(`id`,`type_frais_id`,`promotion_id`,`montant`) values 
(28,1,4,100000),
(29,2,4,50000),
(30,3,4,2000),
(31,1,5,2500),
(32,2,5,40000),
(33,3,5,100000);

/*Table structure for table `gestion_frais_inscription_etudiant` */

DROP TABLE IF EXISTS `gestion_frais_inscription_etudiant`;

CREATE TABLE `gestion_frais_inscription_etudiant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_frais_id` int NOT NULL,
  `inscription_id` int NOT NULL,
  `montant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4B11185272AE4A38` (`type_frais_id`),
  KEY `IDX_4B1118525DAC5993` (`inscription_id`),
  CONSTRAINT `FK_4B1118525DAC5993` FOREIGN KEY (`inscription_id`) REFERENCES `inscription` (`id`),
  CONSTRAINT `FK_4B11185272AE4A38` FOREIGN KEY (`type_frais_id`) REFERENCES `param_type_frais` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `gestion_frais_inscription_etudiant` */

insert  into `gestion_frais_inscription_etudiant`(`id`,`type_frais_id`,`inscription_id`,`montant`) values 
(36,1,29,'100000'),
(37,2,29,'50000'),
(38,3,29,'2000');

/*Table structure for table `gestion_matiere` */

DROP TABLE IF EXISTS `gestion_matiere`;

CREATE TABLE `gestion_matiere` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_matiere_id` int NOT NULL,
  `code` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre` smallint NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_384AC6B377153098` (`code`),
  KEY `IDX_384AC6B3E96F047D` (`type_matiere_id`),
  CONSTRAINT `FK_384AC6B3E96F047D` FOREIGN KEY (`type_matiere_id`) REFERENCES `param_type_matiere` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `gestion_matiere` */

insert  into `gestion_matiere`(`id`,`type_matiere_id`,`code`,`ordre`,`libelle`) values 
(1,1,'FR',1,'Français'),
(2,2,'MATHS',2,'Mathématiques');

/*Table structure for table `info_etudiant` */

DROP TABLE IF EXISTS `info_etudiant`;

CREATE TABLE `info_etudiant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int DEFAULT NULL,
  `habite_avec` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tuteur_nom_prenoms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tuteur_fonction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tuteur_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tuteur_domicile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tuteur_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corres_nom_prenoms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corres_fonction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corres_contacts` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corres_domicile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corres_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pere_nom_prenoms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pere_fonction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pere_contacts` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pere_domicile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mere_nom_prenoms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mere_fonction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mere_contacts` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mere_domicile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pere_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mere_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_664B5989DDEAB1A3` (`etudiant_id`),
  CONSTRAINT `FK_664B5989DDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `info_etudiant` */

insert  into `info_etudiant`(`id`,`etudiant_id`,`habite_avec`,`tuteur_nom_prenoms`,`tuteur_fonction`,`tuteur_contact`,`tuteur_domicile`,`tuteur_email`,`corres_nom_prenoms`,`corres_fonction`,`corres_contacts`,`corres_domicile`,`corres_email`,`pere_nom_prenoms`,`pere_fonction`,`pere_contacts`,`pere_domicile`,`mere_nom_prenoms`,`mere_fonction`,`mere_contacts`,`mere_domicile`,`pere_email`,`mere_email`) values 
(2,31,'pere','Hamed Konate',NULL,NULL,NULL,'konatenhamed@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(3,45,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(4,46,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(5,47,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(6,48,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(7,49,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(8,50,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(9,51,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(10,52,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(11,53,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(12,54,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(13,55,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(14,56,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(15,57,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(16,59,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(17,60,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(18,61,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(20,63,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(21,64,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(22,65,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(23,66,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(24,67,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(25,68,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(26,69,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(27,70,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(28,71,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(29,72,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(30,73,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(31,74,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(32,75,'pere',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `inscription` */

DROP TABLE IF EXISTS `inscription`;

CREATE TABLE `inscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_inscription` datetime NOT NULL,
  `montant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_utilisateur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_paiement` datetime DEFAULT NULL,
  `etudiant_id` int DEFAULT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caissiere_id` int DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` longtext COLLATE utf8mb4_unicode_ci,
  `total_paye` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `classe_id` int DEFAULT NULL,
  `promotion_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5E90F6D6DDEAB1A3` (`etudiant_id`),
  KEY `IDX_5E90F6D623BD48D5` (`caissiere_id`),
  KEY `IDX_5E90F6D68F5EA509` (`classe_id`),
  KEY `IDX_5E90F6D6139DF194` (`promotion_id`),
  CONSTRAINT `FK_5E90F6D6139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`),
  CONSTRAINT `FK_5E90F6D623BD48D5` FOREIGN KEY (`caissiere_id`) REFERENCES `user_utilisateur` (`id`),
  CONSTRAINT `FK_5E90F6D68F5EA509` FOREIGN KEY (`classe_id`) REFERENCES `classe` (`id`),
  CONSTRAINT `FK_5E90F6D6DDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `inscription` */

insert  into `inscription`(`id`,`date_inscription`,`montant`,`code_utilisateur`,`date_paiement`,`etudiant_id`,`etat`,`caissiere_id`,`code`,`commentaire`,`total_paye`,`classe_id`,`promotion_id`) values 
(29,'2024-06-05 00:00:00','152000','admin@gmail.com',NULL,75,'valide',1,'N001-24-001',NULL,'52000',10,4);

/*Table structure for table `log_validation` */

DROP TABLE IF EXISTS `log_validation`;

CREATE TABLE `log_validation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `date_creation` datetime NOT NULL,
  `class_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `object` int NOT NULL,
  `etat` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `statut` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_1CBAF7D5FB88E14F` (`utilisateur_id`),
  KEY `idx_class` (`class_name`),
  KEY `idx_object` (`object`),
  KEY `idx_etat` (`etat`),
  CONSTRAINT `FK_1CBAF7D5FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user_utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `log_validation` */

/*Table structure for table `matiere_ue` */

DROP TABLE IF EXISTS `matiere_ue`;

CREATE TABLE `matiere_ue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unite_enseignement_id` int DEFAULT NULL,
  `matiere_id` int DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `note_eliminatoire` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moyenne_validation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_651CBF9318DEEBA5` (`unite_enseignement_id`),
  KEY `IDX_651CBF93F46CD258` (`matiere_id`),
  CONSTRAINT `FK_651CBF9318DEEBA5` FOREIGN KEY (`unite_enseignement_id`) REFERENCES `unite_enseignement` (`id`),
  CONSTRAINT `FK_651CBF93F46CD258` FOREIGN KEY (`matiere_id`) REFERENCES `gestion_matiere` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `matiere_ue` */

insert  into `matiere_ue`(`id`,`unite_enseignement_id`,`matiere_id`,`visible`,`note_eliminatoire`,`moyenne_validation`) values 
(3,4,1,1,'5','10'),
(4,4,2,1,'6','10'),
(5,5,1,1,'5','10'),
(6,5,2,1,'5','10'),
(7,6,1,1,'5','12');

/*Table structure for table `niveau_etudiant` */

DROP TABLE IF EXISTS `niveau_etudiant`;

CREATE TABLE `niveau_etudiant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int DEFAULT NULL,
  `niveau_id` int DEFAULT NULL,
  `filiere_id` int DEFAULT NULL,
  `date` datetime NOT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_validation` datetime DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_paiement` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C878E250DDEAB1A3` (`etudiant_id`),
  KEY `IDX_C878E250B3E9C81` (`niveau_id`),
  KEY `IDX_C878E250180AA129` (`filiere_id`),
  CONSTRAINT `FK_C878E250180AA129` FOREIGN KEY (`filiere_id`) REFERENCES `param_filiere` (`id`),
  CONSTRAINT `FK_C878E250B3E9C81` FOREIGN KEY (`niveau_id`) REFERENCES `param_niveau` (`id`),
  CONSTRAINT `FK_C878E250DDEAB1A3` FOREIGN KEY (`etudiant_id`) REFERENCES `user_etudiant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `niveau_etudiant` */

insert  into `niveau_etudiant`(`id`,`etudiant_id`,`niveau_id`,`filiere_id`,`date`,`etat`,`date_validation`,`code`,`motif`,`date_paiement`) values 
(7,6,2,1,'2023-11-14 11:35:04','valider_paye','2023-11-14 18:33:33','N002-23-003',NULL,'2023-11-17 00:00:00'),
(8,6,1,3,'2023-11-14 11:37:06','attente_validation',NULL,'','Je suis désolé monsieur',NULL),
(9,13,2,1,'2023-11-17 12:17:39','attente_validation',NULL,NULL,NULL,NULL),
(10,14,3,1,'2023-11-17 12:34:31','attente_paiement',NULL,NULL,NULL,NULL),
(11,15,1,1,'2023-11-17 14:43:16','attente_validation',NULL,NULL,NULL,NULL),
(12,16,1,1,'2023-11-20 08:03:39','EN ATTENTE DE VALIDATION',NULL,NULL,NULL,NULL),
(13,19,2,1,'2023-11-23 13:39:03','attente_validation',NULL,NULL,NULL,NULL),
(14,20,5,2,'2023-11-23 13:40:50','attente_validation',NULL,NULL,NULL,NULL),
(15,21,1,1,'2023-11-23 13:43:57','attente_validation',NULL,NULL,NULL,NULL);



/*Table structure for table `param_examen` */

DROP TABLE IF EXISTS `param_examen`;

CREATE TABLE `param_examen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `promotion_id` int NOT NULL,
  `libelle` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_examen` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_99CCC5A77153098` (`code`),
  KEY `IDX_99CCC5A139DF194` (`promotion_id`),
  CONSTRAINT `FK_99CCC5A139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `param_examen` */








/*Table structure for table `param_info_niveau` */

DROP TABLE IF EXISTS `param_info_niveau`;

CREATE TABLE `param_info_niveau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `promotion_id` int NOT NULL,
  `nature` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6FCF4436139DF194` (`promotion_id`),
  CONSTRAINT `FK_6FCF4436139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `param_info_niveau` */









/*Table structure for table `param_promotion` */

DROP TABLE IF EXISTS `param_promotion`;

CREATE TABLE `param_promotion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` int DEFAULT NULL,
  `responsable_id` int NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int NOT NULL,
  `niveau_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5D49313977153098` (`code`),
  UNIQUE KEY `numero_niveau` (`numero`,`niveau_id`),
  KEY `IDX_5D4931399331C741` (`annee_scolaire_id`),
  KEY `IDX_5D49313953C59D72` (`responsable_id`),
  KEY `IDX_5D493139B3E9C81` (`niveau_id`),
  CONSTRAINT `FK_5D49313953C59D72` FOREIGN KEY (`responsable_id`) REFERENCES `user_employe` (`id`),
  CONSTRAINT `FK_5D4931399331C741` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annee_scolaire` (`id`),
  CONSTRAINT `FK_5D493139B3E9C81` FOREIGN KEY (`niveau_id`) REFERENCES `param_niveau` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `param_promotion` */

insert  into `param_promotion`(`id`,`annee_scolaire_id`,`responsable_id`,`code`,`libelle`,`numero`,`niveau_id`) values 
(4,1,2,'fifkf','Premiere promo',1,1),
(5,1,40,'rrrr','Deuxieme promo',1,3);



















/*Table structure for table `unite_enseignement` */

DROP TABLE IF EXISTS `unite_enseignement`;

CREATE TABLE `unite_enseignement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `semestre_id` int DEFAULT NULL,
  `promotion_id` int DEFAULT NULL,
  `code_ue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coef` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `volume_horaire` int NOT NULL,
  `total_credit` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_46D07C4F5577AFDB` (`semestre_id`),
  KEY `IDX_46D07C4F139DF194` (`promotion_id`),
  CONSTRAINT `FK_46D07C4F139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `param_promotion` (`id`),
  CONSTRAINT `FK_46D07C4F5577AFDB` FOREIGN KEY (`semestre_id`) REFERENCES `param_semestre` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `unite_enseignement` */

insert  into `unite_enseignement`(`id`,`semestre_id`,`promotion_id`,`code_ue`,`libelle`,`coef`,`attribut`,`volume_horaire`,`total_credit`) values 
(4,1,4,'888','Premiere unite','10','Majeur',20,30),
(5,1,5,'fff','Deuxieme promo','10','Majeur',60,30),
(6,1,4,'ed','ddd','5','Majeur',12,20),
(7,1,4,'dd','4444','5','Mineur',0,5);






/*Table structure for table `user_etudiant` */

DROP TABLE IF EXISTS `user_etudiant`;

CREATE TABLE `user_etudiant` (
  `id` int NOT NULL,
  `filiere_id` int DEFAULT NULL,
  `ville` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `boite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employeur` tinyint(1) DEFAULT NULL,
  `bailleur` tinyint(1) DEFAULT NULL,
  `parent` tinyint(1) DEFAULT NULL,
  `autre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `radio` tinyint(1) DEFAULT NULL,
  `presse` tinyint(1) DEFAULT NULL,
  `affiche` tinyint(1) DEFAULT NULL,
  `ministere` tinyint(1) DEFAULT NULL,
  `mailing` tinyint(1) DEFAULT NULL,
  `site_web` tinyint(1) DEFAULT NULL,
  `vous_meme` tinyint(1) DEFAULT NULL,
  `professeur` tinyint(1) DEFAULT NULL,
  `ami_collegue` tinyint(1) DEFAULT NULL,
  `autre_existence` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pays_id` int DEFAULT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut_travail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `travail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut_etudiant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nationalite_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_91F3C6E3180AA129` (`filiere_id`),
  KEY `IDX_91F3C6E3A6E44244` (`pays_id`),
  KEY `IDX_91F3C6E31B063272` (`nationalite_id`),
  CONSTRAINT `FK_91F3C6E3180AA129` FOREIGN KEY (`filiere_id`) REFERENCES `param_filiere` (`id`),
  CONSTRAINT `FK_91F3C6E31B063272` FOREIGN KEY (`nationalite_id`) REFERENCES `param_nationalite` (`id`),
  CONSTRAINT `FK_91F3C6E3A6E44244` FOREIGN KEY (`pays_id`) REFERENCES `pays` (`id`),
  CONSTRAINT `FK_91F3C6E3BF396750` FOREIGN KEY (`id`) REFERENCES `user_personne` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user_etudiant` */

insert  into `user_etudiant`(`id`,`filiere_id`,`ville`,`adresse`,`boite`,`fax`,`employeur`,`bailleur`,`parent`,`autre`,`radio`,`presse`,`affiche`,`ministere`,`mailing`,`site_web`,`vous_meme`,`professeur`,`ami_collegue`,`autre_existence`,`pays_id`,`etat`,`numero_whatsapp`,`statut_travail`,`travail`,`statut_etudiant`,`nationalite_id`) values 
(3,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(4,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(5,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(6,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(7,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(8,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(9,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(10,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(11,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(12,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(13,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(14,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(15,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(16,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(19,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(26,NULL,'Abidjan','BP  Abidjan 0',NULL,NULL,0,0,0,'Test',0,0,0,0,0,0,1,0,0,NULL,57,'pas_complet',NULL,'',NULL,'',NULL),
(27,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,0,0,0,0,0,0,0,0,0,NULL,1,'pas_complet',NULL,'',NULL,'',NULL),
(28,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(30,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(31,NULL,'Abidjan','BP  Abidjan 06','225',NULL,0,0,0,'dsss',0,0,0,0,0,0,1,0,0,NULL,2,'complete',NULL,'non',NULL,'non',NULL),
(32,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'',NULL,'',NULL),
(41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'non',NULL,'non',NULL),
(42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'non',NULL,'non',NULL),
(43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pas_complet',NULL,'non',NULL,'non',NULL),
(45,NULL,'fff','ff','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,1,'complete','fffff','non',NULL,'non',NULL),
(46,NULL,'fff','dff','fff',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,1,'complete','fff','non',NULL,'non',NULL),
(47,NULL,'fff','dff','fff',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,1,'complete','fff','non',NULL,'non',NULL),
(48,NULL,'ff','ff','ff',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','ff','non',NULL,'non',NULL),
(49,NULL,'ff','ff','ffdf',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','fsddsfs','non',NULL,'non',NULL),
(50,NULL,'ff','ff','ffdf',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','fsddsfs','non',NULL,'non',NULL),
(51,NULL,'dhuhus','uuieh','ididud',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,3,'complete','yutyuistuiet','non',NULL,'non',NULL),
(52,NULL,'dhuhus','uuieh','ididud',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,3,'complete','yutyuistuiet','non',NULL,'non',NULL),
(53,NULL,'dhuhus','uuieh','ididud',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,3,'complete','yutyuistuiet','non',NULL,'non',NULL),
(54,NULL,'dhuhus','uuieh','ididud',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,3,'complete','yutyuistuiet','non',NULL,'non',NULL),
(55,NULL,'DAMAS','uuieh','ididud',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,3,'complete','yutyuistuiet','non',NULL,'non',NULL),
(56,NULL,'Paris','eee','ddd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,3,'complete','ddd','non',NULL,'non',NULL),
(57,NULL,'leo','Kkk','dd',NULL,1,1,1,NULL,1,1,1,1,1,1,1,1,1,NULL,2,'complete','ddddd','non',NULL,'non',1),
(59,NULL,'rrr','rrr','ttty',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','etyrt','non',NULL,'non',NULL),
(60,NULL,'uyoyr','uuyeuye','ujyriuz',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','uyiuzyurzr','non',NULL,'non',NULL),
(61,NULL,'fff','ffff','fff',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','fff','non',NULL,'non',NULL),
(63,NULL,'uyuiyd','uyeuye','jhjhd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','uuyur','non',NULL,'non',NULL),
(64,NULL,'ttt','tt','rrr',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,4,'complete','rrr','non',NULL,'non',NULL),
(65,NULL,'dd','dd','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,1,'complete','dd','non',NULL,'non',NULL),
(66,NULL,'ddd','soro','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,4,'complete','dfrr','non',NULL,'non',NULL),
(67,NULL,'dd','hh','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,3,'complete','dfrr','non',NULL,'non',NULL),
(68,NULL,'dd','hh','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,3,'complete','dfrr','non',NULL,'non',NULL),
(69,NULL,'ddd','soro','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','dfrr','non',NULL,'non',NULL),
(70,NULL,'ddd','soro','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','dfrr','non',NULL,'non',NULL),
(71,NULL,'ddd','soro','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,1,'complete','dfrr','non',NULL,'non',NULL),
(72,NULL,'dd','dd','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','dd','non',NULL,'non',NULL),
(73,NULL,'dd','dd','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,2,'complete','dd','non',NULL,'non',NULL),
(74,NULL,'dd','dd','dd',NULL,0,0,0,NULL,0,0,0,0,0,0,0,0,0,NULL,5,'complete','dfrr','non',NULL,'non',NULL),
(75,NULL,'sdd','dd','ddd',NULL,1,0,0,NULL,0,0,0,1,0,0,0,0,0,NULL,2,'complete','78887541','non',NULL,'non',NULL);










/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
