SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;

-- On vire la table de liaison
DROP TABLE IF EXISTS`etablissementclassement` ;

-- -----------------------------------------------------
-- Table`classement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS`classement` ;

CREATE TABLE IF NOT EXISTS`classement` (
  `ID_CLASSEMENT` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_CLASSEMENT` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_CLASSEMENT`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table`etablissementclassement`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS`etablissementclassement` (
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_CLASSEMENT` INT(11) UNSIGNED NOT NULL,
  INDEX `fk_etablissementclassement_classement1_idx` (`ID_CLASSEMENT` ASC),
  INDEX `fk_etablissementclassement_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  PRIMARY KEY (`ID_ETABLISSEMENT`),
  CONSTRAINT `fk_etablissementclassement_classement1`
    FOREIGN KEY (`ID_CLASSEMENT`)
    REFERENCES`classement` (`ID_CLASSEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `etablissementclassement_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES`etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(1, "Artisanale");
INSERT INTO`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(2, "Commerciale");
INSERT INTO`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(3, "Industrielle");
INSERT INTO`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(4, "Lotissement");
INSERT INTO`classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(5, "Autre");


ALTER TABLE`etablissementinformations` ADD COLUMN `ID_CLASSEMENT` int(11) unsigned DEFAULT NULL;
