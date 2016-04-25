-- -----------------------------------------------------
-- Mise à jour des tables : `prescriptiontexteliste` ; `prescriptionarticleliste` ; `prescriptiondossier`
-- -----------------------------------------------------
ALTER TABLE `prescriptiontexteliste` ADD `VISIBLE_TEXTE` tinyint(1) DEFAULT '1';
ALTER TABLE `prescriptionarticleliste` ADD `VISIBLE_ARTICLE` tinyint(1) DEFAULT '1';
ALTER TABLE `prescriptiondossier` ADD `TYPE_PRESCRIPTION_DOSSIER` tinyint(1) DEFAULT '1';

-- -----------------------------------------------------
-- Table`prescriptionregl`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `prescriptionregl` (
  `ID_PRESCRIPTIONREGL` bigint(20) NOT NULL AUTO_INCREMENT,
  `PRESCRIPTIONREGL_TYPE` tinyint(1) DEFAULT NULL,
  `PRESCRIPTIONREGL_LIBELLE` text,
  `PRESCRIPTIONREGL_VISIBLE` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`ID_PRESCRIPTIONREGL`)
) 
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table`prescriptionreglassoc`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `prescriptionreglassoc` (
  `ID_PRESCRIPTIONREGL` bigint(20) NOT NULL,
  `NUM_PRESCRIPTIONASSOC` int(11) NOT NULL,
  `ID_TEXTE` bigint(20) NOT NULL,
  `ID_ARTICLE` bigint(20) NOT NULL,
  INDEX `fk_prescriptionreglassoc_prescriptionregl1_idx` (`ID_PRESCRIPTIONREGL`),
  PRIMARY KEY (`ID_PRESCRIPTIONREGL`,`NUM_PRESCRIPTIONASSOC`),
  CONSTRAINT `fk_prescriptionreglassoc_prescriptionregl1` 
  	FOREIGN KEY (`ID_PRESCRIPTIONREGL`)
  	REFERENCES `prescriptionregl` (`ID_PRESCRIPTIONREGL`) 
  	ON DELETE CASCADE 
  	ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;