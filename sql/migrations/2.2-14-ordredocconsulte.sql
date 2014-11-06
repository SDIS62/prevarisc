SET NAMES 'utf8';

ALTER TABLE `listedocconsulte` ADD `ORDRE_DOC` int(11) NOT NULL DEFAULT "0";

INSERT INTO `listedocconsulte` VALUES (DEFAULT,'Registre de sécurité',1,0,0,0,1);
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Dossier technique amiante", ORDRE_DOC = 2 WHERE ID_DOC = 37;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Portes automatiques : Relevé de vérification des portes automatiques en façade (CO 48)", ORDRE_DOC = 3 WHERE ID_DOC = 33;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Portes automatiques : Contrat d’entretien des portes automatiques en façade (CO 48)", ORDRE_DOC = 4 WHERE ID_DOC = 34;
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"Désenfumage naturel : Tous les ans par un technicien compétent (DF 10)",1,0,0,0,5);
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Désenfumage mécanique : Relevé de vérification du désenfumage (DF 10)", ORDRE_DOC = 6 WHERE ID_DOC = 8;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Désenfumage mécanique : RVRE triennal du désenfumage mécanique associé à un SSI A ou B", ORDRE_DOC = 7 WHERE ID_DOC = 9;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Chauffage : Relevé de vérification du chauffage-ventilation (CH 58)", ORDRE_DOC = 8 WHERE ID_DOC = 10;
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"Etanchéité (gaz liquide frigorigène) : Tous les ans par un technicien compétent (CH 58) (CH 57)",1,0,0,0,9);
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Ramonage : Attestation de ramonage ou visite des conduits (CH 57)", ORDRE_DOC = 10 WHERE ID_DOC = 11;
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"Traitement air VMC : Tous les ans par un technicien compétent (CH 58) (CH 57)",1,0,0,0,11);
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Gaz : Relevé de vérification du gaz (GZ 30) (GZ 29)", ORDRE_DOC = 12 WHERE ID_DOC = 12;
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"Groupe électrogène : TC/15j / 1/mois (EL18)",1,0,0,0,13);
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Électricité : Relevé ou RVRE des installations électriques et/ou des paratonnerres ou protection contre la foudre (EL 19)", ORDRE_DOC = 14 WHERE ID_DOC = 14;
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"Installations extérieures de protection contre la foudre : Tous les ans par un technicien compétent (EL 19)",1,0,0,0,15);
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"Éclairage : Tous les ans par un technicien compétent (EC 15)",1,0,0,0,16);
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Ascenseurs Monte-charges : Tous les ans (AS 11)", ORDRE_DOC = 17 WHERE ID_DOC = 16;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Ascenseurs Monte-charges : RVRE quinquennal des ascenseurs (AS 9)", ORDRE_DOC = 18 WHERE ID_DOC = 15;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Ascenseurs Monte-charges : Contrat d’entretien des ascenseurs, escaliers mécaniques et trottoirs roulants (AS 8)", ORDRE_DOC = 19 WHERE ID_DOC = 17;
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"Escaliers mécaniques : Annuel OA (AS 10)",1,0,0,0,20);
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Appareils de cuisson : Relevé de vérification des appareils de cuisson (GC 22)", ORDRE_DOC = 21 WHERE ID_DOC = 19;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Hotte : Attestation de nettoyage du circuit d’extraction (GC 21)", ORDRE_DOC = 22 WHERE ID_DOC = 18;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Système de sécurité Incendie Alarme Détection : Relevé de vérification du SSI ou d’équipement d’alarme, détection, portes, clapets coupe-feu (MS 73)", ORDRE_DOC = 23 WHERE ID_DOC = 27;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Système de sécurité Incendie Alarme Détection : RVRE triennal du SSI A ou B (MS 73)", ORDRE_DOC = 24 WHERE ID_DOC = 26;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Système de sécurité Incendie Alarme Détection :  Contrat d’entretien du Système de Sécurité Incendie (MS 58)", ORDRE_DOC = 25 WHERE ID_DOC = 28;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Système de sécurité Incendie Alarme Détection : Dossier d’identité du Système de Sécurité Incendie (MS 73)", ORDRE_DOC = 26 WHERE ID_DOC = 29;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Installation fixe d'extinction automatique à eau : Relevé de vérification du système d’extinction automatique du type sprinkleur (MS 29/73) ou déversoirs et rideaux d’eau (L 57)", ORDRE_DOC = 27 WHERE ID_DOC = 24;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Installation fixe d'extinction automatique à eau : RVRE triennal du sprinkleur (MS 73)", ORDRE_DOC = 28 WHERE ID_DOC = 25;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "RIA : Relevé de vérification des R.I.A. (MS 73)", ORDRE_DOC = 29 WHERE ID_DOC = 23;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Extincteurs : Relevé de vérification des extincteurs (MS 38)", ORDRE_DOC = 30 WHERE ID_DOC = 20;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Extincteurs : Révision décennale des extincteurs (MS 38)", ORDRE_DOC = 31 WHERE ID_DOC = 21;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Formation Exercice : Exercice d’évacuation réalisé", ORDRE_DOC = 32 WHERE ID_DOC = 38;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Formation Exercice :  Formation des personnels à l’utilisation des moyens de secours", ORDRE_DOC = 33 WHERE ID_DOC = 39;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Relevé de vérification des PI/BI (MS 73)", ORDRE_DOC = 34 WHERE ID_DOC = 32;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Communication radioélectrique : Attestation de vérification des communications radioélectriques (MS 71)", ORDRE_DOC = 35 WHERE ID_DOC = 30;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Communication radioélectrique : RVRE triennal des moyens de communications radioélectriques mode relayés (MS 71)", ORDRE_DOC = 36 WHERE ID_DOC = 31;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Gaz médicaux : Relevé de vérification des fluides médicaux (U 64 / J 33)", ORDRE_DOC = 37 WHERE ID_DOC = 13;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Espaces scéniques : RVRE des équipements de levage des salles avec espace scénique (L 57)", ORDRE_DOC = 38 WHERE ID_DOC = 35;
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Espaces scéniques : RVRE triennal des salles de spectacles avec espace scénique (L 57)", ORDRE_DOC = 39 WHERE ID_DOC = 36;
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"Réglage luminosité et son : L 13 OA 1 an",1,0,0,0,40);
INSERT INTO `listedocconsulte` VALUES (DEFAULT,"5ème avec hébergement : PE4 PO1 - 2 ans TC : SSI DF manuel CH GC GZ - 1 an TC : EL/détection + contrat",1,0,0,0,41);
UPDATE `listedocconsulte` SET LIBELLE_DOC = "Autres installations : Relevé de vérification des colonnes sèches (MS 73)", ORDRE_DOC = 42 WHERE ID_DOC = 22;








