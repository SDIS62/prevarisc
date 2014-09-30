SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;

USE `PRV_prevarisc_v2` ;

-- On vire la table de liaison
DROP TABLE IF EXISTS `PRV_prevarisc_v2`.`etablissementclassement` ;

-- -----------------------------------------------------
-- Table `PRV_prevarisc_v2`.`classement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `PRV_prevarisc_v2`.`classement` ;

CREATE TABLE IF NOT EXISTS `PRV_prevarisc_v2`.`classement` (
  `ID_CLASSEMENT` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_CLASSEMENT` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_CLASSEMENT`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table `PRV_prevarisc_v2`.`etablissementclassement`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `PRV_prevarisc_v2`.`etablissementclassement` (
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_CLASSEMENT` INT(11) UNSIGNED NOT NULL,
  INDEX `fk_etablissementclassement_classement1_idx` (`ID_CLASSEMENT` ASC),
  INDEX `fk_etablissementclassement_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  PRIMARY KEY (`ID_ETABLISSEMENT`),
  CONSTRAINT `fk_etablissementclassement_classement1`
    FOREIGN KEY (`ID_CLASSEMENT`)
    REFERENCES `PRV_prevarisc_v2`.`classement` (`ID_CLASSEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `etablissementclassement_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `PRV_prevarisc_v2`.`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `PRV_prevarisc_v2`.`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(1, "Artisanale");
INSERT INTO `PRV_prevarisc_v2`.`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(2, "Commerciale");
INSERT INTO `PRV_prevarisc_v2`.`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(3, "Industrielle");
INSERT INTO `PRV_prevarisc_v2`.`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(4, "Lotissement");
INSERT INTO `PRV_prevarisc_v2`.`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(5, "Autre");