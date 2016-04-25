SET NAMES 'utf8';
LOCK TABLES dossiernatureliste WRITE;
LOCK TABLES dossiernature WRITE;
set foreign_key_checks=0;

# Nouvelles natures de courrier
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (52,"Lettre",5,1);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (55,"Mise en demeure",5,2);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (51,"Avis écrit motivé",5,3);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (53,"Consultation PLU",5,4);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (49,"Rapport d'organisme agréé",5,5);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (54,"Demande de renseignements",5,6);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (59,"Demande de visite périodique",5,7);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (57,"Demande de visite technique",5,8);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (58,"Demande de visite inopinée",5,9);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (50,"Demande de visite hors programme",5,10);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (60,"Demande de visite de réception",5,11);

# Migration des dossiers existants vers lettre simple
UPDATE dossiernature SET ID_NATURE = 52 WHERE ID_NATURE IN(34,35,36);

# Suppression des anciennes natures de courrier
DELETE FROM `dossiernatureliste` WHERE ID_DOSSIERNATURE IN(34,35,36);

set foreign_key_checks=1;
UNLOCK TABLES;
