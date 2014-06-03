SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `prevarisc` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `prevarisc` ;

-- -----------------------------------------------------
-- Table `prevarisc`.`adressecommune`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`adressecommune` (
  `NUMINSEE_COMMUNE` CHAR(5) NOT NULL,
  `LIBELLE_COMMUNE` VARCHAR(60) NOT NULL,
  `CODEPOSTAL_COMMUNE` VARCHAR(5) NOT NULL,
  `ID_UTILISATEURINFORMATIONS` BIGINT(20) NOT NULL,
  PRIMARY KEY (`NUMINSEE_COMMUNE`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`adresseruetype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`adresseruetype` (
  `ID_RUETYPE` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_RUETYPE` VARCHAR(32) NOT NULL COMMENT 'Exemple : BOULEVARD',
  `ABREVIATION_RUETYPE` VARCHAR(16) NOT NULL COMMENT 'Exemple : BLVD',
  PRIMARY KEY (`ID_RUETYPE`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`adresserue`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`adresserue` (
  `ID_RUE` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_RUE` VARCHAR(255) NOT NULL COMMENT 'Pour le moment, le nom du type de la rue doit être cité dans le nom de la rue. (RUE DES ACACIAS)',
  `ID_RUETYPE` BIGINT(20) UNSIGNED NOT NULL,
  `NUMINSEE_COMMUNE` CHAR(5) NOT NULL,
  PRIMARY KEY (`ID_RUE`),
  INDEX `fk_adresserue_adresseruetype_idx` (`ID_RUETYPE` ASC),
  INDEX `fk_adresserue_adressecommune1_idx` (`NUMINSEE_COMMUNE` ASC),
  CONSTRAINT `fk_adresserue_adresseruetype`
    FOREIGN KEY (`ID_RUETYPE`)
    REFERENCES `prevarisc`.`adresseruetype` (`ID_RUETYPE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_adresserue_adressecommune1`
    FOREIGN KEY (`NUMINSEE_COMMUNE`)
    REFERENCES `prevarisc`.`adressecommune` (`NUMINSEE_COMMUNE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`avis`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`avis` (
  `ID_AVIS` INT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_AVIS` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`ID_AVIS`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`categorie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`categorie` (
  `ID_CATEGORIE` INT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_CATEGORIE` VARCHAR(15) NULL DEFAULT NULL,
  `COMMENTAIRE_CATEGORIE` VARCHAR(35) NULL DEFAULT NULL,
  PRIMARY KEY (`ID_CATEGORIE`))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`classe`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`classe` (
  `ID_CLASSE` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_CLASSE` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`ID_CLASSE`))
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissiontype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissiontype` (
  `ID_COMMISSIONTYPE` INT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_COMMISSIONTYPE` VARCHAR(50) NULL,
  PRIMARY KEY (`ID_COMMISSIONTYPE`))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commission` (
  `ID_COMMISSION` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_COMMISSION` VARCHAR(50) NOT NULL DEFAULT 'Nom de la commission',
  `DOCUMENT_CR` VARCHAR(255) NULL,
  `ID_COMMISSIONTYPE` INT(2) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_COMMISSION`),
  INDEX `fk_commission_commissiontype1_idx` (`ID_COMMISSIONTYPE` ASC),
  CONSTRAINT `fk_commission_commissiontype1`
    FOREIGN KEY (`ID_COMMISSIONTYPE`)
    REFERENCES `prevarisc`.`commissiontype` (`ID_COMMISSIONTYPE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`utilisateurcivilite`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`utilisateurcivilite` (
  `ID_UTILISATEURCIVILITE` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_UTILISATEURCIVILITE` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_UTILISATEURCIVILITE`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`fonction`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`fonction` (
  `ID_FONCTION` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_FONCTION` VARCHAR(50) CHARACTER SET 'latin1' NOT NULL,
  PRIMARY KEY (`ID_FONCTION`))
ENGINE = InnoDB
AUTO_INCREMENT = 100
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`utilisateurinformations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`utilisateurinformations` (
  `ID_UTILISATEURINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `NOM_UTILISATEURINFORMATIONS` VARCHAR(50) NULL,
  `PRENOM_UTILISATEURINFORMATIONS` VARCHAR(50) NULL,
  `TELFIXE_UTILISATEURINFORMATIONS` VARCHAR(20) NULL DEFAULT NULL,
  `TELPORTABLE_UTILISATEURINFORMATIONS` VARCHAR(20) NULL,
  `TELFAX_UTILISATEURINFORMATIONS` VARCHAR(20) NULL,
  `MAIL_UTILISATEURINFORMATIONS` VARCHAR(50) NULL DEFAULT NULL,
  `SOCIETE_UTILISATEURINFORMATIONS` VARCHAR(100) NULL,
  `NUMEROADRESSE_UTILISATEURINFORMATIONS` VARCHAR(10) NULL DEFAULT NULL,
  `RUEADRESSE_UTILISATEURINFORMATIONS` VARCHAR(255) NULL,
  `CPADRESSE_UTILISATEURINFORMATIONS` VARCHAR(5) NULL DEFAULT NULL,
  `VILLEADRESSE_UTILISATEURINFORMATIONS` VARCHAR(255) NULL DEFAULT NULL,
  `WEB_UTILISATEURINFORMATIONS` VARCHAR(100) NULL,
  `OBS_UTILISATEURINFORMATIONS` TEXT NULL,
  `DATE_PRV2` TIMESTAMP NULL,
  `DATE_RECYCLAGE` TIMESTAMP NULL,
  `DATE_SID` TIMESTAMP NULL,
  `ID_UTILISATEURCIVILITE` INT(11) UNSIGNED NULL,
  `ID_FONCTION` BIGINT(20) UNSIGNED NULL,
  PRIMARY KEY (`ID_UTILISATEURINFORMATIONS`),
  INDEX `fk_utilisateurinformations_utilisateurcivilite1_idx` (`ID_UTILISATEURCIVILITE` ASC),
  INDEX `fk_utilisateurinformations_fonction1_idx` (`ID_FONCTION` ASC),
  CONSTRAINT `fk_utilisateurinformations_utilisateurcivilite1`
    FOREIGN KEY (`ID_UTILISATEURCIVILITE`)
    REFERENCES `prevarisc`.`utilisateurcivilite` (`ID_UTILISATEURCIVILITE`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_utilisateurinformations_fonction1`
    FOREIGN KEY (`ID_FONCTION`)
    REFERENCES `prevarisc`.`fonction` (`ID_FONCTION`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissioncontact`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissioncontact` (
  `ID_COMMISSION` BIGINT(20) UNSIGNED NOT NULL,
  `ID_UTILISATEURINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_COMMISSION`, `ID_UTILISATEURINFORMATIONS`),
  INDEX `fk_commissioncontact_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS` ASC),
  CONSTRAINT `fk_commissioncontact_utilisateurinformations1`
    FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`)
    REFERENCES `prevarisc`.`utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_commissioncontact_commission1`
    FOREIGN KEY (`ID_COMMISSION`)
    REFERENCES `prevarisc`.`commission` (`ID_COMMISSION`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionmembre`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionmembre` (
  `ID_COMMISSIONMEMBRE` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_COMMISSIONMEMBRE` VARCHAR(255) NOT NULL,
  `ID_GROUPEMENT` INT(11) NULL DEFAULT NULL,
  `ID_UTILISATEURINFORMATIONS` INT(11) NULL,
  `PRESENCE_COMMISSIONMEMBRE` INT(11) NOT NULL,
  `COURRIER_CONVOCATIONVISITE` VARCHAR(255) NULL DEFAULT NULL,
  `COURRIER_CONVOCATIONSALLE` VARCHAR(255) NULL,
  `COURRIER_ODJ` VARCHAR(255) NULL,
  `COURRIER_PV` VARCHAR(255) NULL,
  `ID_COMMISSION` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`),
  INDEX `ID_COMMISSION` (`ID_GROUPEMENT` ASC, `ID_UTILISATEURINFORMATIONS` ASC),
  INDEX `ID_COURRIER_BE` (`COURRIER_CONVOCATIONVISITE` ASC, `COURRIER_CONVOCATIONSALLE` ASC),
  INDEX `fk_commissionmembre_commission1_idx` (`ID_COMMISSION` ASC),
  CONSTRAINT `fk_commissionmembre_commission1`
    FOREIGN KEY (`ID_COMMISSION`)
    REFERENCES `prevarisc`.`commission` (`ID_COMMISSION`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionmembrecategorie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionmembrecategorie` (
  `ID_COMMISSIONMEMBRE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_CATEGORIE` INT(1) UNSIGNED NOT NULL,
  INDEX `fk_commissionmembrecategorie_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE` ASC),
  INDEX `fk_commissionmembrecategorie_categorie1_idx` (`ID_CATEGORIE` ASC),
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`, `ID_CATEGORIE`),
  CONSTRAINT `fk_commissionmembrecategorie_commissionmembre1`
    FOREIGN KEY (`ID_COMMISSIONMEMBRE`)
    REFERENCES `prevarisc`.`commissionmembre` (`ID_COMMISSIONMEMBRE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionmembrecategorie_categorie1`
    FOREIGN KEY (`ID_CATEGORIE`)
    REFERENCES `prevarisc`.`categorie` (`ID_CATEGORIE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionmembreclasse`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionmembreclasse` (
  `ID_COMMISSIONMEMBRE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_CLASSE` INT(11) UNSIGNED NOT NULL,
  INDEX `fk_commissionmembreclasse_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE` ASC),
  INDEX `fk_commissionmembreclasse_classe1_idx` (`ID_CLASSE` ASC),
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`, `ID_CLASSE`),
  CONSTRAINT `fk_commissionmembreclasse_commissionmembre1`
    FOREIGN KEY (`ID_COMMISSIONMEMBRE`)
    REFERENCES `prevarisc`.`commissionmembre` (`ID_COMMISSIONMEMBRE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionmembreclasse_classe1`
    FOREIGN KEY (`ID_CLASSE`)
    REFERENCES `prevarisc`.`classe` (`ID_CLASSE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossiertype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossiertype` (
  `ID_DOSSIERTYPE` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOSSIERTYPE` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`ID_DOSSIERTYPE`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossiernatureliste`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossiernatureliste` (
  `ID_DOSSIERNATURE` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOSSIERNATURE` VARCHAR(100) NOT NULL,
  `ID_DOSSIERTYPE` BIGINT(20) UNSIGNED NOT NULL,
  `ORDRE` INT NULL DEFAULT NULL,
  PRIMARY KEY (`ID_DOSSIERNATURE`),
  INDEX `fk_dossiernatureliste_dossiertype1_idx` (`ID_DOSSIERTYPE` ASC),
  CONSTRAINT `fk_dossiernatureliste_dossiertype1`
    FOREIGN KEY (`ID_DOSSIERTYPE`)
    REFERENCES `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 47;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionmembredossiernature`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionmembredossiernature` (
  `ID_COMMISSIONMEMBRE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_DOSSIERNATURE` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`, `ID_DOSSIERNATURE`),
  INDEX `fk_commissionmembredossiernature_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE` ASC),
  INDEX `fk_commissionmembredossiernature_dossiernatureliste1_idx` (`ID_DOSSIERNATURE` ASC),
  CONSTRAINT `fk_commissionmembredossiernature_commissionmembre1`
    FOREIGN KEY (`ID_COMMISSIONMEMBRE`)
    REFERENCES `prevarisc`.`commissionmembre` (`ID_COMMISSIONMEMBRE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionmembredossiernature_dossiernatureliste1`
    FOREIGN KEY (`ID_DOSSIERNATURE`)
    REFERENCES `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionmembredossiertype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionmembredossiertype` (
  `ID_COMMISSIONMEMBRE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_DOSSIERTYPE` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_DOSSIERTYPE`, `ID_COMMISSIONMEMBRE`),
  INDEX `fk_commissionmembredossiertype_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE` ASC),
  INDEX `fk_commissionmembredossiertype_dossiertype1_idx` (`ID_DOSSIERTYPE` ASC),
  CONSTRAINT `fk_commissionmembredossiertype_commissionmembre1`
    FOREIGN KEY (`ID_COMMISSIONMEMBRE`)
    REFERENCES `prevarisc`.`commissionmembre` (`ID_COMMISSIONMEMBRE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionmembredossiertype_dossiertype1`
    FOREIGN KEY (`ID_DOSSIERTYPE`)
    REFERENCES `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionregle`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionregle` (
  `ID_REGLE` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ID_GROUPEMENT` BIGINT(20) NULL,
  `ID_COMMISSION` BIGINT(20) UNSIGNED NOT NULL,
  `NUMINSEE_COMMUNE` CHAR(5) NULL,
  PRIMARY KEY (`ID_REGLE`),
  INDEX `ID_GROUPEMENT` (`ID_GROUPEMENT` ASC),
  INDEX `fk_commissionregle_commission1_idx` (`ID_COMMISSION` ASC),
  INDEX `fk_commissionregle_adressecommune1_idx` (`NUMINSEE_COMMUNE` ASC),
  CONSTRAINT `fk_commissionregle_commission1`
    FOREIGN KEY (`ID_COMMISSION`)
    REFERENCES `prevarisc`.`commission` (`ID_COMMISSION`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionregle_adressecommune1`
    FOREIGN KEY (`NUMINSEE_COMMUNE`)
    REFERENCES `prevarisc`.`adressecommune` (`NUMINSEE_COMMUNE`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionreglecategorie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionreglecategorie` (
  `ID_REGLE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_CATEGORIE` INT(1) UNSIGNED NOT NULL,
  INDEX `fk_commissionreglecategorie_commissionregle1_idx` (`ID_REGLE` ASC),
  INDEX `fk_commissionreglecategorie_categorie1_idx` (`ID_CATEGORIE` ASC),
  CONSTRAINT `fk_commissionreglecategorie_commissionregle1`
    FOREIGN KEY (`ID_REGLE`)
    REFERENCES `prevarisc`.`commissionregle` (`ID_REGLE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionreglecategorie_categorie1`
    FOREIGN KEY (`ID_CATEGORIE`)
    REFERENCES `prevarisc`.`categorie` (`ID_CATEGORIE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionregleclasse`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionregleclasse` (
  `ID_REGLE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_CLASSE` INT(11) UNSIGNED NOT NULL,
  INDEX `fk_commissionregleclasse_commissionregle1_idx` (`ID_REGLE` ASC),
  INDEX `fk_commissionregleclasse_classe1_idx` (`ID_CLASSE` ASC),
  PRIMARY KEY (`ID_REGLE`, `ID_CLASSE`),
  CONSTRAINT `fk_commissionregleclasse_commissionregle1`
    FOREIGN KEY (`ID_REGLE`)
    REFERENCES `prevarisc`.`commissionregle` (`ID_REGLE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionregleclasse_classe1`
    FOREIGN KEY (`ID_CLASSE`)
    REFERENCES `prevarisc`.`classe` (`ID_CLASSE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionregleetudevisite`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionregleetudevisite` (
  `ID_REGLE` BIGINT(20) UNSIGNED NOT NULL,
  `ETUDEVISITE` TINYINT(1) NOT NULL,
  INDEX `ETUDEVISITE` (`ETUDEVISITE` ASC),
  INDEX `fk_commissionregleetudevisite_commissionregle1_idx` (`ID_REGLE` ASC),
  CONSTRAINT `fk_commissionregleetudevisite_commissionregle1`
    FOREIGN KEY (`ID_REGLE`)
    REFERENCES `prevarisc`.`commissionregle` (`ID_REGLE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionreglelocalsommeil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionreglelocalsommeil` (
  `ID_REGLE` BIGINT(20) UNSIGNED NOT NULL,
  `LOCALSOMMEIL` TINYINT(1) NOT NULL,
  INDEX `TYPE_ACTIVITE` (`LOCALSOMMEIL` ASC),
  INDEX `fk_commissionreglelocalsommeil_commissionregle1_idx` (`ID_REGLE` ASC),
  CONSTRAINT `fk_commissionreglelocalsommeil_commissionregle1`
    FOREIGN KEY (`ID_REGLE`)
    REFERENCES `prevarisc`.`commissionregle` (`ID_REGLE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`type` (
  `ID_TYPE` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_TYPE` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`ID_TYPE`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionregletype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionregletype` (
  `ID_REGLE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_TYPE` INT UNSIGNED NOT NULL,
  INDEX `fk_commissionregletype_commissionregle1_idx` (`ID_REGLE` ASC),
  INDEX `fk_commissionregletype_type1_idx` (`ID_TYPE` ASC),
  PRIMARY KEY (`ID_REGLE`, `ID_TYPE`),
  CONSTRAINT `fk_commissionregletype_commissionregle1`
    FOREIGN KEY (`ID_REGLE`)
    REFERENCES `prevarisc`.`commissionregle` (`ID_REGLE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionregletype_type1`
    FOREIGN KEY (`ID_TYPE`)
    REFERENCES `prevarisc`.`type` (`ID_TYPE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissiontypeevenement`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissiontypeevenement` (
  `ID_COMMISSIONTYPEEVENEMENT` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_COMMISSIONTYPEEVENEMENT` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONTYPEEVENEMENT`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`couchecarto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`couchecarto` (
  `ID_COUCHECARTO` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `NOM_COUCHECARTO` VARCHAR(255) NULL,
  `URL_COUCHECARTO` VARCHAR(255) NULL,
  `ISBASELAYER_COUCHECARTO` TINYINT(1) NULL,
  `LAYERS_COUCHECARTO` VARCHAR(255) NULL,
  `FORMAT_COUCHECARTO` VARCHAR(255) NULL,
  `TRANSPARENT_COUCHECARTO` TINYINT(1) NULL,
  `TYPE_COUCHECARTO` VARCHAR(50) NOT NULL,
  UNIQUE INDEX `ID_COUCHECARTO` (`ID_COUCHECARTO` ASC),
  PRIMARY KEY (`ID_COUCHECARTO`))
ENGINE = InnoDB
AUTO_INCREMENT = 2;


-- -----------------------------------------------------
-- Table `prevarisc`.`datecommission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`datecommission` (
  `ID_DATECOMMISSION` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `DATE_COMMISSION` DATE NOT NULL,
  `HEUREDEB_COMMISSION` TIME NOT NULL DEFAULT '09:00:00',
  `HEUREFIN_COMMISSION` TIME NOT NULL DEFAULT '18:00:00',
  `LIBELLE_DATECOMMISSION` VARCHAR(255) NULL DEFAULT NULL,
  `GESTION_HEURES` TINYINT(1) NOT NULL DEFAULT '1',
  `DATECOMMISSION_LIEES` BIGINT NULL,
  `ID_COMMISSIONTYPEEVENEMENT` INT(11) UNSIGNED NOT NULL,
  `COMMISSION_CONCERNE` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_DATECOMMISSION`),
  INDEX `fk_datecommission_commissiontypeevenement1_idx` (`ID_COMMISSIONTYPEEVENEMENT` ASC),
  INDEX `fk_datecommission_commission1_idx` (`COMMISSION_CONCERNE` ASC),
  CONSTRAINT `fk_datecommission_commissiontypeevenement1`
    FOREIGN KEY (`ID_COMMISSIONTYPEEVENEMENT`)
    REFERENCES `prevarisc`.`commissiontypeevenement` (`ID_COMMISSIONTYPEEVENEMENT`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_datecommission_commission1`
    FOREIGN KEY (`COMMISSION_CONCERNE`)
    REFERENCES `prevarisc`.`commission` (`ID_COMMISSION`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`piecejointe`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`piecejointe` (
  `ID_PIECEJOINTE` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `NOM_PIECEJOINTE` VARCHAR(255) NOT NULL,
  `EXTENSION_PIECEJOINTE` VARCHAR(10) NOT NULL,
  `DESCRIPTION_PIECEJOINTE` TEXT NULL DEFAULT NULL,
  `DATE_PIECEJOINTE` DATE NULL,
  PRIMARY KEY (`ID_PIECEJOINTE`))
ENGINE = InnoDB
AUTO_INCREMENT = 6;


-- -----------------------------------------------------
-- Table `prevarisc`.`datecommissionpj`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`datecommissionpj` (
  `ID_DATECOMMISSION` BIGINT(20) NOT NULL,
  `ID_PIECEJOINTE` BIGINT(20) NOT NULL,
  INDEX `fk_datecommissionpj_datecommission1_idx` (`ID_DATECOMMISSION` ASC),
  INDEX `fk_datecommissionpj_piecejointe1_idx` (`ID_PIECEJOINTE` ASC),
  PRIMARY KEY (`ID_DATECOMMISSION`, `ID_PIECEJOINTE`),
  CONSTRAINT `fk_datecommissionpj_datecommission1`
    FOREIGN KEY (`ID_DATECOMMISSION`)
    REFERENCES `prevarisc`.`datecommission` (`ID_DATECOMMISSION`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_datecommissionpj_piecejointe1`
    FOREIGN KEY (`ID_PIECEJOINTE`)
    REFERENCES `prevarisc`.`piecejointe` (`ID_PIECEJOINTE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossier` (
  `ID_DOSSIER` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `OBJET_DOSSIER` TEXT NULL DEFAULT NULL,
  `COMMUNE_DOSSIER` TEXT NULL DEFAULT NULL,
  `DATEMAIRIE_DOSSIER` DATETIME NULL DEFAULT NULL,
  `DATESECRETARIAT_DOSSIER` DATETIME NULL DEFAULT NULL,
  `TYPESERVINSTRUC_DOSSIER` VARCHAR(15) NULL DEFAULT NULL,
  `SERVICEINSTRUC_DOSSIER` VARCHAR(255) NULL DEFAULT NULL,
  `COMMISSION_DOSSIER` BIGINT(20) NULL,
  `DESCGEN_DOSSIER` TEXT NULL DEFAULT NULL,
  `ANOMALIE_DOSSIER` TEXT NULL DEFAULT NULL,
  `DESCANAL_DOSSIER` TEXT NULL DEFAULT NULL,
  `JUSTIFDEROG_DOSSIER` TEXT NULL DEFAULT NULL,
  `MESURESCOMPENS_DOSSIER` TEXT NULL DEFAULT NULL,
  `MESURESCOMPLE_DOSSIER` TEXT NULL DEFAULT NULL,
  `DESCEFF_DOSSIER` TEXT NULL DEFAULT NULL,
  `DATEVISITE_DOSSIER` DATE NULL DEFAULT NULL,
  `DATECOMM_DOSSIER` TEXT NULL DEFAULT NULL,
  `AVIS_DOSSIER` INT(1) UNSIGNED NULL,
  `AVIS_DOSSIER_COMMISSION` INT(1) UNSIGNED NULL,
  `COORDSSI_DOSSIER` TEXT NULL DEFAULT NULL,
  `DATESDIS_DOSSIER` DATETIME NULL DEFAULT NULL,
  `DATEPREF_DOSSIER` DATETIME NULL DEFAULT NULL,
  `DATEREP_DOSSIER` DATETIME NULL DEFAULT NULL,
  `DATEREUN_DOSSIER` DATETIME NULL DEFAULT NULL,
  `OPERSDIS_DOSSIER` TINYINT(4) NULL DEFAULT NULL,
  `RCCI_DOSSIER` TINYINT(4) NULL DEFAULT NULL,
  `REX_DOSSIER` TEXT NULL DEFAULT NULL,
  `CHARGESEC_DOSSIER` TEXT NULL DEFAULT NULL,
  `DUREEDEPL_DOSSIER` INT(11) NULL DEFAULT NULL,
  `GRAVPRESC_DOSSIER` TEXT NULL DEFAULT NULL,
  `NUMINTERV_DOSSIER` INT(11) NULL DEFAULT NULL,
  `DATEINTERV_DOSSIER` DATETIME NULL DEFAULT NULL,
  `DUREEINTERV_DOSSIER` TIME NULL DEFAULT NULL,
  `DATESIGN_DOSSIER` DATE NULL DEFAULT NULL,
  `DATEINSERT_DOSSIER` DATETIME NOT NULL,
  `TYPE_DOSSIER` BIGINT(20) UNSIGNED NOT NULL,
  `DESCRIPTIF_DOSSIER` TEXT BINARY NULL,
  `DEMANDEUR_DOSSIER` TEXT NULL,
  `DATEENVTRANSIT_DOSSIER` DATE NULL,
  `REGLEDEROG_DOSSIER` TEXT NULL DEFAULT NULL,
  `INCOMPLET_DOSSIER` TINYINT(1) NULL DEFAULT 0,
  `DATEINCOMPLET_DOSSIER` DATE NULL,
  `CREATEUR_DOSSIER` BIGINT NULL,
  `HORSDELAI_DOSSIER` TINYINT(1) NULL DEFAULT 0,
  `DIFFEREAVIS_DOSSIER` TINYINT NULL,
  `NPSP_DOSSIER` TINYINT NULL,
  `CNE_DOSSIER` TINYINT NULL,
  `FACTDANGE_DOSSIER` TINYINT NULL,
  `LIEUREUNION_DOSSIER` TEXT NULL,
  `ABSQUORUM_DOSSIER` TINYINT NULL,
  `ECHEANCIERTRAV_DOSSIER` DATE NULL DEFAULT NULL,
  `VERROU_DOSSIER` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`ID_DOSSIER`),
  INDEX `fk_dossier_dossiertype1_idx` (`TYPE_DOSSIER` ASC),
  INDEX `fk_dossier_avis1_idx` (`AVIS_DOSSIER` ASC),
  CONSTRAINT `fk_dossier_dossiertype1`
    FOREIGN KEY (`TYPE_DOSSIER`)
    REFERENCES `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_dossier_avis1`
    FOREIGN KEY (`AVIS_DOSSIER`)
    REFERENCES `prevarisc`.`avis` (`ID_AVIS`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossieraffectation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossieraffectation` (
  `HEURE_DEB_AFFECT` TIME NULL DEFAULT NULL,
  `HEURE_FIN_AFFECT` TIME NULL DEFAULT NULL,
  `NUM_DOSSIER` INT(11) NOT NULL DEFAULT '0',
  `ID_DATECOMMISSION_AFFECT` BIGINT(20) NOT NULL,
  `ID_DOSSIER_AFFECT` BIGINT(20) NOT NULL,
  INDEX `fk_dossieraffectation_datecommission1_idx` (`ID_DATECOMMISSION_AFFECT` ASC),
  INDEX `fk_dossieraffectation_dossier2_idx` (`ID_DOSSIER_AFFECT` ASC),
  CONSTRAINT `fk_dossieraffectation_datecommission1`
    FOREIGN KEY (`ID_DATECOMMISSION_AFFECT`)
    REFERENCES `prevarisc`.`datecommission` (`ID_DATECOMMISSION`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_dossieraffectation_dossier1`
    FOREIGN KEY (`ID_DOSSIER_AFFECT`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossiercontact`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossiercontact` (
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  `ID_UTILISATEURINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_DOSSIER`, `ID_UTILISATEURINFORMATIONS`),
  INDEX `fk_dossiercontact_dossier1_idx` (`ID_DOSSIER` ASC),
  INDEX `fk_dossiercontact_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS` ASC),
  CONSTRAINT `fk_dossiercontact_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossiercontact_utilisateurinformations1`
    FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`)
    REFERENCES `prevarisc`.`utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossierdocconsulte`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossierdocconsulte` (
  `ID_DOSSIERDOCCONSULTE` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `ID_NATURE` BIGINT(20) NOT NULL,
  `REF_CONSULTE` VARCHAR(255) CHARACTER SET 'latin1' NOT NULL,
  `DATE_CONSULTE` DATE NOT NULL,
  `DOC_CONSULTE` TINYINT(1) NOT NULL DEFAULT '0',
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  `ID_DOC` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_DOSSIERDOCCONSULTE`),
  INDEX `fk_dossierdocconsulte_dossier1_idx` (`ID_DOSSIER` ASC),
  CONSTRAINT `fk_dossierdocconsulte_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossierdocurba`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossierdocurba` (
  `ID_DOCURBA` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `NUM_DOCURBA` VARCHAR(100) NOT NULL,
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_DOCURBA`),
  INDEX `fk_dossierdocurba_dossier1_idx` (`ID_DOSSIER` ASC),
  CONSTRAINT `fk_dossierdocurba_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossierlie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossierlie` (
  `ID_DOSSIERLIE` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `ID_DOSSIER1` BIGINT(20) NOT NULL,
  `ID_DOSSIER2` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_DOSSIERLIE`),
  INDEX `fk_dossierlie_dossier1_idx` (`ID_DOSSIER1` ASC),
  INDEX `fk_dossierlie_dossier2_idx` (`ID_DOSSIER2` ASC),
  CONSTRAINT `fk_dossierlie_dossier1`
    FOREIGN KEY (`ID_DOSSIER1`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossierlie_dossier2`
    FOREIGN KEY (`ID_DOSSIER2`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossiernature`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossiernature` (
  `ID_DOSSIERNATURE` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `ID_NATURE` BIGINT(20) NOT NULL,
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_DOSSIERNATURE`),
  INDEX `fk_dossiernature_dossiernatureliste1_idx` (`ID_NATURE` ASC),
  INDEX `fk_dossiernature_dossier1_idx` (`ID_DOSSIER` ASC),
  CONSTRAINT `fk_dossiernature_dossiernatureliste1`
    FOREIGN KEY (`ID_NATURE`)
    REFERENCES `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_dossiernature_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossierpj`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossierpj` (
  `ID_PIECEJOINTE` BIGINT(20) NOT NULL,
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  `PJ_COMMISSION` TINYINT NOT NULL DEFAULT 0,
  INDEX `fk_dossierpj_piecejointe1_idx` (`ID_PIECEJOINTE` ASC),
  INDEX `fk_dossierpj_dossier1_idx` (`ID_DOSSIER` ASC),
  CONSTRAINT `fk_dossierpj_piecejointe1`
    FOREIGN KEY (`ID_PIECEJOINTE`)
    REFERENCES `prevarisc`.`piecejointe` (`ID_PIECEJOINTE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossierpj_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`groupe`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`groupe` (
  `ID_GROUPE` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_GROUPE` VARCHAR(255) NOT NULL,
  `DESC_GROUPE` TEXT NOT NULL,
  PRIMARY KEY (`ID_GROUPE`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`utilisateur`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`utilisateur` (
  `ID_UTILISATEUR` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `USERNAME_UTILISATEUR` VARCHAR(100) NOT NULL,
  `PASSWD_UTILISATEUR` VARCHAR(32) NULL,
  `LASTACTION_UTILISATEUR` TIMESTAMP NULL,
  `ACTIF_UTILISATEUR` TINYINT(1) NOT NULL DEFAULT '1',
  `NUMINSEE_COMMUNE` VARCHAR(5) NULL,
  `ID_UTILISATEURINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  `ID_GROUPE` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_UTILISATEUR`),
  INDEX `fk_utilisateur_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS` ASC),
  INDEX `fk_utilisateur_groupe1_idx` (`ID_GROUPE` ASC),
  CONSTRAINT `fk_utilisateur_utilisateurinformations1`
    FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`)
    REFERENCES `prevarisc`.`utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_utilisateur_groupe1`
    FOREIGN KEY (`ID_GROUPE`)
    REFERENCES `prevarisc`.`groupe` (`ID_GROUPE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossierpreventionniste`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossierpreventionniste` (
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  `ID_PREVENTIONNISTE` BIGINT(20) UNSIGNED NOT NULL,
  INDEX `fk_dossierpreventionniste_dossier1_idx` (`ID_DOSSIER` ASC),
  INDEX `fk_dossierpreventionniste_utilisateur1_idx` (`ID_PREVENTIONNISTE` ASC),
  PRIMARY KEY (`ID_DOSSIER`, `ID_PREVENTIONNISTE`),
  CONSTRAINT `fk_dossierpreventionniste_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossierpreventionniste_utilisateur1`
    FOREIGN KEY (`ID_PREVENTIONNISTE`)
    REFERENCES `prevarisc`.`utilisateur` (`ID_UTILISATEUR`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissement`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissement` (
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `NUMEROID_ETABLISSEMENT` VARCHAR(50) NULL,
  `TELEPHONE_ETABLISSEMENT` VARCHAR(20) NULL,
  `FAX_ETABLISSEMENT` VARCHAR(20) NULL,
  `COURRIEL_ETABLISSEMENT` VARCHAR(75) NULL,
  `DATEENREGISTREMENT_ETABLISSEMENT` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DESCRIPTIF_ETABLISSEMENT` TEXT NULL,
  `NBPREV_ETABLISSEMENT` TINYINT NULL,
  `DUREEVISITE_ETABLISSEMENT` TIME NULL,
  `ID_DOSSIER_DONNANT_AVIS` BIGINT(20) NULL,
  `DESCTECH_IMPLANTATION_SURFACE_ETABLISSEMENT` INT NULL,
  `DESCTECH_IMPLANTATION_SHON_ETABLISSEMENT` INT NULL,
  `DESCTECH_IMPLANTATION_SHOB_ETABLISSEMENT` INT NULL,
  `DESCTECH_IMPLANTATION_NBNIVEAUX_ETABLISSEMENT` INT NULL,
  `DESCTECH_IMPLANTATION_PBDN_ETABLISSEMENT` INT NULL,
  `DESCTECH_DESSERTE_NBFACADELIBRE_ETABLISSEMENT` INT NULL,
  `DESCTECH_DESSERTE_VOIEENGIN_ETABLISSEMENT` INT NULL,
  `DESCTECH_DESSERTE_VOIEECHELLE_ETABLISSEMENT` INT NULL,
  `DESCTECH_DESSERTE_ESPACELIBRE_ETABLISSEMENT` INT NULL,
  `DESCTECH_ISOLEMENT_LATERALCF_ETABLISSEMENT` INT NULL,
  `DESCTECH_ISOLEMENT_SUPERPOSECF_ETABLISSEMENT` INT NULL,
  `DESCTECH_ISOLEMENT_VISAVIS_ETABLISSEMENT` INT NULL,
  `DESCTECH_STABILITE_STRUCTURESF_ETABLISSEMENT` INT NULL,
  `DESCTECH_STABILITE_PLANCHERSF_ETABLISSEMENT` INT NULL,
  `DESCTECH_DISTRIBUTION_CLOISONNEMENTTRAD_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_DISTRIBUTION_SECTEURS_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_DISTRIBUTION_COMPARTIMENTS_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_LOCAUXARISQUE_NBRISQUESMOYENS_ETABLISSEMENT` INT NULL,
  `DESCTECH_LOCAUXARISQUE_NBRISQUESIMPORTANTS_ETABLISSEMENT` INT NULL,
  `DESCTECH_ESPACES_NOMBRE_ETABLISSEMENT` INT NULL,
  `DESCTECH_ESPACES_NIVEAUCONCERNE_ETABLISSEMENT` INT NULL,
  `DESCTECH_DESENFUMAGE_NATUREL_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_DESENFUMAGE_MECANIQUE_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_DESENFUMAGE_COMMENTAIRE_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_CHAUFFERIES_NB_ETABLISSEMENT` INT NULL,
  `DESCTECH_CHAUFFERIES_PUISSMAX_ETABLISSEMENT` INT NULL,
  `DESCTECH_COUPURENRJ_GAZ_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_COUPURENRJ_ELEC_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_COUPURENRJ_PHOTOVOLTAIQUE_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_COUPURENRJ_AUTRE_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_ASCENSEURS_NBTOTAL_ETABLISSEMENT` INT NULL,
  `DESCTECH_ASCENSEURS_NBAS4_ETABLISSEMENT` INT NULL,
  `DESCTECH_MOYENSSECOURS_COLONNESSECHES_ETABLISSEMENT` TINYINT(45) NULL,
  `DESCTECH_MOYENSSECOURS_COLONNESHUMIDES_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_MOYENSSECOURS_RIA_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_MOYENSSECOURS_SPRINKLEUR_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_MOYENSSECOURS_BROUILLARDEAU_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_PCSECU_PRESENCE_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_PCSECU_LOCALISATION_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_SSI_PRESENCE_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_SSI_CATEGORIE_ETABLISSEMENT` CHAR(1) NULL,
  `DESCTECH_SSI_ALARME_TYPE_ETABLISSEMENT` CHAR(2) NULL,
  `DESCTECH_SERVICESECU_EL18_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_SERVICESECU_PERSONNELSDESIGNES_ETABLISSEMENT` TINYINT(1) NULL,
  `DESCTECH_SERVICESECU_AGENTDESECU_ETABLISSEMENT` INT NULL,
  `DESCTECH_SERVICESECU_CHEFEQUIPE_ETABLISSEMENT` INT NULL,
  `DESCTECH_SERVICESECU_CHEFDESERVICESECU_ETABLISSEMENT` INT NULL,
  `DESCTECH_SERVICESECU_SP_ETABLISSEMENT` INT NULL,
  `DESCTECH_SERVICESECU_COMMENTAIRESP_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_DEFENSE_PTEAU_ETABLISSEMENT` INT NULL,
  `DESCTECH_DEFENSE_VOLUMEPTEAU_ETABLISSEMENT` INT NULL,
  `DESCTECH_DEFENSE_PTEAUCOMMENTAIRE_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_DEFENSE_PI_ETABLISSEMENT` INT NULL,
  `DESCTECH_DEFENSE_BI_ETABLISSEMENT` INT NULL,
  `DESCTECH_DEFENSE_DEBITSIMULTANE_ETABLISSEMENT` INT NULL,
  `DESCTECH_CHAUFFERIES_NB30KW` INT NULL,
  `DESCTECH_CHAUFFERIES_NB70KW` INT NULL,
  `DESCTECH_CHAUFFERIES_NB2MW` INT NULL,
  `DESCTECH_CHAUFFERIES_PUISSANCETOTALE` INT NULL,
  `DESCRIPTIF_HISTORIQUE_ETABLISSEMENT` TEXT NULL,
  `DESCRIPTIF_DEROGATIONS_ETABLISSEMENT` TEXT NULL,
  `DESCTECH_IMPLANTATION_SURFACETOTALE_ETABLISSEMENT` INT NULL,
  `DESCTECH_IMPLANTATION_SURFACEACCPUBLIC_ETABLISSEMENT` INT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENT`),
  INDEX `fk_etablissement_dossier1_idx` (`ID_DOSSIER_DONNANT_AVIS` ASC),
  CONSTRAINT `fk_etablissement_dossier1`
    FOREIGN KEY (`ID_DOSSIER_DONNANT_AVIS`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementadresse`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementadresse` (
  `ID_ADRESSE` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `NUMERO_ADRESSE` VARCHAR(255) NULL,
  `COMPLEMENT_ADRESSE` VARCHAR(255) NULL,
  `LON_ETABLISSEMENTADRESSE` FLOAT NULL,
  `LAT_ETABLISSEMENTADRESSE` FLOAT NULL,
  `ID_RUE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `NUMINSEE_COMMUNE` CHAR(5) NOT NULL,
  PRIMARY KEY (`ID_ADRESSE`),
  INDEX `fk_etablissementadresse_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  INDEX `fk_etablissementadresse_adresserue1_idx` (`ID_RUE` ASC),
  INDEX `fk_etablissementadresse_adressecommune1_idx` (`NUMINSEE_COMMUNE` ASC),
  CONSTRAINT `fk_etablissementadresse_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `prevarisc`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementadresse_adresserue1`
    FOREIGN KEY (`ID_RUE`)
    REFERENCES `prevarisc`.`adresserue` (`ID_RUE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementadresse_adressecommune1`
    FOREIGN KEY (`NUMINSEE_COMMUNE`)
    REFERENCES `prevarisc`.`adressecommune` (`NUMINSEE_COMMUNE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementcontact`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementcontact` (
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_UTILISATEURINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENT`, `ID_UTILISATEURINFORMATIONS`),
  INDEX `fk_etablissementcontact_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  INDEX `fk_etablissementcontact_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS` ASC),
  CONSTRAINT `fk_etablissementcontact_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `prevarisc`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementcontact_utilisateurinformations1`
    FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`)
    REFERENCES `prevarisc`.`utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementdossier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementdossier` (
  `ID_ETABLISSEMENTDOSSIER` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTDOSSIER`),
  INDEX `fk_etablissementdossier_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  INDEX `fk_etablissementdossier_dossier1_idx` (`ID_DOSSIER` ASC),
  CONSTRAINT `fk_etablissementdossier_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `prevarisc`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementdossier_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`genre`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`genre` (
  `ID_GENRE` INT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_GENRE` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`ID_GENRE`))
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`famille`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`famille` (
  `ID_FAMILLE` INT(11) NOT NULL AUTO_INCREMENT,
  `LIBELLE_FAMILLE` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`ID_FAMILLE`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`typeactivite`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`typeactivite` (
  `ID_TYPEACTIVITE` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_ACTIVITE` VARCHAR(255) NOT NULL,
  `ID_TYPE` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_TYPEACTIVITE`),
  INDEX `fk_typeactivite_type1_idx` (`ID_TYPE` ASC),
  CONSTRAINT `fk_typeactivite_type1`
    FOREIGN KEY (`ID_TYPE`)
    REFERENCES `prevarisc`.`type` (`ID_TYPE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 102
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`statut`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`statut` (
  `ID_STATUT` INT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_STATUT` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`ID_STATUT`))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementinformations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementinformations` (
  `ID_ETABLISSEMENTINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_ETABLISSEMENTINFORMATIONS` VARCHAR(255) NOT NULL,
  `ICPE_ETABLISSEMENTINFORMATIONS` TINYINT(1) NULL DEFAULT NULL,
  `PERIODICITE_ETABLISSEMENTINFORMATIONS` TINYINT NULL,
  `R12320_ETABLISSEMENTINFORMATIONS` TINYINT(1) NULL DEFAULT NULL,
  `LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS` TINYINT(1) NULL,
  `EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS` INT(11) NULL DEFAULT NULL,
  `EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS` INT(11) NULL DEFAULT NULL,
  `EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS` INT(11) NULL DEFAULT NULL,
  `EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS` INT(11) NULL DEFAULT NULL,
  `COMPLEMENT_ETABLISSEMENTINFORMATIONS` VARCHAR(255) NULL,
  `UTILISATEUR_ETABLISSEMENTINFORMATIONS` BIGINT(20) UNSIGNED ZEROFILL NULL,
  `DATE_ETABLISSEMENTINFORMATIONS` DATE NOT NULL,
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_GENRE` INT(2) UNSIGNED NOT NULL,
  `ID_CLASSE` INT(11) UNSIGNED NULL,
  `ID_FAMILLE` INT(11) NULL,
  `ID_CATEGORIE` INT(1) UNSIGNED NULL,
  `ID_TYPE` INT UNSIGNED NULL,
  `ID_TYPEACTIVITE` INT(11) UNSIGNED NULL,
  `ID_COMMISSION` BIGINT(20) UNSIGNED NULL,
  `ID_STATUT` INT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONS`),
  INDEX `UTILISATEUR_ETABLISSEMENTINFORMATIONS` (`UTILISATEUR_ETABLISSEMENTINFORMATIONS` ASC),
  INDEX `fk_etablissementinformations_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  INDEX `fk_etablissementinformations_genre1_idx` (`ID_GENRE` ASC),
  INDEX `fk_etablissementinformations_classe1_idx` (`ID_CLASSE` ASC),
  INDEX `fk_etablissementinformations_famille1_idx` (`ID_FAMILLE` ASC),
  INDEX `fk_etablissementinformations_categorie1_idx` (`ID_CATEGORIE` ASC),
  INDEX `fk_etablissementinformations_type1_idx` (`ID_TYPE` ASC),
  INDEX `fk_etablissementinformations_typeactivite1_idx` (`ID_TYPEACTIVITE` ASC),
  INDEX `fk_etablissementinformations_commission1_idx` (`ID_COMMISSION` ASC),
  INDEX `fk_etablissementinformations_statut1_idx` (`ID_STATUT` ASC),
  CONSTRAINT `fk_etablissementinformations_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `prevarisc`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementinformations_genre1`
    FOREIGN KEY (`ID_GENRE`)
    REFERENCES `prevarisc`.`genre` (`ID_GENRE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_classe1`
    FOREIGN KEY (`ID_CLASSE`)
    REFERENCES `prevarisc`.`classe` (`ID_CLASSE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_famille1`
    FOREIGN KEY (`ID_FAMILLE`)
    REFERENCES `prevarisc`.`famille` (`ID_FAMILLE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_categorie1`
    FOREIGN KEY (`ID_CATEGORIE`)
    REFERENCES `prevarisc`.`categorie` (`ID_CATEGORIE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_type1`
    FOREIGN KEY (`ID_TYPE`)
    REFERENCES `prevarisc`.`type` (`ID_TYPE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_typeactivite1`
    FOREIGN KEY (`ID_TYPEACTIVITE`)
    REFERENCES `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_commission1`
    FOREIGN KEY (`ID_COMMISSION`)
    REFERENCES `prevarisc`.`commission` (`ID_COMMISSION`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_statut1`
    FOREIGN KEY (`ID_STATUT`)
    REFERENCES `prevarisc`.`statut` (`ID_STATUT`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 41;


-- -----------------------------------------------------
-- Table `prevarisc`.`typeplan`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`typeplan` (
  `ID_TYPEPLAN` INT(11) NOT NULL AUTO_INCREMENT,
  `LIBELLE_TYPEPLAN` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`ID_TYPEPLAN`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementinformationsplan`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementinformationsplan` (
  `ID_ETABLISSEMENTINFORMATIONSPLAN` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `NUMERO_ETABLISSEMENTPLAN` VARCHAR(50) NULL,
  `DATE_ETABLISSEMENTPLAN` DATE NOT NULL,
  `MISEAJOUR_ETABLISSEMENTPLAN` TINYINT(1) NULL,
  `ID_ETABLISSEMENTINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  `ID_TYPEPLAN` INT(11) NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONSPLAN`),
  INDEX `fk_etablissementinformationsplan_etablissementinformations1_idx` (`ID_ETABLISSEMENTINFORMATIONS` ASC),
  INDEX `fk_etablissementinformationsplan_typeplan1_idx` (`ID_TYPEPLAN` ASC),
  CONSTRAINT `fk_etablissementinformationsplan_etablissementinformations1`
    FOREIGN KEY (`ID_ETABLISSEMENTINFORMATIONS`)
    REFERENCES `prevarisc`.`etablissementinformations` (`ID_ETABLISSEMENTINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementinformationsplan_typeplan1`
    FOREIGN KEY (`ID_TYPEPLAN`)
    REFERENCES `prevarisc`.`typeplan` (`ID_TYPEPLAN`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementinformationspreventionniste`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementinformationspreventionniste` (
  `ID_ETABLISSEMENTINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  `ID_UTILISATEUR` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONS`, `ID_UTILISATEUR`),
  INDEX `fk_etablissementinformationspreventionniste_etablissementin_idx` (`ID_ETABLISSEMENTINFORMATIONS` ASC),
  INDEX `fk_etablissementinformationspreventionniste_utilisateur1_idx` (`ID_UTILISATEUR` ASC),
  CONSTRAINT `fk_etablissementinformationspreventionniste_etablissementinfo1`
    FOREIGN KEY (`ID_ETABLISSEMENTINFORMATIONS`)
    REFERENCES `prevarisc`.`etablissementinformations` (`ID_ETABLISSEMENTINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementinformationspreventionniste_utilisateur1`
    FOREIGN KEY (`ID_UTILISATEUR`)
    REFERENCES `prevarisc`.`utilisateur` (`ID_UTILISATEUR`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementinformationsrubrique`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementinformationsrubrique` (
  `ID_ETABLISSEMENTINFORMATIONSRUBRIQUE` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `ID_RUBRIQUE` TINYINT(4) NOT NULL,
  `NUMERO_ETABLISSEMENTINFORMATIONSRUBRIQUE` INT(11) NOT NULL,
  `VALEUR_ETABLISSEMENTINFORMATIONSRUBRIQUE` FLOAT NOT NULL,
  `NOM_ETABLISSEMENTINFORMATIONSRUBRIQUE` VARCHAR(150) NOT NULL,
  `CLASSEMENT_ETABLISSEMENTINFORMATIONSRUBRIQUE` VARCHAR(50) NULL,
  `ID_ETABLISSEMENTINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONSRUBRIQUE`),
  INDEX `ID_ETABLISSEMENTINFORMATIONS` (`ID_RUBRIQUE` ASC),
  INDEX `fk_etablissementinformationsrubrique_etablissementinformati_idx` (`ID_ETABLISSEMENTINFORMATIONS` ASC),
  CONSTRAINT `fk_etablissementinformationsrubrique_etablissementinformations1`
    FOREIGN KEY (`ID_ETABLISSEMENTINFORMATIONS`)
    REFERENCES `prevarisc`.`etablissementinformations` (`ID_ETABLISSEMENTINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementinformationstypesactivitessecondaires`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementinformationstypesactivitessecondaires` (
  `ID_ETABLISSEMENTINFORMATIONSTYPESACTIVITESSECONDAIRES` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ID_ETABLISSEMENTINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  `ID_TYPE_SECONDAIRE` INT UNSIGNED NOT NULL,
  `ID_TYPEACTIVITE_SECONDAIRE` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONSTYPESACTIVITESSECONDAIRES`),
  INDEX `fk_etablissementinformationstypesactivitessecondaires_etabl_idx` (`ID_ETABLISSEMENTINFORMATIONS` ASC),
  INDEX `fk_etablissementinformationstypesactivitessecondaires_type1_idx` (`ID_TYPE_SECONDAIRE` ASC),
  INDEX `fk_etablissementinformationstypesactivitessecondaires_typea_idx` (`ID_TYPEACTIVITE_SECONDAIRE` ASC),
  CONSTRAINT `fk_etablissementinformationstypesactivitessecondaires_etablis1`
    FOREIGN KEY (`ID_ETABLISSEMENTINFORMATIONS`)
    REFERENCES `prevarisc`.`etablissementinformations` (`ID_ETABLISSEMENTINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementinformationstypesactivitessecondaires_type1`
    FOREIGN KEY (`ID_TYPE_SECONDAIRE`)
    REFERENCES `prevarisc`.`type` (`ID_TYPE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformationstypesactivitessecondaires_typeact1`
    FOREIGN KEY (`ID_TYPEACTIVITE_SECONDAIRE`)
    REFERENCES `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementlie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementlie` (
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_FILS_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENT`, `ID_FILS_ETABLISSEMENT`),
  INDEX `fk_etablissementlie_etablissement2_idx` (`ID_FILS_ETABLISSEMENT` ASC),
  CONSTRAINT `fk_etablissementlie_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `prevarisc`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementlie_etablissement2`
    FOREIGN KEY (`ID_FILS_ETABLISSEMENT`)
    REFERENCES `prevarisc`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementpj`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementpj` (
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_PIECEJOINTE` BIGINT(20) NOT NULL,
  `PLACEMENT_ETABLISSEMENTPJ` INT NULL DEFAULT 0,
  PRIMARY KEY (`ID_ETABLISSEMENT`, `ID_PIECEJOINTE`),
  INDEX `fk_etablissementpj_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  INDEX `fk_etablissementpj_piecejointe1_idx` (`ID_PIECEJOINTE` ASC),
  CONSTRAINT `fk_etablissementpj_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `prevarisc`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementpj_piecejointe1`
    FOREIGN KEY (`ID_PIECEJOINTE`)
    REFERENCES `prevarisc`.`piecejointe` (`ID_PIECEJOINTE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`groupementtype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`groupementtype` (
  `ID_GROUPEMENTTYPE` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_GROUPEMENTTYPE` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_GROUPEMENTTYPE`))
ENGINE = InnoDB
AUTO_INCREMENT = 14
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`groupement`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`groupement` (
  `ID_GROUPEMENT` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_GROUPEMENT` VARCHAR(255) NOT NULL,
  `ID_UTILISATEURINFORMATIONS` BIGINT(20) NULL,
  `ID_GROUPEMENTTYPE` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_GROUPEMENT`),
  INDEX `ID_UTILISATEURINFORMATIONS` (`ID_UTILISATEURINFORMATIONS` ASC),
  INDEX `fk_groupement_groupementtype1_idx` (`ID_GROUPEMENTTYPE` ASC),
  CONSTRAINT `fk_groupement_groupementtype1`
    FOREIGN KEY (`ID_GROUPEMENTTYPE`)
    REFERENCES `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`groupementcommune`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`groupementcommune` (
  `ID_GROUPEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `NUMINSEE_COMMUNE` CHAR(5) NOT NULL,
  INDEX `fk_groupementcommune_groupement1_idx` (`ID_GROUPEMENT` ASC),
  INDEX `fk_groupementcommune_adressecommune1_idx` (`NUMINSEE_COMMUNE` ASC),
  PRIMARY KEY (`ID_GROUPEMENT`, `NUMINSEE_COMMUNE`),
  CONSTRAINT `fk_groupementcommune_groupement1`
    FOREIGN KEY (`ID_GROUPEMENT`)
    REFERENCES `prevarisc`.`groupement` (`ID_GROUPEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_groupementcommune_adressecommune1`
    FOREIGN KEY (`NUMINSEE_COMMUNE`)
    REFERENCES `prevarisc`.`adressecommune` (`NUMINSEE_COMMUNE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`groupementcontact`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`groupementcontact` (
  `ID_GROUPEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_UTILISATEURINFORMATIONS` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_GROUPEMENT`, `ID_UTILISATEURINFORMATIONS`),
  INDEX `fk_groupementcontact_groupement1_idx` (`ID_GROUPEMENT` ASC),
  INDEX `fk_groupementcontact_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS` ASC),
  CONSTRAINT `fk_groupementcontact_groupement1`
    FOREIGN KEY (`ID_GROUPEMENT`)
    REFERENCES `prevarisc`.`groupement` (`ID_GROUPEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_groupementcontact_utilisateurinformations1`
    FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`)
    REFERENCES `prevarisc`.`utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`groupementpreventionniste`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`groupementpreventionniste` (
  `ID_GROUPEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_UTILISATEUR` BIGINT(20) UNSIGNED NOT NULL,
  `DATEDEBUT_GROUPEMENTPREVENTIONNISTE` DATETIME NOT NULL,
  `DATEFIN_GROUPEMENTPREVENTIONNISTE` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`ID_GROUPEMENT`, `ID_UTILISATEUR`),
  INDEX `fk_groupementpreventionniste_groupement1_idx` (`ID_GROUPEMENT` ASC),
  INDEX `fk_groupementpreventionniste_utilisateur1_idx` (`ID_UTILISATEUR` ASC),
  CONSTRAINT `fk_groupementpreventionniste_groupement1`
    FOREIGN KEY (`ID_GROUPEMENT`)
    REFERENCES `prevarisc`.`groupement` (`ID_GROUPEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_groupementpreventionniste_utilisateur1`
    FOREIGN KEY (`ID_UTILISATEUR`)
    REFERENCES `prevarisc`.`utilisateur` (`ID_UTILISATEUR`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`listedocajout`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`listedocajout` (
  `ID_DOCAJOUT` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOCAJOUT` TEXT CHARACTER SET 'latin1' NOT NULL,
  `REF_DOCAJOUT` VARCHAR(255) CHARACTER SET 'latin1' NULL DEFAULT NULL,
  `DATE_DOCAJOUT` DATE NULL DEFAULT NULL,
  `ID_NATURE` BIGINT(20) NOT NULL,
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_DOCAJOUT`),
  INDEX `fk_listedocajout_dossier1_idx` (`ID_DOSSIER` ASC),
  CONSTRAINT `fk_listedocajout_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`listedocconsulte`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`listedocconsulte` (
  `ID_DOC` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOC` TEXT CHARACTER SET 'latin1' NULL DEFAULT NULL,
  `VISITE_DOC` TINYINT(1) NOT NULL DEFAULT 0,
  `ETUDE_DOC` TINYINT(1) NOT NULL DEFAULT 0,
  `VISITERT_DOC` TINYINT(1) NOT NULL DEFAULT 0,
  `VISITEVAO_DOC` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID_DOC`))
ENGINE = InnoDB
AUTO_INCREMENT = 49;


-- -----------------------------------------------------
-- Table `prevarisc`.`news`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`news` (
  `ID_NEWS` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `TYPE_NEWS` VARCHAR(100) NOT NULL,
  `TEXTE_NEWS` TEXT NOT NULL,
  `ID_UTILISATEUR` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_NEWS`),
  INDEX `fk_news_utilisateur1_idx` (`ID_UTILISATEUR` ASC),
  CONSTRAINT `fk_news_utilisateur1`
    FOREIGN KEY (`ID_UTILISATEUR`)
    REFERENCES `prevarisc`.`utilisateur` (`ID_UTILISATEUR`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`newsgroupe`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`newsgroupe` (
  `ID_NEWS` BIGINT UNSIGNED NOT NULL,
  `ID_GROUPE` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_NEWS`, `ID_GROUPE`),
  INDEX `fk_newsgroupe_groupe1_idx` (`ID_GROUPE` ASC),
  CONSTRAINT `fk_newsgroupe_news1`
    FOREIGN KEY (`ID_NEWS`)
    REFERENCES `prevarisc`.`news` (`ID_NEWS`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_newsgroupe_groupe1`
    FOREIGN KEY (`ID_GROUPE`)
    REFERENCES `prevarisc`.`groupe` (`ID_GROUPE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`periodicite`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`periodicite` (
  `ID_CATEGORIE` INT(2) UNSIGNED NOT NULL,
  `ID_TYPE` VARCHAR(10) NOT NULL,
  `LOCALSOMMEIL_PERIODICITE` TINYINT(4) NOT NULL DEFAULT '0',
  `PERIODICITE_PERIODICITE` INT(2) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`utilisateurcommission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`utilisateurcommission` (
  `ID_UTILISATEUR` BIGINT(20) UNSIGNED NOT NULL,
  `ID_COMMISSION` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_UTILISATEUR`, `ID_COMMISSION`),
  INDEX `fk_utilisateurcommission_utilisateur1_idx` (`ID_UTILISATEUR` ASC),
  INDEX `fk_utilisateurcommission_commission1_idx` (`ID_COMMISSION` ASC),
  CONSTRAINT `fk_utilisateurcommission_utilisateur1`
    FOREIGN KEY (`ID_UTILISATEUR`)
    REFERENCES `prevarisc`.`utilisateur` (`ID_UTILISATEUR`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_utilisateurcommission_commission1`
    FOREIGN KEY (`ID_COMMISSION`)
    REFERENCES `prevarisc`.`commission` (`ID_COMMISSION`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`utilisateurgroupement`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`utilisateurgroupement` (
  `ID_UTILISATEUR` BIGINT(20) UNSIGNED NOT NULL,
  `ID_GROUPEMENT` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_UTILISATEUR`, `ID_GROUPEMENT`),
  INDEX `fk_utilisateurgroupement_utilisateur1_idx` (`ID_UTILISATEUR` ASC),
  INDEX `fk_utilisateurgroupement_groupement1_idx` (`ID_GROUPEMENT` ASC),
  CONSTRAINT `fk_utilisateurgroupement_utilisateur1`
    FOREIGN KEY (`ID_UTILISATEUR`)
    REFERENCES `prevarisc`.`utilisateur` (`ID_UTILISATEUR`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_utilisateurgroupement_groupement1`
    FOREIGN KEY (`ID_GROUPEMENT`)
    REFERENCES `prevarisc`.`groupement` (`ID_GROUPEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `prevarisc`.`typetextesappl`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`typetextesappl` (
  `ID_TYPETEXTEAPPL` BIGINT NOT NULL AUTO_INCREMENT,
  `LIBELLE_TYPETEXTEAPPL` TEXT NULL,
  PRIMARY KEY (`ID_TYPETEXTEAPPL`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`textesappl`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`textesappl` (
  `ID_TEXTESAPPL` BIGINT NOT NULL AUTO_INCREMENT,
  `LIBELLE_TEXTESAPPL` TEXT NULL,
  `VISIBLE_TEXTESAPPL` TINYINT NULL,
  `ID_TYPETEXTEAPPL` BIGINT NOT NULL,
  PRIMARY KEY (`ID_TEXTESAPPL`),
  INDEX `fk_textesappl_typetextesappl1_idx` (`ID_TYPETEXTEAPPL` ASC),
  CONSTRAINT `fk_textesappl_typetextesappl1`
    FOREIGN KEY (`ID_TYPETEXTEAPPL`)
    REFERENCES `prevarisc`.`typetextesappl` (`ID_TYPETEXTEAPPL`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossiertextesappl`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossiertextesappl` (
  `ID_TEXTESAPPL` BIGINT NOT NULL,
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_TEXTESAPPL`, `ID_DOSSIER`),
  INDEX `fk_table1_dossier1_idx` (`ID_DOSSIER` ASC),
  CONSTRAINT `fk_table1_textesappl1`
    FOREIGN KEY (`ID_TEXTESAPPL`)
    REFERENCES `prevarisc`.`textesappl` (`ID_TEXTESAPPL`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`etablissementtextapp`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`etablissementtextapp` (
  `ID_TEXTESAPPL` BIGINT NOT NULL,
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_TEXTESAPPL`, `ID_ETABLISSEMENT`),
  INDEX `fk_etablissementtextapp_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  CONSTRAINT `fk_etablissementtextapp_textesappl1`
    FOREIGN KEY (`ID_TEXTESAPPL`)
    REFERENCES `prevarisc`.`textesappl` (`ID_TEXTESAPPL`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementtextapp_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `prevarisc`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptioncat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptioncat` (
  `ID_PRESCRIPTION_CAT` BIGINT NOT NULL AUTO_INCREMENT,
  `LIBELLE_PRESCRIPTION_CAT` VARCHAR(255) NULL,
  `NUM_PRESCRIPTION_CAT` INT NOT NULL,
  PRIMARY KEY (`ID_PRESCRIPTION_CAT`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptiontexte`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptiontexte` (
  `ID_PRESCRIPTIONTEXTE` BIGINT NOT NULL AUTO_INCREMENT,
  `LIBELLE_PRESCRIPTIONTEXTE` VARCHAR(255) NULL,
  `NUM_PRESCRIPTIONTEXTE` INT NULL,
  `ID_PRESCRIPTIONCAT` BIGINT NOT NULL,
  PRIMARY KEY (`ID_PRESCRIPTIONTEXTE`),
  INDEX `fk_prescriptiontexte_prescriptioncat1_idx` (`ID_PRESCRIPTIONCAT` ASC),
  CONSTRAINT `fk_prescriptiontexte_prescriptioncat1`
    FOREIGN KEY (`ID_PRESCRIPTIONCAT`)
    REFERENCES `prevarisc`.`prescriptioncat` (`ID_PRESCRIPTION_CAT`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptionarticle`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptionarticle` (
  `ID_PRESCRIPTIONARTICLE` BIGINT NOT NULL AUTO_INCREMENT,
  `LIBELLE_PRESCRIPTIONARTICLE` VARCHAR(255) NULL,
  `NUM_PRESCRIPTIONARTICLE` INT NULL,
  `ID_PRESCRIPTIONTEXTE` BIGINT NOT NULL,
  PRIMARY KEY (`ID_PRESCRIPTIONARTICLE`),
  INDEX `fk_prescriptionarticle_prescriptiontexte1_idx` (`ID_PRESCRIPTIONTEXTE` ASC),
  CONSTRAINT `fk_prescriptionarticle_prescriptiontexte1`
    FOREIGN KEY (`ID_PRESCRIPTIONTEXTE`)
    REFERENCES `prevarisc`.`prescriptiontexte` (`ID_PRESCRIPTIONTEXTE`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptiontexteliste`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptiontexteliste` (
  `ID_TEXTE` BIGINT NOT NULL AUTO_INCREMENT,
  `LIBELLE_TEXTE` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_TEXTE`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptionarticleliste`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptionarticleliste` (
  `ID_ARTICLE` BIGINT NOT NULL AUTO_INCREMENT,
  `LIBELLE_ARTICLE` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_ARTICLE`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptiontype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptiontype` (
  `ID_PRESCRIPTIONTYPE` BIGINT NOT NULL AUTO_INCREMENT,
  `PRESCRIPTIONTYPE_CATEGORIE` BIGINT NULL,
  `PRESCRIPTIONTYPE_TEXTE` BIGINT NULL,
  `PRESCRIPTIONTYPE_ARTICLE` BIGINT NULL,
  `PRESCRIPTIONTYPE_LIBELLE` TEXT NULL,
  PRIMARY KEY (`ID_PRESCRIPTIONTYPE`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptiontypeassoc`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptiontypeassoc` (
  `ID_PRESCRIPTIONTYPE` BIGINT NOT NULL,
  `NUM_PRESCRIPTIONASSOC` INT NOT NULL,
  `ID_TEXTE` BIGINT NOT NULL,
  `ID_ARTICLE` BIGINT NOT NULL,
  INDEX `fk_prescriptiontypeassoc_prescriptiontype1_idx` (`ID_PRESCRIPTIONTYPE` ASC),
  PRIMARY KEY (`ID_PRESCRIPTIONTYPE`, `NUM_PRESCRIPTIONASSOC`),
  CONSTRAINT `fk_prescriptiontypeassoc_prescriptiontype1`
    FOREIGN KEY (`ID_PRESCRIPTIONTYPE`)
    REFERENCES `prevarisc`.`prescriptiontype` (`ID_PRESCRIPTIONTYPE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptiondossier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptiondossier` (
  `ID_PRESCRIPTION_DOSSIER` BIGINT NOT NULL AUTO_INCREMENT,
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  `NUM_PRESCRIPTION_DOSSIER` INT NOT NULL,
  `ID_PRESCRIPTION_TYPE` BIGINT NULL,
  `LIBELLE_PRESCRIPTION_DOSSIER` TEXT NULL,
  PRIMARY KEY (`ID_PRESCRIPTION_DOSSIER`),
  UNIQUE INDEX `ID_PRESCRIPTION_DOSSIER_UNIQUE` (`ID_PRESCRIPTION_DOSSIER` ASC),
  INDEX `fk_prescriptiondossier_dossier1_idx` (`ID_DOSSIER` ASC),
  CONSTRAINT `fk_prescriptiondossier_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`prescriptiondossierassoc`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`prescriptiondossierassoc` (
  `NUM_PRESCRIPTION_DOSSIERASSOC` INT NOT NULL,
  `ID_PRESCRIPTION_DOSSIER` BIGINT NOT NULL,
  `ID_TEXTE` BIGINT NULL,
  `ID_ARTICLE` BIGINT NULL,
  PRIMARY KEY (`NUM_PRESCRIPTION_DOSSIERASSOC`, `ID_PRESCRIPTION_DOSSIER`),
  INDEX `fk_prescriptiondossierassoc_prescriptiondossier1_idx` (`ID_PRESCRIPTION_DOSSIER` ASC),
  CONSTRAINT `fk_prescriptiondossierassoc_prescriptiondossier1`
    FOREIGN KEY (`ID_PRESCRIPTION_DOSSIER`)
    REFERENCES `prevarisc`.`prescriptiondossier` (`ID_PRESCRIPTION_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`resources`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`resources` (
  `id_resource` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `text` TEXT NULL,
  PRIMARY KEY (`id_resource`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`privileges`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`privileges` (
  `id_privilege` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `text` VARCHAR(45) NULL,
  `id_resource` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id_privilege`),
  INDEX `fk_privileges_resources1_idx` (`id_resource` ASC),
  CONSTRAINT `fk_privileges_resources1`
    FOREIGN KEY (`id_resource`)
    REFERENCES `prevarisc`.`resources` (`id_resource`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`groupe-privileges`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`groupe-privileges` (
  `ID_GROUPE` BIGINT(20) UNSIGNED NOT NULL,
  `id_privilege` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_GROUPE`, `id_privilege`),
  INDEX `fk_groupe-privileges_privileges1_idx` (`id_privilege` ASC),
  INDEX `fk_groupe-privileges_groupe1_idx` (`ID_GROUPE` ASC),
  CONSTRAINT `fk_groupe-privileges_groupe1`
    FOREIGN KEY (`ID_GROUPE`)
    REFERENCES `prevarisc`.`groupe` (`ID_GROUPE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_groupe-privileges_privileges1`
    FOREIGN KEY (`id_privilege`)
    REFERENCES `prevarisc`.`privileges` (`id_privilege`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `prevarisc`.`docmanquant`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`docmanquant` (
  `ID_DOCMANQUANT` BIGINT NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOCMANQUANT` TEXT NOT NULL,
  PRIMARY KEY (`ID_DOCMANQUANT`),
  UNIQUE INDEX `ID_DOCMANQUANT_UNIQUE` (`ID_DOCMANQUANT` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`dossierdocmanquant`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`dossierdocmanquant` (
  `ID_DOCMANQUANT` BIGINT NOT NULL AUTO_INCREMENT,
  `ID_DOSSIER` BIGINT(20) NOT NULL,
  `NUM_DOCSMANQUANT` VARCHAR(45) NOT NULL,
  `DOCMANQUANT` TEXT NULL,
  `DATE_DOCSMANQUANT` DATE NULL,
  PRIMARY KEY (`ID_DOCMANQUANT`),
  INDEX `fk_dossierdocmanquant_dossier1_idx` (`ID_DOSSIER` ASC),
  UNIQUE INDEX `ID_DOCMANQUANT_UNIQUE` (`ID_DOCMANQUANT` ASC),
  CONSTRAINT `fk_dossierdocmanquant_dossier1`
    FOREIGN KEY (`ID_DOSSIER`)
    REFERENCES `prevarisc`.`dossier` (`ID_DOSSIER`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`commissionmembretypeactivite`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`commissionmembretypeactivite` (
  `ID_COMMISSIONMEMBRE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_TYPEACTIVITE` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`, `ID_TYPEACTIVITE`),
  INDEX `fk_commissionmembre-typeactivite_typeactivite1_idx` (`ID_TYPEACTIVITE` ASC),
  INDEX `fk_commissionmembre-typeactivite_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE` ASC),
  CONSTRAINT `fk_commissionmembre-typeactivite_commissionmembre1`
    FOREIGN KEY (`ID_COMMISSIONMEMBRE`)
    REFERENCES `prevarisc`.`commissionmembre` (`ID_COMMISSIONMEMBRE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionmembre-typeactivite_typeactivite1`
    FOREIGN KEY (`ID_TYPEACTIVITE`)
    REFERENCES `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`documenttype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`documenttype` (
  `ID_DOCUMENTTYPE` BIGINT(20) UNSIGNED NOT NULL,
  `PATH_DOCUMENTTYPE` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_DOCUMENTTYPE`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prevarisc`.`documenttypedossiernatures`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prevarisc`.`documenttypedossiernatures` (
  `ID_DOCUMENTTYPE` BIGINT(20) UNSIGNED NOT NULL,
  `ID_DOSSIERNATURE` BIGINT(20) NOT NULL,
  PRIMARY KEY (`ID_DOCUMENTTYPE`, `ID_DOSSIERNATURE`),
  INDEX `fk_documenttypedossiernatures_documenttype1_idx` (`ID_DOCUMENTTYPE` ASC),
  INDEX `fk_documenttypedossiernatures_dossiernatureliste1_idx` (`ID_DOSSIERNATURE` ASC),
  CONSTRAINT `fk_documenttypedossiernatures_documenttype1`
    FOREIGN KEY (`ID_DOCUMENTTYPE`)
    REFERENCES `prevarisc`.`documenttype` (`ID_DOCUMENTTYPE`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_documenttypedossiernatures_dossiernatureliste1`
    FOREIGN KEY (`ID_DOSSIERNATURE`)
    REFERENCES `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `prevarisc`.`avis`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`avis` (`ID_AVIS`, `LIBELLE_AVIS`) VALUES (1, 'Favorable');
INSERT INTO `prevarisc`.`avis` (`ID_AVIS`, `LIBELLE_AVIS`) VALUES (2, 'Défavorable');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`categorie`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`categorie` (`ID_CATEGORIE`, `LIBELLE_CATEGORIE`, `COMMENTAIRE_CATEGORIE`) VALUES (1, '1ère catégorie', 'Plus de 1500 personnes');
INSERT INTO `prevarisc`.`categorie` (`ID_CATEGORIE`, `LIBELLE_CATEGORIE`, `COMMENTAIRE_CATEGORIE`) VALUES (2, '2ème catégorie', 'De 701 à 1500 personnes');
INSERT INTO `prevarisc`.`categorie` (`ID_CATEGORIE`, `LIBELLE_CATEGORIE`, `COMMENTAIRE_CATEGORIE`) VALUES (3, '3ème catégorie', 'De 301 à 700 personnes');
INSERT INTO `prevarisc`.`categorie` (`ID_CATEGORIE`, `LIBELLE_CATEGORIE`, `COMMENTAIRE_CATEGORIE`) VALUES (4, '4ème catégorie', '300 personnes et moins');
INSERT INTO `prevarisc`.`categorie` (`ID_CATEGORIE`, `LIBELLE_CATEGORIE`, `COMMENTAIRE_CATEGORIE`) VALUES (5, '5ème catégorie', 'Nombre très réduit de personnes');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`classe`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (1, 'GHA');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (2, 'GHO');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (3, 'GHR');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (4, 'GHS');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (5, 'GHTC');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (6, 'GHU');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (7, 'GHW1');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (8, 'GHW2');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (9, 'GHZ');
INSERT INTO `prevarisc`.`classe` (`ID_CLASSE`, `LIBELLE_CLASSE`) VALUES (10, 'ITGH');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`commissiontype`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`commissiontype` (`ID_COMMISSIONTYPE`, `LIBELLE_COMMISSIONTYPE`) VALUES (1, 'Sous-commission départementale');
INSERT INTO `prevarisc`.`commissiontype` (`ID_COMMISSIONTYPE`, `LIBELLE_COMMISSIONTYPE`) VALUES (2, 'Commission communale');
INSERT INTO `prevarisc`.`commissiontype` (`ID_COMMISSIONTYPE`, `LIBELLE_COMMISSIONTYPE`) VALUES (3, 'Commission intercommunale');
INSERT INTO `prevarisc`.`commissiontype` (`ID_COMMISSIONTYPE`, `LIBELLE_COMMISSIONTYPE`) VALUES (4, 'Commission d\'arrondissement');
INSERT INTO `prevarisc`.`commissiontype` (`ID_COMMISSIONTYPE`, `LIBELLE_COMMISSIONTYPE`) VALUES (5, 'Divers');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`utilisateurcivilite`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`utilisateurcivilite` (`ID_UTILISATEURCIVILITE`, `LIBELLE_UTILISATEURCIVILITE`) VALUES (1, 'Monsieur');
INSERT INTO `prevarisc`.`utilisateurcivilite` (`ID_UTILISATEURCIVILITE`, `LIBELLE_UTILISATEURCIVILITE`) VALUES (2, 'Madame');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`fonction`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (1, 'Préfet');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (2, 'Maire');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (5, 'Pétitionnaire demandeur');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (3, 'Maitre d\'ouvrage');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (4, 'Maitre d\'oeuvre');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (6, 'Controller technique (organisme agréé)');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (7, 'Exploitant');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (8, 'Directeur unique de sécurité');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (9, 'Responsable de sécurité');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (10, 'Participant');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (11, 'Demandeur');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (12, 'Simple utilisateur');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (13, 'Préventionniste');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (15, 'Secrétariat');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (16, 'Service informatique');
INSERT INTO `prevarisc`.`fonction` (`ID_FONCTION`, `LIBELLE_FONCTION`) VALUES (99, 'Utilisateur spécial');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`utilisateurinformations`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`, `NOM_UTILISATEURINFORMATIONS`, `PRENOM_UTILISATEURINFORMATIONS`, `TELFIXE_UTILISATEURINFORMATIONS`, `TELPORTABLE_UTILISATEURINFORMATIONS`, `TELFAX_UTILISATEURINFORMATIONS`, `MAIL_UTILISATEURINFORMATIONS`, `SOCIETE_UTILISATEURINFORMATIONS`, `NUMEROADRESSE_UTILISATEURINFORMATIONS`, `RUEADRESSE_UTILISATEURINFORMATIONS`, `CPADRESSE_UTILISATEURINFORMATIONS`, `VILLEADRESSE_UTILISATEURINFORMATIONS`, `WEB_UTILISATEURINFORMATIONS`, `OBS_UTILISATEURINFORMATIONS`, `DATE_PRV2`, `DATE_RECYCLAGE`, `DATE_SID`, `ID_UTILISATEURCIVILITE`, `ID_FONCTION`) VALUES (1, 'ROOT', 'ROOT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 99);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`dossiertype`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`, `LIBELLE_DOSSIERTYPE`) VALUES (1, 'Étude');
INSERT INTO `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`, `LIBELLE_DOSSIERTYPE`) VALUES (2, 'Visite de commission');
INSERT INTO `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`, `LIBELLE_DOSSIERTYPE`) VALUES (3, 'Groupe de visite');
INSERT INTO `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`, `LIBELLE_DOSSIERTYPE`) VALUES (4, 'Réunion');
INSERT INTO `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`, `LIBELLE_DOSSIERTYPE`) VALUES (5, 'Courrier / Courriel');
INSERT INTO `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`, `LIBELLE_DOSSIERTYPE`) VALUES (6, 'Intervention');
INSERT INTO `prevarisc`.`dossiertype` (`ID_DOSSIERTYPE`, `LIBELLE_DOSSIERTYPE`) VALUES (7, 'Arrêté');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`dossiernatureliste`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (1, 'Permis de construire (PC)', 1, 2);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (2, 'Autorisation de travaux (AT)', 1, 1);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (3, 'Dérogation', 1, 11);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (4, 'Cahier des charges fonctionnel du SSI', 1, 12);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (5, 'Cahier des charges de type T', 1, 16);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (6, 'Salon type T', 1, 7);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (7, 'Levée de prescriptions', 1, 5);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (8, 'Documents divers', 1, 22);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (9, 'Changement de DUS (Directeur unique de sécurité)', 1, 8);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (10, 'Suivi organisme formation SSIAP', 1, 9);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (11, 'Demande de registre de sécurité CTS', 1, 15);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (12, 'Demande d\'implantation CTS < 6mois', 1, 6);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (13, 'Demande d\'implantation CTS > 6mois', 1, 14);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (14, 'Permis d\'aménager', 1, 18);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (15, 'Permis de démolir', 1, 19);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (16, 'CR de visite des organismes d\'inspection de sécurité incendie (GA)', 1, 17);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (17, 'Etude suite à un avis \"différé\"', 1, 13);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (18, 'Utilisation exceptionnelle de locaux', 1, 4);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (19, 'Levée de réserves suite à un avis défavorable', 1, 3);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (20, 'Réception de travaux', 2, 2);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (21, 'Périodique', 2, 1);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (22, 'Chantier', 2, 6);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (23, 'Contrôle', 2, 4);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (24, 'Inopinée', 2, 5);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (25, 'Réception de travaux', 3, 2);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (26, 'Périodique', 3, 1);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (27, 'Chantier', 3, 6);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (28, 'Contrôle', 3, 4);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (29, 'Inopinée', 3, 5);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (30, 'Déclaration préalable', 1, 20);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (31, 'Locaux SDIS', 4, 1);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (32, 'Extérieur SDIS', 4, 2);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (33, 'RVRMD (diag sécu)', 1, 21);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (34, 'Arrivée', 5, 1);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (35, 'Départ', 5, 2);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (36, 'En transit (gestion des dossiers en interne vers d\'autres structures, ect, ...)', 5, 3);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (37, 'Incendie', 6, 1);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (38, 'SAP', 6, 2);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (39, 'Inter. div.', 6, 3);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (40, 'Ouverture', 7, 1);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (41, 'Fermeture', 7, 2);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (42, 'Mise en demeure', 7, 3);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (43, 'Téléphonique', 4, 3);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (44, 'Utilisation exceptionnelle de locaux', 7, 4);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (45, 'Courrier', 7, NULL);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (46, 'Echéancier de travaux', 1, 10);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (47, 'Avant ouverture', 2, 3);
INSERT INTO `prevarisc`.`dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (48, 'Avant ouverture', 3, 3);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`type`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (1, 'CTS');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (2, 'EF');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (3, 'EM');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (4, 'EP');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (5, 'GA');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (6, 'GEEM');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (7, 'J');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (8, 'L');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (9, 'M');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (10, 'N');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (11, 'O');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (12, 'OA');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (13, 'P');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (14, 'PA');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (15, 'PE2§2');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (16, 'PS');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (17, 'R');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (18, 'REF');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (19, 'S');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (20, 'SG');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (21, 'T');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (22, 'U');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (23, 'V');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (24, 'W');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (25, 'X');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (26, 'Y');
INSERT INTO `prevarisc`.`type` (`ID_TYPE`, `LIBELLE_TYPE`) VALUES (27, 'Z');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`commissiontypeevenement`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`commissiontypeevenement` (`ID_COMMISSIONTYPEEVENEMENT`, `LIBELLE_COMMISSIONTYPEEVENEMENT`) VALUES (1, 'Salle');
INSERT INTO `prevarisc`.`commissiontypeevenement` (`ID_COMMISSIONTYPEEVENEMENT`, `LIBELLE_COMMISSIONTYPEEVENEMENT`) VALUES (2, 'Visite de sécurité');
INSERT INTO `prevarisc`.`commissiontypeevenement` (`ID_COMMISSIONTYPEEVENEMENT`, `LIBELLE_COMMISSIONTYPEEVENEMENT`) VALUES (3, 'Groupe de visite');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`groupe`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`groupe` (`ID_GROUPE`, `LIBELLE_GROUPE`, `DESC_GROUPE`) VALUES (1, 'Groupe par défaut', 'Ceci est le groupe qui contient les nouveaux utilisateurs et les utilisateurs dont les groupes ont été supprimés.');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`utilisateur`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`utilisateur` (`ID_UTILISATEUR`, `USERNAME_UTILISATEUR`, `PASSWD_UTILISATEUR`, `LASTACTION_UTILISATEUR`, `ACTIF_UTILISATEUR`, `NUMINSEE_COMMUNE`, `ID_UTILISATEURINFORMATIONS`, `ID_GROUPE`) VALUES (1, 'root', '0ab182b5717693a278cd986898742e76', NULL, 1, NULL, 1, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`genre`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (1, 'Site');
INSERT INTO `prevarisc`.`genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (2, 'Établissement');
INSERT INTO `prevarisc`.`genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (3, 'Cellule');
INSERT INTO `prevarisc`.`genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (4, 'Habitation');
INSERT INTO `prevarisc`.`genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (5, 'IGH');
INSERT INTO `prevarisc`.`genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (6, 'EIC');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`famille`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`famille` (`ID_FAMILLE`, `LIBELLE_FAMILLE`) VALUES (2, '1ère famille');
INSERT INTO `prevarisc`.`famille` (`ID_FAMILLE`, `LIBELLE_FAMILLE`) VALUES (3, '2ème famille');
INSERT INTO `prevarisc`.`famille` (`ID_FAMILLE`, `LIBELLE_FAMILLE`) VALUES (4, '2ème famille collective');
INSERT INTO `prevarisc`.`famille` (`ID_FAMILLE`, `LIBELLE_FAMILLE`) VALUES (5, '3ème famille A');
INSERT INTO `prevarisc`.`famille` (`ID_FAMILLE`, `LIBELLE_FAMILLE`) VALUES (6, '3ème famille B');
INSERT INTO `prevarisc`.`famille` (`ID_FAMILLE`, `LIBELLE_FAMILLE`) VALUES (7, '4ème famille');
INSERT INTO `prevarisc`.`famille` (`ID_FAMILLE`, `LIBELLE_FAMILLE`) VALUES (1, 'Aucune');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`typeactivite`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (1, 'Châpiteau', 1);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (2, 'Structures', 1);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (3, 'Tentes', 1);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (4, 'Bateaux en stationnement sur les eaux intérieures', 2);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (5, 'Bateaux stationnaires', 2);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (6, 'Etablissements flottants', 2);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (7, 'Gares', 5);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (8, 'Etablissements d’enseignement avec internat pour jeunes handicapés ou inadaptés', 7);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (9, 'Etablissements d’hébergement pour adultes handicapés', 7);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (10, 'Etablissements médico-éducatifs avec internat pour jeunes handicapés ou inadaptés', 7);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (11, 'Structures d’accueil pour personnes âgées', 7);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (12, 'Structures d’accueil pour personnes handicapées', 7);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (13, 'Cabarets', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (14, 'Cinéma', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (15, 'Cirques non forains', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (16, 'Salles d\'audition', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (17, 'Salle de conférences', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (18, 'Salles de pari', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (19, 'Salles de projection', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (20, 'Salles de quartier (ou assimilée)', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (21, 'Salles de réunions', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (22, 'Salles de spectacles', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (23, 'Salles multimédia', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (24, 'Salles polyvalentes à dominante sportive, dont la superficie unitaire est supérieure ou égale à 1 200 m2', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (25, 'Salles polyvalentes non visée par le Type X (salle polyvalente qui n’a pas une destination unique)', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (26, 'Salles réservées aux associations', 8);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (27, 'Aires de vente', 9);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (28, 'Boutiques', 9);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (29, 'Centres commerciaux', 9);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (30, 'Locaux de vente', 9);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (31, 'Magasin de vente', 9);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (32, 'Bars', 10);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (33, 'Brasseries', 10);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (34, 'Cafétaria', 10);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (35, 'Cafés', 10);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (36, 'Cantines', 10);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (37, 'Débits de boissons', 10);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (38, 'Restaurants', 10);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (39, 'Hôtels', 11);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (40, 'Motels', 11);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (41, 'Pensions de famille', 11);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (42, 'Hôtels-restaurants d’altitude', 12);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (43, 'Bals', 13);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (46, 'Salles de danse', 13);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (47, 'Salles de jeux', 13);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (48, 'Arènes', 14);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (49, 'Hippodromes', 14);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (50, 'Piscines', 14);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (51, 'Pistes de patinage', 14);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (52, 'Stades', 14);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (53, 'Terrains de sport', 14);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (54, 'Parcs de stationnement couverts', 16);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (55, 'Auberges de jeunesse (comprenant au moins un local collectif à sommeil)', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (56, 'Auto-écoles', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (57, 'Centres aérés', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (58, 'Centres de loisirs (sans hébergement)', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (59, 'Centres de vacances', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (60, 'Colonies de vacances', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (61, 'Crèches', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (62, 'Ecoles maternelles', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (63, 'Etablissements d’enseignement', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (64, 'Etablissements de formation', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (65, 'Haltes-garderies', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (66, 'Internats des établissements de l\'enseignement primaire et secondaire', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (67, 'Jardins d\'enfant', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (68, 'Lycee public', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (69, 'Refuges de montagne', 18);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (70, 'Bibliothèques', 19);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (71, 'Centres de documentation et de consultation d’archives', 19);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (72, 'Structures gonflables', 20);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (73, 'Etablissements à vocation commerciale destinés à des expositions', 21);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (74, 'Foires-expositions', 21);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (75, 'Salles d’exposition à caractère permanent n’ayant pas une vocation de foire ou de salons ', 21);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (76, 'Salles d’expositions de véhicules automobiles, bateaux, machines et autres volumineux biens d’équipements assimilables', 21);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (77, 'Salons à caractère temporaire', 21);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (78, 'Etablissements de cure thermale ou de thalassothérapie', 22);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (79, 'Etablissements de santé publics ou privés dispensant des soins de courte durée en médecine, chirurgie, obstétrique', 22);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (80, 'Etablissements de santé publics ou privés dispensant des soins de psychiatrie, de suite ou de réadaptation, des soins de longue durée, à des personnes n\'ayant pas leur autonomie de vie dont l\'état nécessite une surveillance médicale constante', 22);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (81, 'Etablissements de thalassothérapie', 22);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (82, 'Pouponnières', 22);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (83, 'Eglises', 23);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (84, 'Mosquées', 23);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (85, 'Synagogues', 23);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (86, 'Temples', 23);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (87, 'Administrations', 24);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (88, 'Banques', 24);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (89, 'Bureaux', 24);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (90, 'Hôtels de ville', 24);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (91, 'Manèges', 25);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (92, 'Patinoires', 25);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (93, 'Piscines couvertes, transformables et mixtes', 25);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (94, 'Salles d\'éducation physique et sportive', 25);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (95, 'Salles omnisports', 25);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (96, 'Salles polyvalentes à dominante sportive, dont l\'aire d\'activité est inférieure à 1200 2 et la hauteur sous plafond supérieure ou égale à 6,50 mètres, etc', 25);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (97, 'Salles sportives spécialisées', 25);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (98, 'Musées', 26);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (99, 'Salles destinées à recevoir des expositions à vocation culturelle, scientifique, technique, artistique, etc. ayant un caractère temporaire', 26);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (101, 'Collège public', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (103, 'En attente de classement', 27);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (104, 'Parc d\'attraction', 14);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (105, 'Locaux à usage collectif d\'une surface unitaire supérieure à 50 mètres carrés des logements-foyers et de l\'habitat de loisirs à gestion collective', 15);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (106, 'Bâtiments ou locaux à usage d\'hébergement qui ne relèvent d\'aucun type défini à l\'article GN 1 et qui permettent d\'accueillir plus de 15 et moins de 100 personnes n\'y élisant pas domicile', 15);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (107, 'Hébergement de mineurs en dehors de leurs familles, le seuil de l\'effectif est fixé à 7 mineurs', 15);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (108, 'Maisons d\'assistants maternels (MAM) dont les locaux accessibles au public sont strictement limités à un seul étage sur rez-de-chaussée et dont l\'effectif ne dépasse pas 16 enfants', 15);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (109, 'Ecoles primaires', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (110, 'Lycee privé', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (111, 'Collège privé', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (112, 'Lycée agricole', 17);
INSERT INTO `prevarisc`.`typeactivite` (`ID_TYPEACTIVITE`, `LIBELLE_ACTIVITE`, `ID_TYPE`) VALUES (113, 'Lycée maritime', 17);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`statut`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`statut` (`ID_STATUT`, `LIBELLE_STATUT`) VALUES (1, 'Projet');
INSERT INTO `prevarisc`.`statut` (`ID_STATUT`, `LIBELLE_STATUT`) VALUES (2, 'Ouvert');
INSERT INTO `prevarisc`.`statut` (`ID_STATUT`, `LIBELLE_STATUT`) VALUES (3, 'Fermé');
INSERT INTO `prevarisc`.`statut` (`ID_STATUT`, `LIBELLE_STATUT`) VALUES (4, 'Itinérant / Périodique');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`typeplan`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`typeplan` (`ID_TYPEPLAN`, `LIBELLE_TYPEPLAN`) VALUES (1, 'Plan ER');
INSERT INTO `prevarisc`.`typeplan` (`ID_TYPEPLAN`, `LIBELLE_TYPEPLAN`) VALUES (2, 'PPI');
INSERT INTO `prevarisc`.`typeplan` (`ID_TYPEPLAN`, `LIBELLE_TYPEPLAN`) VALUES (3, 'POI');
INSERT INTO `prevarisc`.`typeplan` (`ID_TYPEPLAN`, `LIBELLE_TYPEPLAN`) VALUES (4, 'PPMS');
INSERT INTO `prevarisc`.`typeplan` (`ID_TYPEPLAN`, `LIBELLE_TYPEPLAN`) VALUES (5, 'Plan de sauvegarde des oeuvres');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`groupementtype`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (1, 'Département');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (2, 'Arrondissement');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (3, 'Canton');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (4, 'Intercommunalité');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (5, 'Groupement territorial');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (6, 'Centre de secours');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (7, 'Secrétariat prévention SDIS');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (8, 'Secteur de prévention');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (9, 'Secrétariat prévision');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (10, 'Service instructeur');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (11, 'DDSP');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (12, 'Gendarmerie');
INSERT INTO `prevarisc`.`groupementtype` (`ID_GROUPEMENTTYPE`, `LIBELLE_GROUPEMENTTYPE`) VALUES (13, 'Autre service');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`listedocconsulte`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (1, 'Un courrier de', 0, 1, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (2, 'Un jeu de plans', 0, 1, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (3, 'Une notice de sécurité', 0, 1, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (4, 'Une notice descriptive', 0, 1, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (5, 'Un engagement solidité', 0, 1, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (6, 'Un rapport initial de contrôle technique', 0, 1, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (7, 'Une attestation du directeur unique de sécurité', 0, 1, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (8, 'Relevé de vérification du désenfumage (Art. DF 10)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (9, 'RVRE triennal du désenfumage mécanique associé à un SSI A ou B', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (10, 'Relevé de vérification du chauffage-ventilation (Art. CH 58)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (11, 'Attestation de ramonage ou visite des conduits (Art. CH 57)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (12, 'Relevé de vérification du gaz (Art. GZ 30)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (13, 'Relevé de vérification des fluides médicaux (Art. U 64 ou J 33)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (14, 'Relevé ou RVRE des installations électriques et/ou des paratonnerres ou protection contre la foudre (Art. EL 19)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (15, 'RVRE quinquennal des ascenseurs (Art. AS 9)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (16, 'Relevé de vérification des ascenseurs (Art. AS 9) et/ou escaliers mécaniques et trottoirs roulants (Art. AS 10)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (17, 'Contrat d’entretien des ascenseurs, escaliers mécaniques et trottoirs roulants (Art. AS 8)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (18, 'Attestation de nettoyage du circuit d’extraction (Art. GC 21)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (19, 'Relevé de vérification des appareils de cuisson (Art. GC 22)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (20, 'Relevé de vérification des extincteurs (Art. MS 38)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (21, 'Révision décennal des extincteurs (Art. MS 38)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (22, 'Relevé de vérification des colonnes sèches (Art. MS 73)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (23, 'Relevé de vérification des R.I.A. (Art. MS 73)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (24, 'Relevé de vérification du système d’extinction automatique du type sprinkleur (Art. MS 29/73) ou déversoirs et rideaux d’eau (L 57)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (25, 'RVRE triennal du sprinkleur (Art. MS 73)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (26, 'RVRE triennal du SSI A ou B (Art. MS 73)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (27, 'Relevé de vérification du SSI  ou  d’équipement  d’alarme,  détection,  portes, clapets coupe-feu (Art. MS 73)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (28, 'Contrat d’entretien du Système de Sécurité Incendie (Art. MS 58)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (29, 'Dossier d’identité du Système de Sécurité Incendie (Art. MS 73)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (30, 'Attestation de vérification des communications radioélectriques (Art. MS 71)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (31, 'RVRE triennal des moyens de communications radioélectriques mode relayés (Art. MS 71)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (32, 'Relevé de vérification des poteaux d’incendie privés', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (33, 'Relevé de vérification des portes automatiques en façade (Art. GE 6)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (34, 'Contrat d’entretien des portes automatiques en façade (Art. CO 48)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (35, 'RVRE des équipements de levage des salles avec espace scénique (L 57)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (36, 'RVRE triennal des salles de spectacles avec espace scénique (L 57)', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (37, 'Dossier technique amiante', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (38, 'Exercice d’évacuation réalisé', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (39, 'Formation des personnels à l’utilisation des moyens de secours', 1, 0, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (40, 'Cahier des charges fonctionnelles du SSI', 0, 1, 0, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (41, 'Attestation de solidité', 0, 0, 1, 1);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (42, 'Attestation du maître d\'ouvrage', 0, 0, 1, 1);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (43, 'RVRAT', 0, 0, 1, 1);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (44, 'Attestation de réception des hydrants', 0, 0, 1, 1);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (45, 'Dossier d\'identité SSI', 0, 0, 1, 1);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (46, 'PV de réception du SSI', 0, 0, 1, 1);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (47, 'Mise à jour du dossier d\'identité du SSI', 0, 0, 1, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (48, 'Attestation de réception de modification sprinkleur', 0, 0, 1, 0);
INSERT INTO `prevarisc`.`listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`) VALUES (49, 'Attestation de réception de modification de détection incendie', 0, 0, 1, 0);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`periodicite`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '1', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '10', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '11', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '2', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '3', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '4', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '5', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '6', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '7', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '8', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (0, '9', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '1', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '1', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '10', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '10', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '11', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '11', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '12', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '12', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '13', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '13', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '14', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '14', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '15', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '15', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '16', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '16', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '17', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '17', 1, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '18', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '18', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '19', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '19', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '2', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '2', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '20', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '20', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '21', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '21', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '22', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '22', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '23', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '23', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '24', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '24', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '25', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '25', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '26', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '26', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '27', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '27', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '3', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '3', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '4', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '4', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '5', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '5', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '6', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '6', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '7', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '7', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '8', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '8', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '9', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (1, '9', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '1', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '1', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '10', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '10', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '11', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '11', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '12', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '12', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '13', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '13', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '14', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '14', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '15', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '15', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '16', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '16', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '17', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '17', 1, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '18', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '18', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '19', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '19', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '2', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '2', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '20', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '20', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '21', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '21', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '22', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '22', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '23', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '23', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '24', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '24', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '25', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '25', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '26', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '26', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '27', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '27', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '3', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '3', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '4', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '4', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '5', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '5', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '6', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '6', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '7', 0, 24);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '7', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '8', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '8', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '9', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (2, '9', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '1', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '1', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '10', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '10', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '11', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '11', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '12', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '12', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '13', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '13', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '14', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '14', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '15', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '15', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '16', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '16', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '17', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '17', 1, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '18', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '18', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '19', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '19', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '2', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '2', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '20', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '20', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '21', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '21', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '22', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '22', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '23', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '23', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '24', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '24', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '25', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '25', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '26', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '26', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '27', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '27', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '3', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '3', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '4', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '4', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '5', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '5', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '6', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '6', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '7', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '7', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '8', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '8', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '9', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (3, '9', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '1', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '1', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '10', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '10', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '11', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '11', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '12', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '12', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '13', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '13', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '14', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '14', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '15', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '15', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '16', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '16', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '17', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '17', 1, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '18', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '18', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '19', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '19', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '2', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '2', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '20', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '20', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '21', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '21', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '22', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '22', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '23', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '23', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '24', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '24', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '25', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '25', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '26', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '26', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '27', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '27', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '3', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '3', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '4', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '4', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '5', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '5', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '6', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '6', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '7', 0, 36);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '7', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '8', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '8', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '9', 0, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (4, '9', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '1', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '1', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '10', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '10', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '11', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '11', 1, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '12', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '12', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '13', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '13', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '14', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '14', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '15', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '15', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '16', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '16', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '17', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '17', 1, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '18', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '18', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '19', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '19', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '2', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '2', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '20', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '20', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '21', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '21', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '22', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '22', 1, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '23', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '23', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '24', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '24', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '25', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '25', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '26', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '26', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '27', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '27', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '3', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '3', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '4', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '4', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '5', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '5', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '6', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '6', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '7', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '7', 1, 60);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '8', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '8', 1, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '9', 0, 0);
INSERT INTO `prevarisc`.`periodicite` (`ID_CATEGORIE`, `ID_TYPE`, `LOCALSOMMEIL_PERIODICITE`, `PERIODICITE_PERIODICITE`) VALUES (5, '9', 1, 0);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`typetextesappl`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`typetextesappl` (`ID_TYPETEXTEAPPL`, `LIBELLE_TYPETEXTEAPPL`) VALUES (1, 'Dispositions générales');
INSERT INTO `prevarisc`.`typetextesappl` (`ID_TYPETEXTEAPPL`, `LIBELLE_TYPETEXTEAPPL`) VALUES (2, 'Dispositions établissement de 5ème Cat');
INSERT INTO `prevarisc`.`typetextesappl` (`ID_TYPETEXTEAPPL`, `LIBELLE_TYPETEXTEAPPL`) VALUES (3, 'Dispositions particulières');
INSERT INTO `prevarisc`.`typetextesappl` (`ID_TYPETEXTEAPPL`, `LIBELLE_TYPETEXTEAPPL`) VALUES (4, 'Dispositions spéciales');
INSERT INTO `prevarisc`.`typetextesappl` (`ID_TYPETEXTEAPPL`, `LIBELLE_TYPETEXTEAPPL`) VALUES (5, 'Textes relatifs aux bâtiments d’habitation');
INSERT INTO `prevarisc`.`typetextesappl` (`ID_TYPETEXTEAPPL`, `LIBELLE_TYPETEXTEAPPL`) VALUES (6, 'Textes relatifs aux immeubles de grande hauteur');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`textesappl`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (2, 'Décret n°95-260 du 8 mars 1995 modifié relatif à la Commission Consultative Départementale de Sécurité et d’Accessibilté', 1, 1);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (3, 'Règlement de sécurité contre l’incendie du 23 mars 1965 modifié, relatif aux établissements recevant du public', 1, 1);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (4, 'Arrêté du 25 juin 1980 modifié, relatif aux dispositions générales applicables aux établissements recevant du public et instructions techniques annexées', 1, 1);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (5, 'Arrêté du 22 Juin 1990 modifié (dispositions particulières applicables aux établissements de 5ième catégorie PE)', 1, 2);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (6, 'Arrêté du 26 octobre 2011 modifié (dispositions particulières applicables aux petits hôtels PO)', 1, 2);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (7, 'Arrêté du 10 décembre 2004 (dispositions particulières applicables aux petits établissements de soins PU)', 1, 2);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (8, 'Arrêté du 20 novembre 2000 (dispositions particulières applicables aux établissements sportifs PX)', 1, 2);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (9, 'Arrêté du 19 novembre 2001 modifié (dispositions particulières applicables aux établissements du type J)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (10, 'Arrêté du 5 février 2007 modifié (dispositions particulières applicables aux établissements du type L)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (11, 'Arrêté du 22 décembre 1981 modifié (dispositions particulières applicables aux établissements du type M)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (12, 'Arrêté du 21 juin 1982 modifié (dispositions particulières applicables aux établissements du type N)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (13, 'Arrêté du 21 juin 1982 modifié (dispositions particulières applicables aux établissements du type O)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (14, 'Arrêté du 7 juillet 1983 modifié (dispositions particulières applicables aux établissements du type P)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (15, 'Arrêté du 4 juin 1982 modifié (dispositions particulières applicables aux établissements du type R)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (16, 'Arrêté du 12 juin 1995 modifié (dispositions particulières applicables aux établissements du type S)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (17, 'Arrêté du 18 novembre 1987 modifié (dispositions particulières applicables aux établissements du type T)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (18, 'Arrêté du 10 décembre 2004 modifié (dispositions particulières applicables aux établissements du type U)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (19, 'Arrêté du 21 avril 1983 modifié (dispositions particulières applicables aux établissements du type V)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (20, 'Arrêté du 21 avril 1983 modifié (dispositions particulières applicables aux établissements du type W)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (21, 'Arrêté du 4 juin 1982 modifié (dispositions particulières applicables aux établissements du type X)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (22, 'Arrêté du 12 juin 1995 modifié (dispositions particulières applicables aux établissements du type Y)', 1, 3);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (23, 'Arrêté du 23 janvier 1985 modifié (dispositions particulières applicables aux établissements du type CTS)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (24, 'Arrêté du 6 janvier 1983 modifié (dispositions particulières applicables aux établissements du type PA)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (25, 'Arrêté du 6 janvier 1983 modifié (dispositions particulières applicables aux établissements du type SG)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (26, 'Arrêté du 9 janvier 1990 modifié (dispositions particulières applicables aux établissements du type EF)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (27, 'Arrêté du 24 décembre 2007 (dispositions particulières applicables aux établissements du type GA)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (28, 'Arrêté du 9 mai 2006 (dispositions particulières applicables aux établissements du type PS)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (29, 'Arrêté du 23 octobre 1986 modifié (dispositions particulières applicables aux établissements du type OA)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (30, 'Arrêté du 10 novrembre 1994 modifié (dispositions particulières applicables aux établissements du type REF)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (31, 'Arrêté du 18 juillet 2006 (règles de sécurité contre les risques d’incendie et de panique dans les établissements pénitentiaires)', 1, 4);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (32, 'Arrêté du 31 janvier 1986 modifié (Habitations)', 1, 5);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (33, 'Code de la Construction et de l’Habitation - Articles R 123.1 à R 123.55 (ERP)', 1, 1);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (34, 'Code de la Construction et de l’Habitation - Articles R 122.1 à R 122.29 (IGH)', 1, 1);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (35, 'Arrêté du 18 octobre 1977 modifié portant règlement de sécurité pour la construction des immeubles de grande hauteur et leur protection contre les risques d\'incendie et de panique', 1, 6);
INSERT INTO `prevarisc`.`textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`) VALUES (36, 'Arrêté du 30 décembre 2011 portant règlement de sécurité pour la construction des immeubles de grande hauteur et leur protection contre les risques d\'incendie et de panique', 1, 6);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`prescriptiontexteliste`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`prescriptiontexteliste` (`ID_TEXTE`, `LIBELLE_TEXTE`) VALUES (1, '');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`prescriptionarticleliste`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`prescriptionarticleliste` (`ID_ARTICLE`, `LIBELLE_ARTICLE`) VALUES (1, '');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`resources`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`resources` (`id_resource`, `name`, `text`) VALUES (1, 'gestion_parametrages', 'Gestion et Paramétrages');
INSERT INTO `prevarisc`.`resources` (`id_resource`, `name`, `text`) VALUES (2, 'commission', 'Gestion des commissions');
INSERT INTO `prevarisc`.`resources` (`id_resource`, `name`, `text`) VALUES (3, 'creations', 'Gestion des droits de création');

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`privileges`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (1, 'docs', 'Gestion des documents', 1);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (2, 'groupement_communes', 'Gestion des groupements de communes', 1);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (3, 'gestion_prescriptions', 'Gestion des prescriptions', 1);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (4, 'gestion_textes_applicables', 'Gestion des textes applicables', 1);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (5, 'fil_actus', 'Écriture dans le fil d\'actualités', 1);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (6, 'gestion_commissions', 'Gestion des commissions', 2);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (7, 'lecture', 'Lecture', 2);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (8, 'ecriture', 'Écriture', 2);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (9, 'creation', 'Création', 2);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (10, 'modification_odj', 'Modification de l\'Ordre du Jour', 2);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (11, 'admin', 'Accès à l\'administration', 1);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (12, 'communes', 'Gestion des communes', 1);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (13, 'periodicites', 'Gestion des périodicités', 1);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (14, 'add_etablissement', 'Création d\'un établissement', 3);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (15, 'add_dossier', 'Création d\'un dossier', 3);
INSERT INTO `prevarisc`.`privileges` (`id_privilege`, `name`, `text`, `id_resource`) VALUES (16, 'generation_doc_com', 'Génération des documents de la commission', 2);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`groupe-privileges`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 1);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 2);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 3);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 4);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 5);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 6);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 7);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 8);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 9);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 10);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 11);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 12);
INSERT INTO `prevarisc`.`groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES (1, 13);

COMMIT;


-- -----------------------------------------------------
-- Data for table `prevarisc`.`docmanquant`
-- -----------------------------------------------------
START TRANSACTION;
USE `prevarisc`;
INSERT INTO `prevarisc`.`docmanquant` (`ID_DOCMANQUANT`, `LIBELLE_DOCMANQUANT`) VALUES (1, 'Absence du plan masse – général – niveaux – coupe');
INSERT INTO `prevarisc`.`docmanquant` (`ID_DOCMANQUANT`, `LIBELLE_DOCMANQUANT`) VALUES (2, 'Absence de la notice descriptive des matériaux utilisés (gros œuvre ou aménagements intérieurs).');
INSERT INTO `prevarisc`.`docmanquant` (`ID_DOCMANQUANT`, `LIBELLE_DOCMANQUANT`) VALUES (3, 'Absence de la notice de sécurité.');
INSERT INTO `prevarisc`.`docmanquant` (`ID_DOCMANQUANT`, `LIBELLE_DOCMANQUANT`) VALUES (4, 'Notice de sécurité incomplète (ne pas omettre de préciser les puissances des installations techniques : électricité, chauffage, appareils de cuisson, etc.).');
INSERT INTO `prevarisc`.`docmanquant` (`ID_DOCMANQUANT`, `LIBELLE_DOCMANQUANT`) VALUES (5, 'Absence de transmission par la mairie');
INSERT INTO `prevarisc`.`docmanquant` (`ID_DOCMANQUANT`, `LIBELLE_DOCMANQUANT`) VALUES (6, 'Absence de transmission par le directeur unique de sécurité');
INSERT INTO `prevarisc`.`docmanquant` (`ID_DOCMANQUANT`, `LIBELLE_DOCMANQUANT`) VALUES (7, 'Absence du CERFA');
INSERT INTO `prevarisc`.`docmanquant` (`ID_DOCMANQUANT`, `LIBELLE_DOCMANQUANT`) VALUES (8, 'CERFA non signé');

COMMIT;

