SET NAMES 'utf8';

# Nouveaux genres
INSERT INTO `genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (7, 'Camping');
INSERT INTO `genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (8, 'Manifestation Temporaire');
INSERT INTO `genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (9, 'IOP');
INSERT INTO `genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (10, 'Zone');

# Nouvelles ressources
INSERT INTO `resources`(`id_resource`,`name`, `text`) VALUES
(52,'etablissement_camp_0_0','Camping (Ignorer les groupements - Ignorer la commune)'),
(53,'etablissement_temp_0_0','Manifestation temporaire (Ignorer les groupements - Ignorer la commune)'),
(54,'etablissement_iop_0_0','IOP (Ignorer les groupements - Ignorer la commune)'),
(55,'etablissement_zone_0_0_0','Zone (Toutes les classes - Ignorer les groupements - Ignorer la commune)');

# Nouveaux privileges
INSERT INTO `privileges` VALUES 
(52,'view_ets','Lecture',52),
(53,'edit_ets','Modifier',52),
(54,'view_ets','Lecture',53),
(55,'edit_ets','Modifier',53),
(56,'view_ets','Lecture',54),
(57,'edit_ets','Modifier',54),
(58,'view_ets','Lecture',55),
(59,'edit_ets','Modifier',55);