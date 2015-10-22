set names 'utf8';

INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (64,"Visite conseil",2,7);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (65,"Autorisation de travaux",5,12);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (66,"Déclassement / Reclassement",1,26);
UPDATE `typeactivite` SET `LIBELLE_ACTIVITE` = 'Salles polyvalentes à dominante sportive, dont l\'aire d\'activité est inférieure à 1200m² et la hauteur sous plafond supérieure ou égale à 6,50 mètres, etc' WHERE `ID_TYPEACTIVITE` = 96;