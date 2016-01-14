-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Lun 07 Décembre 2015 à 10:15
-- Version du serveur: 1.0.4
-- Version de PHP: 5.4.4-14+deb7u4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `prevarisc`
--

-- --------------------------------------------------------

--
-- Structure de la table `textesappl`
--

CREATE TABLE IF NOT EXISTS `textesappl` (
  `ID_TEXTESAPPL` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_TEXTESAPPL` text,
  `VISIBLE_TEXTESAPPL` tinyint(4) DEFAULT NULL,
  `ID_TYPETEXTEAPPL` bigint(20) NOT NULL,
  `NUM_TEXTESAPPL` int(11) NOT NULL DEFAULT '99999',
  PRIMARY KEY (`ID_TEXTESAPPL`),
  KEY `fk_textesappl_typetextesappl1_idx` (`ID_TYPETEXTEAPPL`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

--
-- Contenu de la table `textesappl`
--

INSERT INTO `textesappl` (`ID_TEXTESAPPL`, `LIBELLE_TEXTESAPPL`, `VISIBLE_TEXTESAPPL`, `ID_TYPETEXTEAPPL`, `NUM_TEXTESAPPL`) VALUES
(2, 'Décret n°95-260 du 8 mars 1995 modifié relatif à la Commission Consultative Départementale de Sécurité et d’Accessibilité', 1, 1, 0),
(3, 'Règlement de sécurité contre l’incendie du 23 mars 1965 modifié, relatif aux établissements recevant du public', 1, 1, 4),
(4, 'Arrêté du 25 juin 1980 modifié, relatif aux dispositions générales applicables aux établissements recevant du public et instructions techniques annexées', 1, 1, 1),
(5, 'PE - Arrêté du 22 Juin 1990 modifié (dispositions particulières applicables aux établissements de 5ème catégorie PE)', 1, 3, 0),
(6, 'PO - Arrêté du 26 octobre 2011 modifié (dispositions particulières applicables aux petits hôtels PO)', 1, 3, 2),
(7, 'PU - Arrêté du 10 décembre 2004 (dispositions particulières applicables aux petits établissements de soins PU)', 1, 3, 3),
(8, 'PX - Arrêté du 20 novembre 2000 (dispositions particulières applicables aux établissements sportifs PX)', 1, 3, 99999),
(9, 'J - Arrêté du 19 novembre 2001 modifié (dispositions particulières applicables aux établissements du type J)', 1, 2, 0),
(10, 'L - Arrêté du 5 février 2007 modifié (dispositions particulières applicables aux établissements du type L)', 1, 2, 2),
(11, 'M - Arrêté du 22 décembre 1981 modifié (dispositions particulières applicables aux établissements du type M)', 1, 2, 3),
(12, 'N - Arrêté du 21 juin 1982 modifié (dispositions particulières applicables aux établissements du type N)', 1, 2, 4),
(13, 'O - Arrêté du 25 octobre 2011 (dispositions particulières applicables aux établissements du type O)', 1, 2, 7),
(14, 'P - Arrêté du 7 juillet 1983 modifié (dispositions particulières applicables aux établissements du type P)', 1, 2, 8),
(15, 'R - Arrêté du 4 juin 1982 modifié (dispositions particulières applicables aux établissements du type R)', 1, 2, 9),
(16, 'S - Arrêté du 12 juin 1995 modifié (dispositions particulières applicables aux établissements du type S)', 1, 2, 10),
(17, 'T - Arrêté du 18 novembre 1987 modifié (dispositions particulières applicables aux établissements du type T)', 1, 2, 11),
(18, 'U - Arrêté du 10 décembre 2004 modifié (dispositions particulières applicables aux établissements du type U)', 1, 2, 13),
(19, 'V - Arrêté du 21 avril 1983 modifié (dispositions particulières applicables aux établissements du type V)', 1, 2, 14),
(20, 'W - Arrêté du 21 avril 1983 modifié (dispositions particulières applicables aux établissements du type W)', 1, 2, 15),
(21, 'X - Arrêté du 4 juin 1982 modifié (dispositions particulières applicables aux établissements du type X)', 1, 2, 16),
(22, 'Y - Arrêté du 12 juin 1995 modifié (dispositions particulières applicables aux établissements du type Y)', 1, 2, 99999),
(23, 'CTS - Arrêté du 23 janvier 1985 modifié (dispositions particulières applicables aux établissements du type CTS)', 1, 4, 99999),
(24, 'PA - Arrêté du 6 janvier 1983 modifié (dispositions particulières applicables aux établissements du type PA)', 1, 4, 99999),
(25, 'SG - Arrêté du 6 janvier 1983 modifié (dispositions particulières applicables aux établissements du type SG)', 1, 4, 99999),
(26, 'EF - Arrêté du 9 janvier 1990 modifié (dispositions particulières applicables aux établissements du type EF)', 1, 4, 99999),
(27, 'GA - Arrêté du 24 décembre 2007 (dispositions particulières applicables aux établissements du type GA)', 1, 4, 99999),
(28, 'PS - Arrêté du 9 mai 2006 (dispositions particulières applicables aux établissements du type PS)', 1, 4, 99999),
(29, 'OA - Arrêté du 23 octobre 1986 modifié (dispositions particulières applicables aux établissements du type OA)', 1, 4, 99999),
(30, 'REF - Arrêté du 10 novembre 1994 modifié (dispositions particulières applicables aux établissements du type REF)', 1, 4, 99999),
(31, 'EP - Arrêté du 18 juillet 2006 (règles de sécurité contre les risques d’incendie et de panique dans les établissements pénitentiaires)', 1, 4, 99999),
(32, 'Arrêté du 31 janvier 1986 modifié (dispositions applicables aux habitations)', 1, 5, 4),
(33, '(ERP) Code de la Construction et de l’Habitation - Articles R 123-1 à R 123-55 et L 111-8', 1, 1, 2),
(34, '(IGH) Code de la Construction et de l’Habitation - Articles R 122-1 à R 122-29 ', 1, 1, 3),
(35, 'Arrêté du 18 octobre 1977 modifié portant règlement de sécurité pour la construction des immeubles de grande hauteur et leur protection contre les risques d''incendie et de panique', 1, 6, 99999),
(36, 'Arrêté du 30 décembre 2011 portant règlement de sécurité pour la construction des immeubles de grande hauteur et leur protection contre les risques d''incendie et de panique', 1, 6, 99999),
(37, 'Code de l’Urbanisme, articles R 111-1 à R 111-4', 1, 5, 0),
(38, '­Code de la Construction et de l’Habitation (articles R 111-1 à R 111-17)(habitations)', 1, 5, 99999),
(39, '­Décret n° 69-596 du 14 Juin 1969, fixant les règles générales de construction des bâtiments d''habitation', 1, 5, 99999),
(40, 'Décret n° 2011-36 du 10 janvier 2011 relatif à l’installation de détecteurs de fumée dans tous les lieux d’habitation', 1, 5, 99999),
(41, 'O - Arrêté du 21 Juin 1982 modifié (dispositions particulières applicables aux établissements du type O, hôtels et pensions de famille). ', 1, 2, 6),
(42, 'Code du travail', 1, 1, 5),
(43, 'L - Arrêté du 12 décembre 1984 modifié (dispositions particulières applicables aux établissements du type L)', 1, 2, 1),
(44, 'U - Arrêté du 23 mai 1989 modifié (dispositions particulières applicables aux établissements du type U)', 1, 2, 12),
(45, 'Arrêté du 21 juin 1982 (dispositions particulières applicables aux établissements du type O)', 0, 2, 5),
(46, 'PO - Arrêté du 24 juillet 2006 modifié (dispositions particulières applicables aux petits hôtels PO)', 1, 3, 1),
(47, 'Instruction technique n°246 relative au désenfumage dans les ERP', 1, 1, 99999),
(48, 'Instruction technique n°249 relative aux façades', 1, 1, 7),
(49, 'Instruction technique n°263 relative à la construction et au désenfumage des volumes libres intérieurs dans les ERP', 1, 1, 8),
(50, 'Instruction technique n°248 relative aux systèmes d''alarme utilisés dans les ERP', 1, 1, 99999);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
