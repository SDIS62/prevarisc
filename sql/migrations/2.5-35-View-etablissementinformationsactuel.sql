SET NAMES 'utf8';

DROP VIEW IF EXISTS `etablissementinformationsactuel`;

CREATE VIEW `etablissementinformationsactuel` AS 
SELECT * FROM `etablissementinformations` ei WHERE ei.DATE_ETABLISSEMENTINFORMATIONS = 
( 
    SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations 
    WHERE etablissementinformations.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT 
);