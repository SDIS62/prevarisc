-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Lun 07 Décembre 2015 à 10:12
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
-- Structure de la table `groupe`
--

CREATE TABLE IF NOT EXISTS `groupe` (
  `ID_GROUPE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_GROUPE` varchar(255) NOT NULL,
  `DESC_GROUPE` text NOT NULL,
  PRIMARY KEY (`ID_GROUPE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Contenu de la table `groupe`
--

INSERT INTO `groupe` (`ID_GROUPE`, `LIBELLE_GROUPE`, `DESC_GROUPE`) VALUES
(1, 'Groupe par défaut', 'Ceci est le groupe qui contient les nouveaux utilisateurs et les utilisateurs dont les groupes ont été supprimés.'),
(2, 'Administration informatique', ''),
(3, 'Administration prévention', ''),
(12, 'Développeurs', ''),
(13, 'Prévisionnistes', ''),
(14, 'Secretariat prévention', 'CASA'),
(15, 'Anciens Préventionnistes', 'Groupe des préventionnistes désactivés, pour archivage.'),
(17, 'Chefs de service en GT', ''),
(18, 'Préventionnistes', ''),
(19, 'Sous-Préfecture', ''),
(20, 'Région', 'Accès uniquement aux lycées publics'),
(21, 'Président de commission', ''),
(23, 'Chefs de centre', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
