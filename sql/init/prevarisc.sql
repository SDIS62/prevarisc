CREATE DATABASE  IF NOT EXISTS `prevarisc` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `prevarisc`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: prevarisc
-- ------------------------------------------------------
-- Server version	5.6.15-log
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
--
-- Table structure for table `adressecommune`
--

DROP TABLE IF EXISTS `adressecommune`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adressecommune` (
  `NUMINSEE_COMMUNE` char(5) NOT NULL,
  `LIBELLE_COMMUNE` varchar(60) NOT NULL,
  `CODEPOSTAL_COMMUNE` varchar(5) NOT NULL,
  `ID_UTILISATEURINFORMATIONS` bigint(20) NOT NULL,
  PRIMARY KEY (`NUMINSEE_COMMUNE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adressecommune`
--

LOCK TABLES `adressecommune` WRITE;
/*!40000 ALTER TABLE `adressecommune` DISABLE KEYS */;
/*!40000 ALTER TABLE `adressecommune` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adresserue`
--

DROP TABLE IF EXISTS `adresserue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresserue` (
  `ID_RUE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_RUE` varchar(255) NOT NULL COMMENT 'Pour le moment, le nom du type de la rue doit être cité dans le nom de la rue. (RUE DES ACACIAS)',
  `ID_RUETYPE` bigint(20) unsigned NOT NULL,
  `NUMINSEE_COMMUNE` char(5) NOT NULL,
  PRIMARY KEY (`ID_RUE`),
  KEY `fk_adresserue_adresseruetype_idx` (`ID_RUETYPE`),
  KEY `fk_adresserue_adressecommune1_idx` (`NUMINSEE_COMMUNE`),
  CONSTRAINT `fk_adresserue_adressecommune1` FOREIGN KEY (`NUMINSEE_COMMUNE`) REFERENCES `adressecommune` (`NUMINSEE_COMMUNE`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_adresserue_adresseruetype` FOREIGN KEY (`ID_RUETYPE`) REFERENCES `adresseruetype` (`ID_RUETYPE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adresserue`
--

LOCK TABLES `adresserue` WRITE;
/*!40000 ALTER TABLE `adresserue` DISABLE KEYS */;
/*!40000 ALTER TABLE `adresserue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adresseruetype`
--

DROP TABLE IF EXISTS `adresseruetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresseruetype` (
  `ID_RUETYPE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_RUETYPE` varchar(32) NOT NULL COMMENT 'Exemple : BOULEVARD',
  `ABREVIATION_RUETYPE` varchar(16) NOT NULL COMMENT 'Exemple : BLVD',
  PRIMARY KEY (`ID_RUETYPE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adresseruetype`
--

LOCK TABLES `adresseruetype` WRITE;
/*!40000 ALTER TABLE `adresseruetype` DISABLE KEYS */;
/*!40000 ALTER TABLE `adresseruetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avis`
--

DROP TABLE IF EXISTS `avis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `avis` (
  `ID_AVIS` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_AVIS` varchar(30) NOT NULL,
  PRIMARY KEY (`ID_AVIS`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avis`
--

LOCK TABLES `avis` WRITE;
/*!40000 ALTER TABLE `avis` DISABLE KEYS */;
INSERT INTO `avis` VALUES (1,'Favorable'),(2,'Défavorable');
/*!40000 ALTER TABLE `avis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorie` (
  `ID_CATEGORIE` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_CATEGORIE` varchar(15) DEFAULT NULL,
  `COMMENTAIRE_CATEGORIE` varchar(35) DEFAULT NULL,
  PRIMARY KEY (`ID_CATEGORIE`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorie`
--

LOCK TABLES `categorie` WRITE;
/*!40000 ALTER TABLE `categorie` DISABLE KEYS */;
INSERT INTO `categorie` VALUES (1,'1ère catégorie','Plus de 1500 personnes'),(2,'2ème catégorie','De 701 à 1500 personnes'),(3,'3ème catégorie','De 301 à 700 personnes'),(4,'4ème catégorie','300 personnes et moins'),(5,'5ème catégorie','Nombre très réduit de personnes');
/*!40000 ALTER TABLE `categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classe`
--

DROP TABLE IF EXISTS `classe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classe` (
  `ID_CLASSE` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_CLASSE` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_CLASSE`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classe`
--

LOCK TABLES `classe` WRITE;
/*!40000 ALTER TABLE `classe` DISABLE KEYS */;
INSERT INTO `classe` VALUES (1,'GHA'),(2,'GHO'),(3,'GHR'),(4,'GHS'),(5,'GHTC'),(6,'GHU'),(7,'GHW1'),(8,'GHW2'),(9,'GHZ'),(10,'ITGH');
/*!40000 ALTER TABLE `classe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission`
--

DROP TABLE IF EXISTS `commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commission` (
  `ID_COMMISSION` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_COMMISSION` varchar(50) NOT NULL DEFAULT 'Nom de la commission',
  `DOCUMENT_CR` varchar(255) DEFAULT NULL,
  `ID_COMMISSIONTYPE` int(2) unsigned NOT NULL,
  PRIMARY KEY (`ID_COMMISSION`),
  KEY `fk_commission_commissiontype1_idx` (`ID_COMMISSIONTYPE`),
  CONSTRAINT `fk_commission_commissiontype1` FOREIGN KEY (`ID_COMMISSIONTYPE`) REFERENCES `commissiontype` (`ID_COMMISSIONTYPE`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission`
--

LOCK TABLES `commission` WRITE;
/*!40000 ALTER TABLE `commission` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissioncontact`
--

DROP TABLE IF EXISTS `commissioncontact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissioncontact` (
  `ID_COMMISSION` bigint(20) unsigned NOT NULL,
  `ID_UTILISATEURINFORMATIONS` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_COMMISSION`,`ID_UTILISATEURINFORMATIONS`),
  KEY `fk_commissioncontact_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS`),
  CONSTRAINT `fk_commissioncontact_commission1` FOREIGN KEY (`ID_COMMISSION`) REFERENCES `commission` (`ID_COMMISSION`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_commissioncontact_utilisateurinformations1` FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`) REFERENCES `utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissioncontact`
--

LOCK TABLES `commissioncontact` WRITE;
/*!40000 ALTER TABLE `commissioncontact` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissioncontact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionmembre`
--

DROP TABLE IF EXISTS `commissionmembre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionmembre` (
  `ID_COMMISSIONMEMBRE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_COMMISSIONMEMBRE` varchar(255) NOT NULL,
  `ID_GROUPEMENT` int(11) DEFAULT NULL,
  `ID_UTILISATEURINFORMATIONS` int(11) DEFAULT NULL,
  `PRESENCE_COMMISSIONMEMBRE` int(11) NOT NULL,
  `COURRIER_CONVOCATIONVISITE` varchar(255) DEFAULT NULL,
  `COURRIER_CONVOCATIONSALLE` varchar(255) DEFAULT NULL,
  `COURRIER_ODJ` varchar(255) DEFAULT NULL,
  `COURRIER_PV` varchar(255) DEFAULT NULL,
  `ID_COMMISSION` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`),
  KEY `ID_COMMISSION` (`ID_GROUPEMENT`,`ID_UTILISATEURINFORMATIONS`),
  KEY `ID_COURRIER_BE` (`COURRIER_CONVOCATIONVISITE`,`COURRIER_CONVOCATIONSALLE`),
  KEY `fk_commissionmembre_commission1_idx` (`ID_COMMISSION`),
  CONSTRAINT `fk_commissionmembre_commission1` FOREIGN KEY (`ID_COMMISSION`) REFERENCES `commission` (`ID_COMMISSION`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionmembre`
--

LOCK TABLES `commissionmembre` WRITE;
/*!40000 ALTER TABLE `commissionmembre` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionmembre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionmembrecategorie`
--

DROP TABLE IF EXISTS `commissionmembrecategorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionmembrecategorie` (
  `ID_COMMISSIONMEMBRE` bigint(20) unsigned NOT NULL,
  `ID_CATEGORIE` int(1) unsigned NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`,`ID_CATEGORIE`),
  KEY `fk_commissionmembrecategorie_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE`),
  KEY `fk_commissionmembrecategorie_categorie1_idx` (`ID_CATEGORIE`),
  CONSTRAINT `fk_commissionmembrecategorie_categorie1` FOREIGN KEY (`ID_CATEGORIE`) REFERENCES `categorie` (`ID_CATEGORIE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionmembrecategorie_commissionmembre1` FOREIGN KEY (`ID_COMMISSIONMEMBRE`) REFERENCES `commissionmembre` (`ID_COMMISSIONMEMBRE`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionmembrecategorie`
--

LOCK TABLES `commissionmembrecategorie` WRITE;
/*!40000 ALTER TABLE `commissionmembrecategorie` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionmembrecategorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionmembreclasse`
--

DROP TABLE IF EXISTS `commissionmembreclasse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionmembreclasse` (
  `ID_COMMISSIONMEMBRE` bigint(20) unsigned NOT NULL,
  `ID_CLASSE` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`,`ID_CLASSE`),
  KEY `fk_commissionmembreclasse_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE`),
  KEY `fk_commissionmembreclasse_classe1_idx` (`ID_CLASSE`),
  CONSTRAINT `fk_commissionmembreclasse_classe1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `classe` (`ID_CLASSE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionmembreclasse_commissionmembre1` FOREIGN KEY (`ID_COMMISSIONMEMBRE`) REFERENCES `commissionmembre` (`ID_COMMISSIONMEMBRE`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionmembreclasse`
--

LOCK TABLES `commissionmembreclasse` WRITE;
/*!40000 ALTER TABLE `commissionmembreclasse` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionmembreclasse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionmembredossiernature`
--

DROP TABLE IF EXISTS `commissionmembredossiernature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionmembredossiernature` (
  `ID_COMMISSIONMEMBRE` bigint(20) unsigned NOT NULL,
  `ID_DOSSIERNATURE` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`,`ID_DOSSIERNATURE`),
  KEY `fk_commissionmembredossiernature_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE`),
  KEY `fk_commissionmembredossiernature_dossiernatureliste1_idx` (`ID_DOSSIERNATURE`),
  CONSTRAINT `fk_commissionmembredossiernature_commissionmembre1` FOREIGN KEY (`ID_COMMISSIONMEMBRE`) REFERENCES `commissionmembre` (`ID_COMMISSIONMEMBRE`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionmembredossiernature_dossiernatureliste1` FOREIGN KEY (`ID_DOSSIERNATURE`) REFERENCES `dossiernatureliste` (`ID_DOSSIERNATURE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionmembredossiernature`
--

LOCK TABLES `commissionmembredossiernature` WRITE;
/*!40000 ALTER TABLE `commissionmembredossiernature` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionmembredossiernature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionmembredossiertype`
--

DROP TABLE IF EXISTS `commissionmembredossiertype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionmembredossiertype` (
  `ID_COMMISSIONMEMBRE` bigint(20) unsigned NOT NULL,
  `ID_DOSSIERTYPE` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_DOSSIERTYPE`,`ID_COMMISSIONMEMBRE`),
  KEY `fk_commissionmembredossiertype_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE`),
  KEY `fk_commissionmembredossiertype_dossiertype1_idx` (`ID_DOSSIERTYPE`),
  CONSTRAINT `fk_commissionmembredossiertype_commissionmembre1` FOREIGN KEY (`ID_COMMISSIONMEMBRE`) REFERENCES `commissionmembre` (`ID_COMMISSIONMEMBRE`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionmembredossiertype_dossiertype1` FOREIGN KEY (`ID_DOSSIERTYPE`) REFERENCES `dossiertype` (`ID_DOSSIERTYPE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionmembredossiertype`
--

LOCK TABLES `commissionmembredossiertype` WRITE;
/*!40000 ALTER TABLE `commissionmembredossiertype` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionmembredossiertype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionmembretypeactivite`
--

DROP TABLE IF EXISTS `commissionmembretypeactivite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionmembretypeactivite` (
  `ID_COMMISSIONMEMBRE` bigint(20) unsigned NOT NULL,
  `ID_TYPEACTIVITE` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONMEMBRE`,`ID_TYPEACTIVITE`),
  KEY `fk_commissionmembre-typeactivite_typeactivite1_idx` (`ID_TYPEACTIVITE`),
  KEY `fk_commissionmembre-typeactivite_commissionmembre1_idx` (`ID_COMMISSIONMEMBRE`),
  CONSTRAINT `fk_commissionmembre-typeactivite_commissionmembre1` FOREIGN KEY (`ID_COMMISSIONMEMBRE`) REFERENCES `commissionmembre` (`ID_COMMISSIONMEMBRE`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_commissionmembre-typeactivite_typeactivite1` FOREIGN KEY (`ID_TYPEACTIVITE`) REFERENCES `typeactivite` (`ID_TYPEACTIVITE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionmembretypeactivite`
--

LOCK TABLES `commissionmembretypeactivite` WRITE;
/*!40000 ALTER TABLE `commissionmembretypeactivite` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionmembretypeactivite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionregle`
--

DROP TABLE IF EXISTS `commissionregle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionregle` (
  `ID_REGLE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ID_GROUPEMENT` bigint(20) DEFAULT NULL,
  `ID_COMMISSION` bigint(20) unsigned NOT NULL,
  `NUMINSEE_COMMUNE` char(5) DEFAULT NULL,
  PRIMARY KEY (`ID_REGLE`),
  KEY `ID_GROUPEMENT` (`ID_GROUPEMENT`),
  KEY `fk_commissionregle_commission1_idx` (`ID_COMMISSION`),
  KEY `fk_commissionregle_adressecommune1_idx` (`NUMINSEE_COMMUNE`),
  CONSTRAINT `fk_commissionregle_adressecommune1` FOREIGN KEY (`NUMINSEE_COMMUNE`) REFERENCES `adressecommune` (`NUMINSEE_COMMUNE`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionregle_commission1` FOREIGN KEY (`ID_COMMISSION`) REFERENCES `commission` (`ID_COMMISSION`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionregle`
--

LOCK TABLES `commissionregle` WRITE;
/*!40000 ALTER TABLE `commissionregle` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionregle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionreglecategorie`
--

DROP TABLE IF EXISTS `commissionreglecategorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionreglecategorie` (
  `ID_REGLE` bigint(20) unsigned NOT NULL,
  `ID_CATEGORIE` int(1) unsigned NOT NULL,
  KEY `fk_commissionreglecategorie_commissionregle1_idx` (`ID_REGLE`),
  KEY `fk_commissionreglecategorie_categorie1_idx` (`ID_CATEGORIE`),
  CONSTRAINT `fk_commissionreglecategorie_categorie1` FOREIGN KEY (`ID_CATEGORIE`) REFERENCES `categorie` (`ID_CATEGORIE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionreglecategorie_commissionregle1` FOREIGN KEY (`ID_REGLE`) REFERENCES `commissionregle` (`ID_REGLE`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionreglecategorie`
--

LOCK TABLES `commissionreglecategorie` WRITE;
/*!40000 ALTER TABLE `commissionreglecategorie` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionreglecategorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionregleclasse`
--

DROP TABLE IF EXISTS `commissionregleclasse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionregleclasse` (
  `ID_REGLE` bigint(20) unsigned NOT NULL,
  `ID_CLASSE` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID_REGLE`,`ID_CLASSE`),
  KEY `fk_commissionregleclasse_commissionregle1_idx` (`ID_REGLE`),
  KEY `fk_commissionregleclasse_classe1_idx` (`ID_CLASSE`),
  CONSTRAINT `fk_commissionregleclasse_classe1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `classe` (`ID_CLASSE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionregleclasse_commissionregle1` FOREIGN KEY (`ID_REGLE`) REFERENCES `commissionregle` (`ID_REGLE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionregleclasse`
--

LOCK TABLES `commissionregleclasse` WRITE;
/*!40000 ALTER TABLE `commissionregleclasse` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionregleclasse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionregleetudevisite`
--

DROP TABLE IF EXISTS `commissionregleetudevisite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionregleetudevisite` (
  `ID_REGLE` bigint(20) unsigned NOT NULL,
  `ETUDEVISITE` tinyint(1) NOT NULL,
  KEY `ETUDEVISITE` (`ETUDEVISITE`),
  KEY `fk_commissionregleetudevisite_commissionregle1_idx` (`ID_REGLE`),
  CONSTRAINT `fk_commissionregleetudevisite_commissionregle1` FOREIGN KEY (`ID_REGLE`) REFERENCES `commissionregle` (`ID_REGLE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionregleetudevisite`
--

LOCK TABLES `commissionregleetudevisite` WRITE;
/*!40000 ALTER TABLE `commissionregleetudevisite` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionregleetudevisite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionreglelocalsommeil`
--

DROP TABLE IF EXISTS `commissionreglelocalsommeil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionreglelocalsommeil` (
  `ID_REGLE` bigint(20) unsigned NOT NULL,
  `LOCALSOMMEIL` tinyint(1) NOT NULL,
  KEY `TYPE_ACTIVITE` (`LOCALSOMMEIL`),
  KEY `fk_commissionreglelocalsommeil_commissionregle1_idx` (`ID_REGLE`),
  CONSTRAINT `fk_commissionreglelocalsommeil_commissionregle1` FOREIGN KEY (`ID_REGLE`) REFERENCES `commissionregle` (`ID_REGLE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionreglelocalsommeil`
--

LOCK TABLES `commissionreglelocalsommeil` WRITE;
/*!40000 ALTER TABLE `commissionreglelocalsommeil` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionreglelocalsommeil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissionregletype`
--

DROP TABLE IF EXISTS `commissionregletype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissionregletype` (
  `ID_REGLE` bigint(20) unsigned NOT NULL,
  `ID_TYPE` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID_REGLE`,`ID_TYPE`),
  KEY `fk_commissionregletype_commissionregle1_idx` (`ID_REGLE`),
  KEY `fk_commissionregletype_type1_idx` (`ID_TYPE`),
  CONSTRAINT `fk_commissionregletype_commissionregle1` FOREIGN KEY (`ID_REGLE`) REFERENCES `commissionregle` (`ID_REGLE`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_commissionregletype_type1` FOREIGN KEY (`ID_TYPE`) REFERENCES `type` (`ID_TYPE`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissionregletype`
--

LOCK TABLES `commissionregletype` WRITE;
/*!40000 ALTER TABLE `commissionregletype` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissionregletype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissiontype`
--

DROP TABLE IF EXISTS `commissiontype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissiontype` (
  `ID_COMMISSIONTYPE` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_COMMISSIONTYPE` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_COMMISSIONTYPE`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissiontype`
--

LOCK TABLES `commissiontype` WRITE;
/*!40000 ALTER TABLE `commissiontype` DISABLE KEYS */;
INSERT INTO `commissiontype` VALUES (1,'Sous-commission départementale'),(2,'Commission communale'),(3,'Commission intercommunale'),(4,'Commission d\'arrondissement'),(5,'Divers');
/*!40000 ALTER TABLE `commissiontype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissiontypeevenement`
--

DROP TABLE IF EXISTS `commissiontypeevenement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commissiontypeevenement` (
  `ID_COMMISSIONTYPEEVENEMENT` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_COMMISSIONTYPEEVENEMENT` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_COMMISSIONTYPEEVENEMENT`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissiontypeevenement`
--

LOCK TABLES `commissiontypeevenement` WRITE;
/*!40000 ALTER TABLE `commissiontypeevenement` DISABLE KEYS */;
INSERT INTO `commissiontypeevenement` VALUES (1,'Salle'),(2,'Visite de sécurité'),(3,'Groupe de visite');
/*!40000 ALTER TABLE `commissiontypeevenement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `couchecarto`
--

DROP TABLE IF EXISTS `couchecarto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `couchecarto` (
  `ID_COUCHECARTO` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `NOM_COUCHECARTO` varchar(255) DEFAULT NULL,
  `URL_COUCHECARTO` varchar(255) DEFAULT NULL,
  `ISBASELAYER_COUCHECARTO` tinyint(1) DEFAULT NULL,
  `LAYERS_COUCHECARTO` varchar(255) DEFAULT NULL,
  `FORMAT_COUCHECARTO` varchar(255) DEFAULT NULL,
  `TRANSPARENT_COUCHECARTO` tinyint(1) DEFAULT NULL,
  `TYPE_COUCHECARTO` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_COUCHECARTO`),
  UNIQUE KEY `ID_COUCHECARTO` (`ID_COUCHECARTO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `couchecarto`
--

LOCK TABLES `couchecarto` WRITE;
/*!40000 ALTER TABLE `couchecarto` DISABLE KEYS */;
/*!40000 ALTER TABLE `couchecarto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datecommission`
--

DROP TABLE IF EXISTS `datecommission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datecommission` (
  `ID_DATECOMMISSION` bigint(20) NOT NULL AUTO_INCREMENT,
  `DATE_COMMISSION` date NOT NULL,
  `HEUREDEB_COMMISSION` time NOT NULL DEFAULT '09:00:00',
  `HEUREFIN_COMMISSION` time NOT NULL DEFAULT '18:00:00',
  `LIBELLE_DATECOMMISSION` varchar(255) DEFAULT NULL,
  `GESTION_HEURES` tinyint(1) NOT NULL DEFAULT '1',
  `DATECOMMISSION_LIEES` bigint(20) DEFAULT NULL,
  `ID_COMMISSIONTYPEEVENEMENT` int(11) unsigned NOT NULL,
  `COMMISSION_CONCERNE` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_DATECOMMISSION`),
  KEY `fk_datecommission_commissiontypeevenement1_idx` (`ID_COMMISSIONTYPEEVENEMENT`),
  KEY `fk_datecommission_commission1_idx` (`COMMISSION_CONCERNE`),
  CONSTRAINT `fk_datecommission_commission1` FOREIGN KEY (`COMMISSION_CONCERNE`) REFERENCES `commission` (`ID_COMMISSION`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_datecommission_commissiontypeevenement1` FOREIGN KEY (`ID_COMMISSIONTYPEEVENEMENT`) REFERENCES `commissiontypeevenement` (`ID_COMMISSIONTYPEEVENEMENT`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datecommission`
--

LOCK TABLES `datecommission` WRITE;
/*!40000 ALTER TABLE `datecommission` DISABLE KEYS */;
/*!40000 ALTER TABLE `datecommission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datecommissionpj`
--

DROP TABLE IF EXISTS `datecommissionpj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datecommissionpj` (
  `ID_DATECOMMISSION` bigint(20) NOT NULL,
  `ID_PIECEJOINTE` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_DATECOMMISSION`,`ID_PIECEJOINTE`),
  KEY `fk_datecommissionpj_datecommission1_idx` (`ID_DATECOMMISSION`),
  KEY `fk_datecommissionpj_piecejointe1_idx` (`ID_PIECEJOINTE`),
  CONSTRAINT `fk_datecommissionpj_datecommission1` FOREIGN KEY (`ID_DATECOMMISSION`) REFERENCES `datecommission` (`ID_DATECOMMISSION`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_datecommissionpj_piecejointe1` FOREIGN KEY (`ID_PIECEJOINTE`) REFERENCES `piecejointe` (`ID_PIECEJOINTE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datecommissionpj`
--

LOCK TABLES `datecommissionpj` WRITE;
/*!40000 ALTER TABLE `datecommissionpj` DISABLE KEYS */;
/*!40000 ALTER TABLE `datecommissionpj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docmanquant`
--

DROP TABLE IF EXISTS `docmanquant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docmanquant` (
  `ID_DOCMANQUANT` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOCMANQUANT` text NOT NULL,
  PRIMARY KEY (`ID_DOCMANQUANT`),
  UNIQUE KEY `ID_DOCMANQUANT_UNIQUE` (`ID_DOCMANQUANT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docmanquant`
--

LOCK TABLES `docmanquant` WRITE;
/*!40000 ALTER TABLE `docmanquant` DISABLE KEYS */;
/*!40000 ALTER TABLE `docmanquant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documenttype`
--

DROP TABLE IF EXISTS `documenttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documenttype` (
  `ID_DOCUMENTTYPE` bigint(20) unsigned NOT NULL,
  `PATH_DOCUMENTTYPE` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_DOCUMENTTYPE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documenttype`
--

LOCK TABLES `documenttype` WRITE;
/*!40000 ALTER TABLE `documenttype` DISABLE KEYS */;
/*!40000 ALTER TABLE `documenttype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documenttypedossiernatures`
--

DROP TABLE IF EXISTS `documenttypedossiernatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documenttypedossiernatures` (
  `ID_DOCUMENTTYPE` bigint(20) unsigned NOT NULL,
  `ID_DOSSIERNATURE` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_DOCUMENTTYPE`,`ID_DOSSIERNATURE`),
  KEY `fk_documenttypedossiernatures_documenttype1_idx` (`ID_DOCUMENTTYPE`),
  KEY `fk_documenttypedossiernatures_dossiernatureliste1_idx` (`ID_DOSSIERNATURE`),
  CONSTRAINT `fk_documenttypedossiernatures_documenttype1` FOREIGN KEY (`ID_DOCUMENTTYPE`) REFERENCES `documenttype` (`ID_DOCUMENTTYPE`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_documenttypedossiernatures_dossiernatureliste1` FOREIGN KEY (`ID_DOSSIERNATURE`) REFERENCES `dossiernatureliste` (`ID_DOSSIERNATURE`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documenttypedossiernatures`
--

LOCK TABLES `documenttypedossiernatures` WRITE;
/*!40000 ALTER TABLE `documenttypedossiernatures` DISABLE KEYS */;
/*!40000 ALTER TABLE `documenttypedossiernatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossier`
--

DROP TABLE IF EXISTS `dossier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossier` (
  `ID_DOSSIER` bigint(20) NOT NULL AUTO_INCREMENT,
  `OBJET_DOSSIER` text,
  `COMMUNE_DOSSIER` text,
  `DATEMAIRIE_DOSSIER` datetime DEFAULT NULL,
  `DATESECRETARIAT_DOSSIER` datetime DEFAULT NULL,
  `TYPESERVINSTRUC_DOSSIER` varchar(15) DEFAULT NULL,
  `SERVICEINSTRUC_DOSSIER` varchar(255) DEFAULT NULL,
  `COMMISSION_DOSSIER` bigint(20) DEFAULT NULL,
  `DESCGEN_DOSSIER` text,
  `ANOMALIE_DOSSIER` text,
  `DESCANAL_DOSSIER` text,
  `JUSTIFDEROG_DOSSIER` text,
  `MESURESCOMPENS_DOSSIER` text,
  `MESURESCOMPLE_DOSSIER` text,
  `DESCEFF_DOSSIER` text,
  `DATEVISITE_DOSSIER` date DEFAULT NULL,
  `DATECOMM_DOSSIER` text,
  `AVIS_DOSSIER` int(1) unsigned DEFAULT NULL,
  `AVIS_DOSSIER_COMMISSION` int(1) unsigned DEFAULT NULL,
  `COORDSSI_DOSSIER` text,
  `DATESDIS_DOSSIER` datetime DEFAULT NULL,
  `DATEPREF_DOSSIER` datetime DEFAULT NULL,
  `DATEREP_DOSSIER` datetime DEFAULT NULL,
  `DATEREUN_DOSSIER` datetime DEFAULT NULL,
  `OPERSDIS_DOSSIER` tinyint(4) DEFAULT NULL,
  `RCCI_DOSSIER` tinyint(4) DEFAULT NULL,
  `REX_DOSSIER` text,
  `CHARGESEC_DOSSIER` text,
  `DUREEDEPL_DOSSIER` int(11) DEFAULT NULL,
  `GRAVPRESC_DOSSIER` text,
  `NUMINTERV_DOSSIER` int(11) DEFAULT NULL,
  `DATEINTERV_DOSSIER` datetime DEFAULT NULL,
  `DUREEINTERV_DOSSIER` time DEFAULT NULL,
  `DATESIGN_DOSSIER` date DEFAULT NULL,
  `DATEINSERT_DOSSIER` datetime NOT NULL,
  `TYPE_DOSSIER` bigint(20) unsigned NOT NULL,
  `DESCRIPTIF_DOSSIER` text CHARACTER SET utf8 COLLATE utf8_bin,
  `DEMANDEUR_DOSSIER` text,
  `DATEENVTRANSIT_DOSSIER` date DEFAULT NULL,
  `REGLEDEROG_DOSSIER` text,
  `INCOMPLET_DOSSIER` tinyint(1) DEFAULT '0',
  `DATEINCOMPLET_DOSSIER` date DEFAULT NULL,
  `CREATEUR_DOSSIER` bigint(20) DEFAULT NULL,
  `HORSDELAI_DOSSIER` tinyint(1) DEFAULT '0',
  `DIFFEREAVIS_DOSSIER` tinyint(4) DEFAULT NULL,
  `NPSP_DOSSIER` tinyint(4) DEFAULT NULL,
  `CNE_DOSSIER` tinyint(4) DEFAULT NULL,
  `FACTDANGE_DOSSIER` tinyint(4) DEFAULT NULL,
  `LIEUREUNION_DOSSIER` text,
  `ABSQUORUM_DOSSIER` tinyint(4) DEFAULT NULL,
  `ECHEANCIERTRAV_DOSSIER` date DEFAULT NULL,
  `VERROU_DOSSIER` tinyint(1) DEFAULT '0',
  `VERROU_USER_DOSSIER` bigint(20) DEFAULT NULL,
  `DATETRANSFERTCOMM_DOSSIER` datetime DEFAULT NULL,
  `DATERECEPTIONCOMM_DOSSIER` datetime DEFAULT NULL,
  `OBSERVATION_DOSSIER` text,
  `DATERVRAT_DOSSIER` date DEFAULT NULL,
  `DELAIPRESC_DOSSIER` date DEFAULT NULL,
  PRIMARY KEY (`ID_DOSSIER`),
  KEY `fk_dossier_dossiertype1_idx` (`TYPE_DOSSIER`),
  KEY `fk_dossier_avis1_idx` (`AVIS_DOSSIER`),
  CONSTRAINT `fk_dossier_avis1` FOREIGN KEY (`AVIS_DOSSIER`) REFERENCES `avis` (`ID_AVIS`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_dossier_dossiertype1` FOREIGN KEY (`TYPE_DOSSIER`) REFERENCES `dossiertype` (`ID_DOSSIERTYPE`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossier`
--

LOCK TABLES `dossier` WRITE;
/*!40000 ALTER TABLE `dossier` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossieraffectation`
--

DROP TABLE IF EXISTS `dossieraffectation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossieraffectation` (
  `HEURE_DEB_AFFECT` time DEFAULT NULL,
  `HEURE_FIN_AFFECT` time DEFAULT NULL,
  `NUM_DOSSIER` int(11) NOT NULL DEFAULT '0',
  `ID_DATECOMMISSION_AFFECT` bigint(20) NOT NULL,
  `ID_DOSSIER_AFFECT` bigint(20) NOT NULL,
  KEY `fk_dossieraffectation_datecommission1_idx` (`ID_DATECOMMISSION_AFFECT`),
  KEY `fk_dossieraffectation_dossier2_idx` (`ID_DOSSIER_AFFECT`),
  CONSTRAINT `fk_dossieraffectation_datecommission1` FOREIGN KEY (`ID_DATECOMMISSION_AFFECT`) REFERENCES `datecommission` (`ID_DATECOMMISSION`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dossieraffectation_dossier1` FOREIGN KEY (`ID_DOSSIER_AFFECT`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossieraffectation`
--

LOCK TABLES `dossieraffectation` WRITE;
/*!40000 ALTER TABLE `dossieraffectation` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossieraffectation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossiercontact`
--

DROP TABLE IF EXISTS `dossiercontact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossiercontact` (
  `ID_DOSSIER` bigint(20) NOT NULL,
  `ID_UTILISATEURINFORMATIONS` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_DOSSIER`,`ID_UTILISATEURINFORMATIONS`),
  KEY `fk_dossiercontact_dossier1_idx` (`ID_DOSSIER`),
  KEY `fk_dossiercontact_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS`),
  CONSTRAINT `fk_dossiercontact_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossiercontact_utilisateurinformations1` FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`) REFERENCES `utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossiercontact`
--

LOCK TABLES `dossiercontact` WRITE;
/*!40000 ALTER TABLE `dossiercontact` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossiercontact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossierdocconsulte`
--

DROP TABLE IF EXISTS `dossierdocconsulte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossierdocconsulte` (
  `ID_DOSSIERDOCCONSULTE` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_NATURE` bigint(20) NOT NULL,
  `REF_CONSULTE` varchar(255) CHARACTER SET latin1 NOT NULL,
  `DATE_CONSULTE` date NOT NULL,
  `DOC_CONSULTE` tinyint(1) NOT NULL DEFAULT '0',
  `ID_DOSSIER` bigint(20) NOT NULL,
  `ID_DOC` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_DOSSIERDOCCONSULTE`),
  KEY `fk_dossierdocconsulte_dossier1_idx` (`ID_DOSSIER`),
  KEY `fk_dossierdocconsulte_listedocconsulte1_idx` (`ID_DOC`),
  CONSTRAINT `fk_dossierdocconsulte_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossierdocconsulte`
--

LOCK TABLES `dossierdocconsulte` WRITE;
/*!40000 ALTER TABLE `dossierdocconsulte` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossierdocconsulte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossierdocmanquant`
--

DROP TABLE IF EXISTS `dossierdocmanquant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossierdocmanquant` (
  `ID_DOCMANQUANT` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_DOSSIER` bigint(20) NOT NULL,
  `NUM_DOCSMANQUANT` varchar(45) NOT NULL,
  `DOCMANQUANT` text,
  `DATE_DOCSMANQUANT` date DEFAULT NULL,
  `DATE_RECEPTION_DOC` date DEFAULT NULL,
  PRIMARY KEY (`ID_DOCMANQUANT`),
  UNIQUE KEY `ID_DOCMANQUANT_UNIQUE` (`ID_DOCMANQUANT`),
  KEY `fk_dossierdocmanquant_dossier1_idx` (`ID_DOSSIER`),
  CONSTRAINT `fk_dossierdocmanquant_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossierdocmanquant`
--

LOCK TABLES `dossierdocmanquant` WRITE;
/*!40000 ALTER TABLE `dossierdocmanquant` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossierdocmanquant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossierdocurba`
--

DROP TABLE IF EXISTS `dossierdocurba`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossierdocurba` (
  `ID_DOCURBA` bigint(20) NOT NULL AUTO_INCREMENT,
  `NUM_DOCURBA` varchar(100) NOT NULL,
  `ID_DOSSIER` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_DOCURBA`),
  KEY `fk_dossierdocurba_dossier1_idx` (`ID_DOSSIER`),
  CONSTRAINT `fk_dossierdocurba_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossierdocurba`
--

LOCK TABLES `dossierdocurba` WRITE;
/*!40000 ALTER TABLE `dossierdocurba` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossierdocurba` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossierlie`
--

DROP TABLE IF EXISTS `dossierlie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossierlie` (
  `ID_DOSSIERLIE` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_DOSSIER1` bigint(20) NOT NULL,
  `ID_DOSSIER2` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_DOSSIERLIE`),
  KEY `fk_dossierlie_dossier1_idx` (`ID_DOSSIER1`),
  KEY `fk_dossierlie_dossier2_idx` (`ID_DOSSIER2`),
  CONSTRAINT `fk_dossierlie_dossier1` FOREIGN KEY (`ID_DOSSIER1`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossierlie_dossier2` FOREIGN KEY (`ID_DOSSIER2`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossierlie`
--

LOCK TABLES `dossierlie` WRITE;
/*!40000 ALTER TABLE `dossierlie` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossierlie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossiernature`
--

DROP TABLE IF EXISTS `dossiernature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossiernature` (
  `ID_DOSSIERNATURE` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_NATURE` bigint(20) NOT NULL,
  `ID_DOSSIER` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_DOSSIERNATURE`),
  KEY `fk_dossiernature_dossiernatureliste1_idx` (`ID_NATURE`),
  KEY `fk_dossiernature_dossier1_idx` (`ID_DOSSIER`),
  CONSTRAINT `fk_dossiernature_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossiernature_dossiernatureliste1` FOREIGN KEY (`ID_NATURE`) REFERENCES `dossiernatureliste` (`ID_DOSSIERNATURE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossiernature`
--

LOCK TABLES `dossiernature` WRITE;
/*!40000 ALTER TABLE `dossiernature` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossiernature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossiernatureliste`
--

DROP TABLE IF EXISTS `dossiernatureliste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossiernatureliste` (
  `ID_DOSSIERNATURE` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOSSIERNATURE` varchar(100) NOT NULL,
  `ID_DOSSIERTYPE` bigint(20) unsigned NOT NULL,
  `ORDRE` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_DOSSIERNATURE`),
  KEY `fk_dossiernatureliste_dossiertype1_idx` (`ID_DOSSIERTYPE`),
  CONSTRAINT `fk_dossiernatureliste_dossiertype1` FOREIGN KEY (`ID_DOSSIERTYPE`) REFERENCES `dossiertype` (`ID_DOSSIERTYPE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossiernatureliste`
--

LOCK TABLES `dossiernatureliste` WRITE;
/*!40000 ALTER TABLE `dossiernatureliste` DISABLE KEYS */;
INSERT INTO `dossiernatureliste` VALUES (1,'Permis de construire (PC)',1,2),(2,'Autorisation de travaux (AT)',1,1),(3,'Dérogation',1,11),(4,'Cahier des charges fonctionnel du SSI',1,12),(5,'Cahier des charges de type T',1,16),(6,'Salon type T',1,7),(7,'Levée de prescriptions',1,5),(8,'Documents divers',1,22),(9,'Changement de DUS (Directeur unique de sécurité)',1,8),(10,'Suivi organisme formation SSIAP',1,9),(11,'Demande de registre de sécurité CTS',1,15),(12,'Demande d\'implantation CTS < 6mois',1,6),(13,'Demande d\'implantation CTS > 6mois',1,14),(14,'Permis d\'aménager',1,18),(15,'Permis de démolir',1,19),(16,'CR de visite des organismes d\'inspection de sécurité incendie (GA)',1,17),(17,'Etude suite à un avis \"différé\"',1,13),(18,'Utilisation exceptionnelle de locaux',1,4),(19,'Levée de réserves suite à un avis défavorable',1,3),(20,'Réception de travaux',2,2),(21,'Périodique',2,1),(22,'Chantier',2,6),(23,'Contrôle',2,4),(24,'Inopinée',2,5),(25,'Réception de travaux',3,2),(26,'Périodique',3,1),(27,'Chantier',3,6),(28,'Contrôle',3,4),(29,'Inopinée',3,5),(30,'Déclaration préalable',1,20),(31,'Locaux SDIS',4,1),(32,'Extérieur SDIS',4,2),(33,'RVRMD (diag sécu)',1,21),(37,'Incendie',6,1),(38,'SAP',6,2),(39,'Inter. div.',6,3),(40,'Ouverture',7,1),(41,'Fermeture',7,2),(42,'Mise en demeure',7,3),(43,'Téléphonique',4,3),(44,'Utilisation exceptionnelle de locaux',7,4),(45,'Courrier',7,NULL),(46,'Echéancier de travaux',1,10),(47,'Avant ouverture',2,3),(48,'Avant ouverture',3,3);
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
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (61,"Autorisation d'une ICPE",1,23);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (62,"Certificats d'urbanisme (CU)",1,24);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (63,"Demande d'organisation de manifestation temporaire",1,25);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (64,"Visite conseil",2,7);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (65,"Autorisation de travaux",5,12);
INSERT INTO `dossiernatureliste` (`ID_DOSSIERNATURE`, `LIBELLE_DOSSIERNATURE`, `ID_DOSSIERTYPE`, `ORDRE`) VALUES (66,"Déclassement / Reclassement",1,26);
/*!40000 ALTER TABLE `dossiernatureliste` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossierpj`
--

DROP TABLE IF EXISTS `dossierpj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossierpj` (
  `ID_PIECEJOINTE` bigint(20) NOT NULL,
  `ID_DOSSIER` bigint(20) NOT NULL,
  `PJ_COMMISSION` tinyint(4) NOT NULL DEFAULT '0',
  KEY `fk_dossierpj_piecejointe1_idx` (`ID_PIECEJOINTE`),
  KEY `fk_dossierpj_dossier1_idx` (`ID_DOSSIER`),
  CONSTRAINT `fk_dossierpj_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossierpj_piecejointe1` FOREIGN KEY (`ID_PIECEJOINTE`) REFERENCES `piecejointe` (`ID_PIECEJOINTE`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossierpj`
--

LOCK TABLES `dossierpj` WRITE;
/*!40000 ALTER TABLE `dossierpj` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossierpj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossierpreventionniste`
--

DROP TABLE IF EXISTS `dossierpreventionniste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossierpreventionniste` (
  `ID_DOSSIER` bigint(20) NOT NULL,
  `ID_PREVENTIONNISTE` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_DOSSIER`,`ID_PREVENTIONNISTE`),
  KEY `fk_dossierpreventionniste_dossier1_idx` (`ID_DOSSIER`),
  KEY `fk_dossierpreventionniste_utilisateur1_idx` (`ID_PREVENTIONNISTE`),
  CONSTRAINT `fk_dossierpreventionniste_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_dossierpreventionniste_utilisateur1` FOREIGN KEY (`ID_PREVENTIONNISTE`) REFERENCES `utilisateur` (`ID_UTILISATEUR`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossierpreventionniste`
--

LOCK TABLES `dossierpreventionniste` WRITE;
/*!40000 ALTER TABLE `dossierpreventionniste` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossierpreventionniste` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossiertextesappl`
--

DROP TABLE IF EXISTS `dossiertextesappl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossiertextesappl` (
  `ID_TEXTESAPPL` bigint(20) NOT NULL,
  `ID_DOSSIER` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_TEXTESAPPL`,`ID_DOSSIER`),
  KEY `fk_table1_dossier1_idx` (`ID_DOSSIER`),
  CONSTRAINT `fk_table1_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_textesappl1` FOREIGN KEY (`ID_TEXTESAPPL`) REFERENCES `textesappl` (`ID_TEXTESAPPL`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossiertextesappl`
--

LOCK TABLES `dossiertextesappl` WRITE;
/*!40000 ALTER TABLE `dossiertextesappl` DISABLE KEYS */;
/*!40000 ALTER TABLE `dossiertextesappl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dossiertype`
--

DROP TABLE IF EXISTS `dossiertype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dossiertype` (
  `ID_DOSSIERTYPE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOSSIERTYPE` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_DOSSIERTYPE`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dossiertype`
--

LOCK TABLES `dossiertype` WRITE;
/*!40000 ALTER TABLE `dossiertype` DISABLE KEYS */;
INSERT INTO `dossiertype` VALUES (1,'Étude'),(2,'Visite de commission'),(3,'Groupe de visite'),(4,'Réunion'),(5,'Courrier / Courriel'),(6,'Intervention'),(7,'Arrêté');
/*!40000 ALTER TABLE `dossiertype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissement`
--

DROP TABLE IF EXISTS `etablissement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissement` (
  `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `NUMEROID_ETABLISSEMENT` varchar(50) DEFAULT NULL,
  `TELEPHONE_ETABLISSEMENT` varchar(20) DEFAULT NULL,
  `FAX_ETABLISSEMENT` varchar(20) DEFAULT NULL,
  `COURRIEL_ETABLISSEMENT` varchar(75) DEFAULT NULL,
  `DATEENREGISTREMENT_ETABLISSEMENT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DESCRIPTIF_ETABLISSEMENT` text,
  `NBPREV_ETABLISSEMENT` tinyint(4) DEFAULT NULL,
  `DUREEVISITE_ETABLISSEMENT` time DEFAULT NULL,
  `ID_DOSSIER_DONNANT_AVIS` bigint(20) DEFAULT NULL,
  `DESCTECH_IMPLANTATION_SURFACE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_IMPLANTATION_SHON_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_IMPLANTATION_SHOB_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_IMPLANTATION_NBNIVEAUX_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_IMPLANTATION_PBDN_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DESSERTE_NBFACADELIBRE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DESSERTE_VOIEENGIN_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DESSERTE_VOIEECHELLE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DESSERTE_ESPACELIBRE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_ISOLEMENT_LATERALCF_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_ISOLEMENT_SUPERPOSECF_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_ISOLEMENT_VISAVIS_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_STABILITE_STRUCTURESF_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_STABILITE_PLANCHERSF_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DISTRIBUTION_CLOISONNEMENTTRAD_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_DISTRIBUTION_SECTEURS_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_DISTRIBUTION_COMPARTIMENTS_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_LOCAUXARISQUE_NBRISQUESMOYENS_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_LOCAUXARISQUE_NBRISQUESIMPORTANTS_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_ESPACES_NOMBRE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_ESPACES_NIVEAUCONCERNE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DESENFUMAGE_NATUREL_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_DESENFUMAGE_MECANIQUE_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_DESENFUMAGE_COMMENTAIRE_ETABLISSEMENT` text,
  `DESCTECH_CHAUFFERIES_NB_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_CHAUFFERIES_PUISSMAX_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_COUPURENRJ_GAZ_ETABLISSEMENT` text,
  `DESCTECH_COUPURENRJ_ELEC_ETABLISSEMENT` text,
  `DESCTECH_COUPURENRJ_PHOTOVOLTAIQUE_ETABLISSEMENT` text,
  `DESCTECH_COUPURENRJ_AUTRE_ETABLISSEMENT` text,
  `DESCTECH_ASCENSEURS_NBTOTAL_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_ASCENSEURS_NBAS4_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_MOYENSSECOURS_COLONNESSECHES_ETABLISSEMENT` tinyint(45) DEFAULT NULL,
  `DESCTECH_MOYENSSECOURS_COLONNESHUMIDES_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_MOYENSSECOURS_RIA_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_MOYENSSECOURS_SPRINKLEUR_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_MOYENSSECOURS_BROUILLARDEAU_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_PCSECU_PRESENCE_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_PCSECU_LOCALISATION_ETABLISSEMENT` text,
  `DESCTECH_SSI_PRESENCE_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_SSI_CATEGORIE_ETABLISSEMENT` char(1) DEFAULT NULL,
  `DESCTECH_SSI_ALARME_TYPE_ETABLISSEMENT` char(2) DEFAULT NULL,
  `DESCTECH_SERVICESECU_EL18_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_SERVICESECU_PERSONNELSDESIGNES_ETABLISSEMENT` tinyint(1) DEFAULT NULL,
  `DESCTECH_SERVICESECU_AGENTDESECU_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_SERVICESECU_CHEFEQUIPE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_SERVICESECU_CHEFDESERVICESECU_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_SERVICESECU_SP_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_SERVICESECU_COMMENTAIRESP_ETABLISSEMENT` text,
  `DESCTECH_DEFENSE_PTEAU_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DEFENSE_VOLUMEPTEAU_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DEFENSE_PTEAUCOMMENTAIRE_ETABLISSEMENT` text,
  `DESCTECH_DEFENSE_PI_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DEFENSE_BI_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_DEFENSE_DEBITSIMULTANE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_CHAUFFERIES_NB30KW` int(11) DEFAULT NULL,
  `DESCTECH_CHAUFFERIES_NB70KW` int(11) DEFAULT NULL,
  `DESCTECH_CHAUFFERIES_NB2MW` int(11) DEFAULT NULL,
  `DESCTECH_CHAUFFERIES_PUISSANCETOTALE` int(11) DEFAULT NULL,
  `DESCRIPTIF_HISTORIQUE_ETABLISSEMENT` text,
  `DESCRIPTIF_DEROGATIONS_ETABLISSEMENT` text,
  `DESCTECH_IMPLANTATION_SURFACETOTALE_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_IMPLANTATION_SURFACEACCPUBLIC_ETABLISSEMENT` int(11) DEFAULT NULL,
  `DESCTECH_RISQUES_NATURELS_ETABLISSEMENT` text,
  `DESCTECH_RISQUES_TECHNOLOGIQUES_ETABLISSEMENT` text,
  PRIMARY KEY (`ID_ETABLISSEMENT`),
  KEY `fk_etablissement_dossier1_idx` (`ID_DOSSIER_DONNANT_AVIS`),
  CONSTRAINT `fk_etablissement_dossier1` FOREIGN KEY (`ID_DOSSIER_DONNANT_AVIS`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissement`
--

LOCK TABLES `etablissement` WRITE;
/*!40000 ALTER TABLE `etablissement` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementadresse`
--

DROP TABLE IF EXISTS `etablissementadresse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementadresse` (
  `ID_ADRESSE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `NUMERO_ADRESSE` varchar(255) DEFAULT NULL,
  `COMPLEMENT_ADRESSE` varchar(255) DEFAULT NULL,
  `LON_ETABLISSEMENTADRESSE` float DEFAULT NULL,
  `LAT_ETABLISSEMENTADRESSE` float DEFAULT NULL,
  `ID_RUE` bigint(20) unsigned NOT NULL,
  `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
  `NUMINSEE_COMMUNE` char(5) NOT NULL,
  PRIMARY KEY (`ID_ADRESSE`),
  KEY `fk_etablissementadresse_etablissement1_idx` (`ID_ETABLISSEMENT`),
  KEY `fk_etablissementadresse_adresserue1_idx` (`ID_RUE`),
  KEY `fk_etablissementadresse_adressecommune1_idx` (`NUMINSEE_COMMUNE`),
  CONSTRAINT `fk_etablissementadresse_adressecommune1` FOREIGN KEY (`NUMINSEE_COMMUNE`) REFERENCES `adressecommune` (`NUMINSEE_COMMUNE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementadresse_adresserue1` FOREIGN KEY (`ID_RUE`) REFERENCES `adresserue` (`ID_RUE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementadresse_etablissement1` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementadresse`
--

LOCK TABLES `etablissementadresse` WRITE;
/*!40000 ALTER TABLE `etablissementadresse` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementadresse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementcontact`
--

DROP TABLE IF EXISTS `etablissementcontact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementcontact` (
  `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
  `ID_UTILISATEURINFORMATIONS` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENT`,`ID_UTILISATEURINFORMATIONS`),
  KEY `fk_etablissementcontact_etablissement1_idx` (`ID_ETABLISSEMENT`),
  KEY `fk_etablissementcontact_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS`),
  CONSTRAINT `fk_etablissementcontact_etablissement1` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementcontact_utilisateurinformations1` FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`) REFERENCES `utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementcontact`
--

LOCK TABLES `etablissementcontact` WRITE;
/*!40000 ALTER TABLE `etablissementcontact` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementcontact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementdossier`
--

DROP TABLE IF EXISTS `etablissementdossier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementdossier` (
  `ID_ETABLISSEMENTDOSSIER` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
  `ID_DOSSIER` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTDOSSIER`),
  KEY `fk_etablissementdossier_etablissement1_idx` (`ID_ETABLISSEMENT`),
  KEY `fk_etablissementdossier_dossier1_idx` (`ID_DOSSIER`),
  CONSTRAINT `fk_etablissementdossier_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementdossier_etablissement1` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementdossier`
--

LOCK TABLES `etablissementdossier` WRITE;
/*!40000 ALTER TABLE `etablissementdossier` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementdossier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementinformations`
--

DROP TABLE IF EXISTS `etablissementinformations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementinformations` (
  `ID_ETABLISSEMENTINFORMATIONS` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_ETABLISSEMENTINFORMATIONS` varchar(255) NOT NULL,
  `ICPE_ETABLISSEMENTINFORMATIONS` tinyint(1) DEFAULT NULL,
  `PERIODICITE_ETABLISSEMENTINFORMATIONS` tinyint(4) DEFAULT NULL,
  `R12320_ETABLISSEMENTINFORMATIONS` tinyint(1) DEFAULT NULL,
  `DROITPUBLIC_ETABLISSEMENTINFORMATIONS` tinyint(1) DEFAULT NULL,
  `LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS` tinyint(1) DEFAULT NULL,
  `EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS` int(11) DEFAULT NULL,
  `EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS` int(11) DEFAULT NULL,
  `EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS` int(11) DEFAULT NULL,
  `EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS` int(11) DEFAULT NULL,
  `COMPLEMENT_ETABLISSEMENTINFORMATIONS` varchar(255) DEFAULT NULL,
  `UTILISATEUR_ETABLISSEMENTINFORMATIONS` bigint(20) unsigned zerofill DEFAULT NULL,
  `DATE_ETABLISSEMENTINFORMATIONS` date NOT NULL,
  `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
  `ID_GENRE` int(2) unsigned NOT NULL,
  `ID_CLASSE` int(11) unsigned DEFAULT NULL,
  `ID_CLASSEMENT` int(11) unsigned DEFAULT NULL,
  `ID_FAMILLE` int(11) DEFAULT NULL,
  `ID_CATEGORIE` int(1) unsigned DEFAULT NULL,
  `ID_TYPE` int(10) unsigned DEFAULT NULL,
  `ID_TYPEACTIVITE` int(11) unsigned DEFAULT NULL,
  `ID_COMMISSION` bigint(20) unsigned DEFAULT NULL,
  `ID_STATUT` int(1) unsigned NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONS`),
  KEY `UTILISATEUR_ETABLISSEMENTINFORMATIONS` (`UTILISATEUR_ETABLISSEMENTINFORMATIONS`),
  KEY `fk_etablissementinformations_etablissement1_idx` (`ID_ETABLISSEMENT`),
  KEY `fk_etablissementinformations_genre1_idx` (`ID_GENRE`),
  KEY `fk_etablissementinformations_classe1_idx` (`ID_CLASSE`),
  KEY `fk_etablissementinformations_classement1_idx` (`ID_CLASSEMENT`),
  KEY `fk_etablissementinformations_famille1_idx` (`ID_FAMILLE`),
  KEY `fk_etablissementinformations_categorie1_idx` (`ID_CATEGORIE`),
  KEY `fk_etablissementinformations_type1_idx` (`ID_TYPE`),
  KEY `fk_etablissementinformations_typeactivite1_idx` (`ID_TYPEACTIVITE`),
  KEY `fk_etablissementinformations_commission1_idx` (`ID_COMMISSION`),
  KEY `fk_etablissementinformations_statut1_idx` (`ID_STATUT`),
  CONSTRAINT `fk_etablissementinformations_categorie1` FOREIGN KEY (`ID_CATEGORIE`) REFERENCES `categorie` (`ID_CATEGORIE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_classe1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `classe` (`ID_CLASSE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_classement1` FOREIGN KEY (`ID_CLASSEMENT`) REFERENCES `classement` (`ID_CLASSEMENT`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_commission1` FOREIGN KEY (`ID_COMMISSION`) REFERENCES `commission` (`ID_COMMISSION`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_etablissement1` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementinformations_famille1` FOREIGN KEY (`ID_FAMILLE`) REFERENCES `famille` (`ID_FAMILLE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_genre1` FOREIGN KEY (`ID_GENRE`) REFERENCES `genre` (`ID_GENRE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_statut1` FOREIGN KEY (`ID_STATUT`) REFERENCES `statut` (`ID_STATUT`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_type1` FOREIGN KEY (`ID_TYPE`) REFERENCES `type` (`ID_TYPE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformations_typeactivite1` FOREIGN KEY (`ID_TYPEACTIVITE`) REFERENCES `typeactivite` (`ID_TYPEACTIVITE`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementinformations`
--

LOCK TABLES `etablissementinformations` WRITE;
/*!40000 ALTER TABLE `etablissementinformations` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementinformations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementinformationsplan`
--

DROP TABLE IF EXISTS `etablissementinformationsplan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementinformationsplan` (
  `ID_ETABLISSEMENTINFORMATIONSPLAN` bigint(20) NOT NULL AUTO_INCREMENT,
  `NUMERO_ETABLISSEMENTPLAN` text DEFAULT NULL,
  `DATE_ETABLISSEMENTPLAN` date NOT NULL,
  `MISEAJOUR_ETABLISSEMENTPLAN` tinyint(1) DEFAULT NULL,
  `ID_ETABLISSEMENTINFORMATIONS` bigint(20) unsigned NOT NULL,
  `ID_TYPEPLAN` int(11) NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONSPLAN`),
  KEY `fk_etablissementinformationsplan_etablissementinformations1_idx` (`ID_ETABLISSEMENTINFORMATIONS`),
  KEY `fk_etablissementinformationsplan_typeplan1_idx` (`ID_TYPEPLAN`),
  CONSTRAINT `fk_etablissementinformationsplan_etablissementinformations1` FOREIGN KEY (`ID_ETABLISSEMENTINFORMATIONS`) REFERENCES `etablissementinformations` (`ID_ETABLISSEMENTINFORMATIONS`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementinformationsplan_typeplan1` FOREIGN KEY (`ID_TYPEPLAN`) REFERENCES `typeplan` (`ID_TYPEPLAN`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementinformationsplan`
--

LOCK TABLES `etablissementinformationsplan` WRITE;
/*!40000 ALTER TABLE `etablissementinformationsplan` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementinformationsplan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementinformationspreventionniste`
--

DROP TABLE IF EXISTS `etablissementinformationspreventionniste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementinformationspreventionniste` (
  `ID_ETABLISSEMENTINFORMATIONS` bigint(20) unsigned NOT NULL,
  `ID_UTILISATEUR` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONS`,`ID_UTILISATEUR`),
  KEY `fk_etablissementinformationspreventionniste_etablissementin_idx` (`ID_ETABLISSEMENTINFORMATIONS`),
  KEY `fk_etablissementinformationspreventionniste_utilisateur1_idx` (`ID_UTILISATEUR`),
  CONSTRAINT `fk_etablissementinformationspreventionniste_etablissementinfo1` FOREIGN KEY (`ID_ETABLISSEMENTINFORMATIONS`) REFERENCES `etablissementinformations` (`ID_ETABLISSEMENTINFORMATIONS`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementinformationspreventionniste_utilisateur1` FOREIGN KEY (`ID_UTILISATEUR`) REFERENCES `utilisateur` (`ID_UTILISATEUR`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementinformationspreventionniste`
--

LOCK TABLES `etablissementinformationspreventionniste` WRITE;
/*!40000 ALTER TABLE `etablissementinformationspreventionniste` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementinformationspreventionniste` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementinformationsrubrique`
--

DROP TABLE IF EXISTS `etablissementinformationsrubrique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementinformationsrubrique` (
  `ID_ETABLISSEMENTINFORMATIONSRUBRIQUE` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_RUBRIQUE` tinyint(4) NOT NULL,
  `NUMERO_ETABLISSEMENTINFORMATIONSRUBRIQUE` int(11) NOT NULL,
  `VALEUR_ETABLISSEMENTINFORMATIONSRUBRIQUE` float NOT NULL,
  `NOM_ETABLISSEMENTINFORMATIONSRUBRIQUE` varchar(500) NOT NULL,
  `CLASSEMENT_ETABLISSEMENTINFORMATIONSRUBRIQUE` varchar(50) DEFAULT NULL,
  `ID_ETABLISSEMENTINFORMATIONS` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONSRUBRIQUE`),
  KEY `ID_ETABLISSEMENTINFORMATIONS` (`ID_RUBRIQUE`),
  KEY `fk_etablissementinformationsrubrique_etablissementinformati_idx` (`ID_ETABLISSEMENTINFORMATIONS`),
  CONSTRAINT `fk_etablissementinformationsrubrique_etablissementinformations1` FOREIGN KEY (`ID_ETABLISSEMENTINFORMATIONS`) REFERENCES `etablissementinformations` (`ID_ETABLISSEMENTINFORMATIONS`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementinformationsrubrique`
--

LOCK TABLES `etablissementinformationsrubrique` WRITE;
/*!40000 ALTER TABLE `etablissementinformationsrubrique` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementinformationsrubrique` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementinformationstypesactivitessecondaires`
--

DROP TABLE IF EXISTS `etablissementinformationstypesactivitessecondaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementinformationstypesactivitessecondaires` (
  `ID_ETABLISSEMENTINFORMATIONSTYPESACTIVITESSECONDAIRES` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ID_ETABLISSEMENTINFORMATIONS` bigint(20) unsigned NOT NULL,
  `ID_TYPE_SECONDAIRE` int(10) unsigned NOT NULL,
  `ID_TYPEACTIVITE_SECONDAIRE` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENTINFORMATIONSTYPESACTIVITESSECONDAIRES`),
  KEY `fk_etablissementinformationstypesactivitessecondaires_etabl_idx` (`ID_ETABLISSEMENTINFORMATIONS`),
  KEY `fk_etablissementinformationstypesactivitessecondaires_type1_idx` (`ID_TYPE_SECONDAIRE`),
  KEY `fk_etablissementinformationstypesactivitessecondaires_typea_idx` (`ID_TYPEACTIVITE_SECONDAIRE`),
  CONSTRAINT `fk_etablissementinformationstypesactivitessecondaires_etablis1` FOREIGN KEY (`ID_ETABLISSEMENTINFORMATIONS`) REFERENCES `etablissementinformations` (`ID_ETABLISSEMENTINFORMATIONS`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementinformationstypesactivitessecondaires_type1` FOREIGN KEY (`ID_TYPE_SECONDAIRE`) REFERENCES `type` (`ID_TYPE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_etablissementinformationstypesactivitessecondaires_typeact1` FOREIGN KEY (`ID_TYPEACTIVITE_SECONDAIRE`) REFERENCES `typeactivite` (`ID_TYPEACTIVITE`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementinformationstypesactivitessecondaires`
--

LOCK TABLES `etablissementinformationstypesactivitessecondaires` WRITE;
/*!40000 ALTER TABLE `etablissementinformationstypesactivitessecondaires` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementinformationstypesactivitessecondaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementlie`
--

DROP TABLE IF EXISTS `etablissementlie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementlie` (
  `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
  `ID_FILS_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_ETABLISSEMENT`,`ID_FILS_ETABLISSEMENT`),
  KEY `fk_etablissementlie_etablissement2_idx` (`ID_FILS_ETABLISSEMENT`),
  CONSTRAINT `fk_etablissementlie_etablissement1` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementlie_etablissement2` FOREIGN KEY (`ID_FILS_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementlie`
--

LOCK TABLES `etablissementlie` WRITE;
/*!40000 ALTER TABLE `etablissementlie` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementlie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementpj`
--

DROP TABLE IF EXISTS `etablissementpj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementpj` (
  `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
  `ID_PIECEJOINTE` bigint(20) NOT NULL,
  `PLACEMENT_ETABLISSEMENTPJ` int(11) DEFAULT '0',
  PRIMARY KEY (`ID_ETABLISSEMENT`,`ID_PIECEJOINTE`),
  KEY `fk_etablissementpj_etablissement1_idx` (`ID_ETABLISSEMENT`),
  KEY `fk_etablissementpj_piecejointe1_idx` (`ID_PIECEJOINTE`),
  CONSTRAINT `fk_etablissementpj_etablissement1` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementpj_piecejointe1` FOREIGN KEY (`ID_PIECEJOINTE`) REFERENCES `piecejointe` (`ID_PIECEJOINTE`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementpj`
--

LOCK TABLES `etablissementpj` WRITE;
/*!40000 ALTER TABLE `etablissementpj` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementpj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etablissementtextapp`
--

DROP TABLE IF EXISTS `etablissementtextapp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etablissementtextapp` (
  `ID_TEXTESAPPL` bigint(20) NOT NULL,
  `ID_ETABLISSEMENT` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_TEXTESAPPL`,`ID_ETABLISSEMENT`),
  KEY `fk_etablissementtextapp_etablissement1_idx` (`ID_ETABLISSEMENT`),
  CONSTRAINT `fk_etablissementtextapp_etablissement1` FOREIGN KEY (`ID_ETABLISSEMENT`) REFERENCES `etablissement` (`ID_ETABLISSEMENT`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etablissementtextapp_textesappl1` FOREIGN KEY (`ID_TEXTESAPPL`) REFERENCES `textesappl` (`ID_TEXTESAPPL`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etablissementtextapp`
--

LOCK TABLES `etablissementtextapp` WRITE;
/*!40000 ALTER TABLE `etablissementtextapp` DISABLE KEYS */;
/*!40000 ALTER TABLE `etablissementtextapp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `famille`
--

DROP TABLE IF EXISTS `famille`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `famille` (
  `ID_FAMILLE` int(11) NOT NULL AUTO_INCREMENT,
  `LIBELLE_FAMILLE` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_FAMILLE`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `famille`
--

LOCK TABLES `famille` WRITE;
/*!40000 ALTER TABLE `famille` DISABLE KEYS */;
INSERT INTO `famille` VALUES (1,'Aucune'),(2,'1ère famille'),(3,'2ème famille'),(4,'2ème famille collective'),(5,'3ème famille A'),(6,'3ème famille B'),(7,'4ème famille'),(8,'Logements-foyers pour personne autres que personnes âgées et handicapés physique'),(9,'Logements-foyers pour personnes âgées'),(10,'Logements-foyers pour handicapés physiques ayant leur autonomie'),(11,"3ème famille (classement antérieur à l'A. du 31/01/1986 modifié)");
/*!40000 ALTER TABLE `famille` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonction`
--

DROP TABLE IF EXISTS `fonction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fonction` (
  `ID_FONCTION` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_FONCTION` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`ID_FONCTION`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonction`
--

LOCK TABLES `fonction` WRITE;
/*!40000 ALTER TABLE `fonction` DISABLE KEYS */;
INSERT INTO `fonction` VALUES (1,'Préfet'),(2,'Maire'),(3,'Maitre d\'ouvrage'),(4,'Maitre d\'oeuvre'),(5,'Pétitionnaire demandeur'),(6,'Controller technique (organisme agréé)'),(7,'Exploitant'),(8,'Directeur unique de sécurité'),(9,'Responsable de sécurité'),(10,'Participant'),(11,'Demandeur'),(12,'Simple utilisateur'),(13,'Préventionniste'),(15,'Secrétariat'),(16,'Service informatique'),(17,'Propriétaire'),(99,'Utilisateur spécial');
/*!40000 ALTER TABLE `fonction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `genre` (
  `ID_GENRE` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_GENRE` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_GENRE`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genre`
--

LOCK TABLES `genre` WRITE;
/*!40000 ALTER TABLE `genre` DISABLE KEYS */;
INSERT INTO `genre` VALUES (1,'Site'),(2,'Établissement'),(3,'Cellule'),(4,'Habitation'),(5,'IGH'),(6,'EIC');
INSERT INTO `genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (7, 'Camping');
INSERT INTO `genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (8, 'Manifestation Temporaire');
INSERT INTO `genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (9, 'IOP');
INSERT INTO `genre` (`ID_GENRE`, `LIBELLE_GENRE`) VALUES (10, 'Zone');
/*!40000 ALTER TABLE `genre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupe`
--

DROP TABLE IF EXISTS `groupe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupe` (
  `ID_GROUPE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_GROUPE` varchar(255) NOT NULL,
  `DESC_GROUPE` text NOT NULL,
  PRIMARY KEY (`ID_GROUPE`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupe`
--

LOCK TABLES `groupe` WRITE;
/*!40000 ALTER TABLE `groupe` DISABLE KEYS */;
INSERT INTO `groupe` VALUES (1,'Groupe par défaut','Ceci est le groupe qui contient les nouveaux utilisateurs et les utilisateurs dont les groupes ont été supprimés.');
/*!40000 ALTER TABLE `groupe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupe-privileges`
--

DROP TABLE IF EXISTS `groupe-privileges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupe-privileges` (
  `ID_GROUPE` bigint(20) unsigned NOT NULL,
  `id_privilege` bigint(19) unsigned NOT NULL,
  PRIMARY KEY (`ID_GROUPE`,`id_privilege`),
  KEY `fk_groupe-privileges_privileges1_idx` (`id_privilege`),
  KEY `fk_groupe-privileges_groupe1_idx` (`ID_GROUPE`),
  CONSTRAINT `fk_groupe-privileges_groupe1` FOREIGN KEY (`ID_GROUPE`) REFERENCES `groupe` (`ID_GROUPE`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_groupe-privileges_privileges1` FOREIGN KEY (`id_privilege`) REFERENCES `privileges` (`id_privilege`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupe-privileges`
--

LOCK TABLES `groupe-privileges` WRITE;
/*!40000 ALTER TABLE `groupe-privileges` DISABLE KEYS */;
INSERT INTO `groupe-privileges` (`ID_GROUPE`, `id_privilege`) VALUES ('1', '11');
/*!40000 ALTER TABLE `groupe-privileges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupement`
--

DROP TABLE IF EXISTS `groupement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupement` (
  `ID_GROUPEMENT` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_GROUPEMENT` varchar(255) NOT NULL,
  `ID_UTILISATEURINFORMATIONS` bigint(20) DEFAULT NULL,
  `ID_GROUPEMENTTYPE` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_GROUPEMENT`),
  KEY `ID_UTILISATEURINFORMATIONS` (`ID_UTILISATEURINFORMATIONS`),
  KEY `fk_groupement_groupementtype1_idx` (`ID_GROUPEMENTTYPE`),
  CONSTRAINT `fk_groupement_groupementtype1` FOREIGN KEY (`ID_GROUPEMENTTYPE`) REFERENCES `groupementtype` (`ID_GROUPEMENTTYPE`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupement`
--

LOCK TABLES `groupement` WRITE;
/*!40000 ALTER TABLE `groupement` DISABLE KEYS */;
/*!40000 ALTER TABLE `groupement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupementcommune`
--

DROP TABLE IF EXISTS `groupementcommune`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupementcommune` (
  `ID_GROUPEMENT` bigint(20) unsigned NOT NULL,
  `NUMINSEE_COMMUNE` char(5) NOT NULL,
  PRIMARY KEY (`ID_GROUPEMENT`,`NUMINSEE_COMMUNE`),
  KEY `fk_groupementcommune_groupement1_idx` (`ID_GROUPEMENT`),
  KEY `fk_groupementcommune_adressecommune1_idx` (`NUMINSEE_COMMUNE`),
  CONSTRAINT `fk_groupementcommune_adressecommune1` FOREIGN KEY (`NUMINSEE_COMMUNE`) REFERENCES `adressecommune` (`NUMINSEE_COMMUNE`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_groupementcommune_groupement1` FOREIGN KEY (`ID_GROUPEMENT`) REFERENCES `groupement` (`ID_GROUPEMENT`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupementcommune`
--

LOCK TABLES `groupementcommune` WRITE;
/*!40000 ALTER TABLE `groupementcommune` DISABLE KEYS */;
/*!40000 ALTER TABLE `groupementcommune` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupementcontact`
--

DROP TABLE IF EXISTS `groupementcontact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupementcontact` (
  `ID_GROUPEMENT` bigint(20) unsigned NOT NULL,
  `ID_UTILISATEURINFORMATIONS` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_GROUPEMENT`,`ID_UTILISATEURINFORMATIONS`),
  KEY `fk_groupementcontact_groupement1_idx` (`ID_GROUPEMENT`),
  KEY `fk_groupementcontact_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS`),
  CONSTRAINT `fk_groupementcontact_groupement1` FOREIGN KEY (`ID_GROUPEMENT`) REFERENCES `groupement` (`ID_GROUPEMENT`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_groupementcontact_utilisateurinformations1` FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`) REFERENCES `utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupementcontact`
--

LOCK TABLES `groupementcontact` WRITE;
/*!40000 ALTER TABLE `groupementcontact` DISABLE KEYS */;
/*!40000 ALTER TABLE `groupementcontact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupementpreventionniste`
--

DROP TABLE IF EXISTS `groupementpreventionniste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupementpreventionniste` (
  `ID_GROUPEMENT` bigint(20) unsigned NOT NULL,
  `ID_UTILISATEUR` bigint(20) unsigned NOT NULL,
  `DATEDEBUT_GROUPEMENTPREVENTIONNISTE` datetime NOT NULL,
  `DATEFIN_GROUPEMENTPREVENTIONNISTE` datetime DEFAULT NULL,
  PRIMARY KEY (`ID_GROUPEMENT`,`ID_UTILISATEUR`),
  KEY `fk_groupementpreventionniste_groupement1_idx` (`ID_GROUPEMENT`),
  KEY `fk_groupementpreventionniste_utilisateur1_idx` (`ID_UTILISATEUR`),
  CONSTRAINT `fk_groupementpreventionniste_groupement1` FOREIGN KEY (`ID_GROUPEMENT`) REFERENCES `groupement` (`ID_GROUPEMENT`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_groupementpreventionniste_utilisateur1` FOREIGN KEY (`ID_UTILISATEUR`) REFERENCES `utilisateur` (`ID_UTILISATEUR`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupementpreventionniste`
--

LOCK TABLES `groupementpreventionniste` WRITE;
/*!40000 ALTER TABLE `groupementpreventionniste` DISABLE KEYS */;
/*!40000 ALTER TABLE `groupementpreventionniste` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupementtype`
--

DROP TABLE IF EXISTS `groupementtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupementtype` (
  `ID_GROUPEMENTTYPE` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_GROUPEMENTTYPE` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_GROUPEMENTTYPE`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupementtype`
--

LOCK TABLES `groupementtype` WRITE;
/*!40000 ALTER TABLE `groupementtype` DISABLE KEYS */;
INSERT INTO `groupementtype` VALUES (1,'Département'),(2,'Arrondissement'),(3,'Canton'),(4,'Intercommunalité'),(5,'Groupement territorial'),(6,'Centre de secours'),(7,'Secrétariat prévention SDIS'),(8,'Secteur de prévention'),(9,'Secrétariat prévision'),(10,'Service instructeur'),(11,'DDSP'),(12,'Gendarmerie'),(13,'Autre service');
/*!40000 ALTER TABLE `groupementtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listedocajout`
--

DROP TABLE IF EXISTS `listedocajout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listedocajout` (
  `ID_DOCAJOUT` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOCAJOUT` text CHARACTER SET latin1 NOT NULL,
  `REF_DOCAJOUT` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `DATE_DOCAJOUT` date DEFAULT NULL,
  `ID_NATURE` bigint(20) NOT NULL,
  `ID_DOSSIER` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_DOCAJOUT`),
  KEY `fk_listedocajout_dossier1_idx` (`ID_DOSSIER`),
  CONSTRAINT `fk_listedocajout_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listedocajout`
--

LOCK TABLES `listedocajout` WRITE;
/*!40000 ALTER TABLE `listedocajout` DISABLE KEYS */;
/*!40000 ALTER TABLE `listedocajout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listedocconsulte`
--

DROP TABLE IF EXISTS `listedocconsulte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listedocconsulte` (
  `ID_DOC` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_DOC` text CHARACTER SET latin1,
  `VISITE_DOC` tinyint(1) NOT NULL DEFAULT '0',
  `ETUDE_DOC` tinyint(1) NOT NULL DEFAULT '0',
  `VISITERT_DOC` tinyint(1) NOT NULL DEFAULT '0',
  `VISITEVAO_DOC` tinyint(1) NOT NULL DEFAULT '0',
  `ORDRE_DOC` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_DOC`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listedocconsulte`
--

LOCK TABLES `listedocconsulte` WRITE;
/*!40000 ALTER TABLE `listedocconsulte` DISABLE KEYS */;
INSERT INTO `listedocconsulte` (`ID_DOC`, `LIBELLE_DOC`, `VISITE_DOC`, `ETUDE_DOC`, `VISITERT_DOC`, `VISITEVAO_DOC`, `ORDRE_DOC`) VALUES
(1, 'Un courrier de', 0, 1, 0, 0, 0),
(2, 'Un jeu de plans', 0, 1, 0, 0, 0),
(3, 'Une notice de sécurité', 0, 1, 0, 0, 0),
(4, 'Une notice descriptive', 0, 1, 0, 0, 0),
(5, 'Un engagement solidité', 0, 1, 0, 0, 0),
(6, 'Un rapport initial de contrôle technique', 0, 1, 0, 0, 0),
(7, 'Une attestation du directeur unique de sécurité', 0, 1, 0, 0, 0),
(8, 'Désenfumage mécanique : Tous les ans par un technicien compétent (DF 10)', 1, 0, 0, 0, 6),
(9, 'Désenfumage mécanique : Organisme agréé 3 ans SSI A et B', 1, 0, 0, 0, 7),
(10, 'Chauffage : Relevé de vérification du chauffage-ventilation (CH 58)', 1, 0, 0, 0, 8),
(11, 'Ramonage : Attestation de ramonage ou visite des conduits (CH 57)', 1, 0, 0, 0, 10),
(12, 'Gaz : Relevé de vérification du gaz (GZ 30) (GZ 29)', 1, 0, 0, 0, 12),
(13, 'Gaz médicaux : Relevé de vérification des fluides médicaux (U 64 / J 33)', 1, 0, 0, 0, 37),
(14, 'Électricité : Relevé ou RVRE des installations électriques et/ou des paratonnerres ou protection contre la foudre (EL 19)', 1, 0, 0, 0, 14),
(15, 'Ascenseurs Monte-charges : RVRE quinquennal des ascenseurs (AS 9)', 1, 0, 0, 0, 18),
(16, 'Ascenseurs Monte-charges : Tous les ans (AS 11)', 1, 0, 0, 0, 17),
(17, 'Ascenseurs Monte-charges : Contrat d’entretien des ascenseurs, escaliers mécaniques et trottoirs roulants (AS 8)', 1, 0, 0, 0, 19),
(18, 'Hotte : Attestation de nettoyage du circuit d’extraction (GC 21)', 1, 0, 0, 0, 22),
(19, 'Appareils de cuisson : Relevé de vérification des appareils de cuisson (GC 22)', 1, 0, 0, 0, 21),
(20, 'Extincteurs : Relevé de vérification des extincteurs (MS 38)', 1, 0, 0, 0, 30),
(21, 'Extincteurs : Révision décennale des extincteurs (MS 38)', 1, 0, 0, 0, 31),
(22, 'Autres installations : Relevé de vérification des colonnes sèches (MS 73)', 1, 0, 0, 0, 42),
(23, 'RIA : Relevé de vérification des R.I.A. (MS 73)', 1, 0, 0, 0, 29),
(24, 'Installation fixe d''extinction automatique à eau : Relevé de vérification du système d’extinction automatique du type sprinkleur (MS 29/73) ou déversoirs et rideaux d’eau (L 57)', 1, 0, 0, 0, 27),
(25, 'Installation fixe d''extinction automatique à eau : RVRE triennal du sprinkleur (MS 73)', 1, 0, 0, 0, 28),
(26, 'Système de sécurité Incendie Alarme Détection : RVRE triennal du SSI A ou B (MS 73)', 1, 0, 0, 0, 24),
(27, 'Système de sécurité Incendie Alarme Détection : Relevé de vérification du SSI ou d’équipement d’alarme, détection, portes, clapets coupe-feu (MS 73)', 1, 0, 0, 0, 23),
(28, 'Système de sécurité Incendie Alarme Détection :  Contrat d’entretien du Système de Sécurité Incendie (MS 58)', 1, 0, 0, 0, 25),
(29, 'Système de sécurité Incendie Alarme Détection : Dossier d’identité du Système de Sécurité Incendie (MS 73)', 1, 0, 0, 0, 26),
(30, 'Communication radioélectrique : Attestation de vérification des communications radioélectriques (MS 71)', 1, 0, 0, 0, 35),
(31, 'Communication radioélectrique : RVRE triennal des moyens de communications radioélectriques mode relayés (MS 71)', 1, 0, 0, 0, 36),
(32, 'Relevé de vérification des PI/BI (MS 73)', 1, 0, 0, 0, 34),
(33, 'Portes automatiques : Relevé de vérification des portes automatiques en façade (CO 48)', 1, 0, 0, 0, 3),
(34, 'Portes automatiques : Contrat d’entretien des portes automatiques en façade (CO 48)', 1, 0, 0, 0, 4),
(35, 'Espaces scéniques : RVRE des équipements de levage des salles avec espace scénique (L 57)', 1, 0, 0, 0, 38),
(36, 'Espaces scéniques : RVRE triennal des salles de spectacles avec espace scénique (L 57)', 1, 0, 0, 0, 39),
(37, 'Dossier technique amiante', 1, 0, 0, 0, 2),
(38, 'Formation Exercice : Exercice d’évacuation réalisé', 1, 0, 0, 0, 32),
(39, 'Formation Exercice :  Formation des personnels à l’utilisation des moyens de secours', 1, 0, 0, 0, 33),
(40, 'Cahier des charges fonctionnelles du SSI', 0, 1, 0, 0, 0),
(41, 'Attestation de solidité', 0, 0, 1, 1, 0),
(42, 'Attestation du maître d''ouvrage', 0, 0, 1, 1, 0),
(43, 'RVRAT', 0, 0, 1, 1, 0),
(44, 'Attestation de réception des hydrants', 0, 0, 1, 1, 0),
(45, 'Dossier d''identité SSI', 0, 0, 1, 1, 0),
(46, 'Rapport de réception technique du SSI', 0, 0, 1, 1, 0),
(47, 'Mise à jour du dossier d''identité du SSI', 0, 0, 1, 0, 0),
(48, 'Attestation de réception de modification sprinkleur', 0, 0, 1, 0, 0),
(49, 'Attestation de réception de modification de détection incendie', 0, 0, 1, 0, 0),
(50, 'Registre de sécurité', 1, 0, 0, 0, 1),
(51, 'Désenfumage naturel : Tous les ans par un technicien compétent (DF 10)', 1, 0, 0, 0, 5),
(52, 'Etanchéité (gaz liquide frigorigène) : Tous les ans par un technicien compétent (CH 58) (CH 57)', 1, 0, 0, 0, 9),
(53, 'Traitement air VMC : Tous les ans par un technicien compétent (CH 58) (CH 57)', 1, 0, 0, 0, 11),
(54, 'Groupe électrogène : TC/15j / 1/mois (EL18)', 1, 0, 0, 0, 13),
(55, 'Installations extérieures de protection contre la foudre : Tous les ans par un technicien compétent (EL 19)', 1, 0, 0, 0, 15),
(56, 'Éclairage : Tous les ans par un technicien compétent (EC 15)', 1, 0, 0, 0, 16),
(57, 'Escaliers mécaniques : Annuel OA (AS 10)', 1, 0, 0, 0, 20),
(58, 'Réglage luminosité et son : L 13 OA 1 an', 1, 0, 0, 0, 40),
(59, '5ème avec hébergement : PE4 PO1 - 2 ans TC : SSI DF manuel CH GC GZ - 1 an TC : EL/détection + contrat', 1, 0, 0, 0, 41);
/*!40000 ALTER TABLE `listedocconsulte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `ID_NEWS` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `TYPE_NEWS` varchar(100) NOT NULL,
  `TEXTE_NEWS` text NOT NULL,
  `ID_UTILISATEUR` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_NEWS`),
  KEY `fk_news_utilisateur1_idx` (`ID_UTILISATEUR`),
  CONSTRAINT `fk_news_utilisateur1` FOREIGN KEY (`ID_UTILISATEUR`) REFERENCES `utilisateur` (`ID_UTILISATEUR`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsgroupe`
--

DROP TABLE IF EXISTS `newsgroupe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsgroupe` (
  `ID_NEWS` bigint(19) unsigned NOT NULL,
  `ID_GROUPE` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_NEWS`,`ID_GROUPE`),
  KEY `fk_newsgroupe_groupe1_idx` (`ID_GROUPE`),
  CONSTRAINT `fk_newsgroupe_groupe1` FOREIGN KEY (`ID_GROUPE`) REFERENCES `groupe` (`ID_GROUPE`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_newsgroupe_news1` FOREIGN KEY (`ID_NEWS`) REFERENCES `news` (`ID_NEWS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsgroupe`
--

LOCK TABLES `newsgroupe` WRITE;
/*!40000 ALTER TABLE `newsgroupe` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsgroupe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periodicite`
--

DROP TABLE IF EXISTS `periodicite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periodicite` (
  `ID_CATEGORIE` int(2) unsigned NOT NULL,
  `ID_TYPE` varchar(10) NOT NULL,
  `LOCALSOMMEIL_PERIODICITE` tinyint(4) NOT NULL DEFAULT '0',
  `PERIODICITE_PERIODICITE` int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_CATEGORIE`,`ID_TYPE`,`LOCALSOMMEIL_PERIODICITE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodicite`
--

LOCK TABLES `periodicite` WRITE;
/*!40000 ALTER TABLE `periodicite` DISABLE KEYS */;
INSERT INTO `periodicite` VALUES (0,'1',0,0),(0,'10',0,0),(0,'11',0,0),(0,'2',0,0),(0,'3',0,0),(0,'4',0,0),(0,'5',0,0),(0,'6',0,0),(0,'7',0,0),(0,'8',0,0),(0,'9',0,0),(1,'1',0,0),(1,'1',1,0),(1,'10',0,24),(1,'10',1,0),(1,'11',0,24),(1,'11',1,0),(1,'12',0,0),(1,'12',1,0),(1,'13',0,24),(1,'13',1,0),(1,'14',0,0),(1,'14',1,0),(1,'15',0,0),(1,'15',1,0),(1,'16',0,0),(1,'16',1,0),(1,'17',0,24),(1,'17',1,24),(1,'18',0,0),(1,'18',1,0),(1,'19',0,36),(1,'19',1,0),(1,'2',0,0),(1,'2',1,0),(1,'20',0,0),(1,'20',1,0),(1,'21',0,24),(1,'21',1,0),(1,'22',0,24),(1,'22',1,0),(1,'23',0,60),(1,'23',1,0),(1,'24',0,36),(1,'24',1,0),(1,'25',0,36),(1,'25',1,0),(1,'26',0,36),(1,'26',1,0),(1,'27',0,0),(1,'27',1,0),(1,'3',0,0),(1,'3',1,0),(1,'4',0,0),(1,'4',1,0),(1,'5',0,0),(1,'5',1,0),(1,'6',0,0),(1,'6',1,0),(1,'7',0,24),(1,'7',1,0),(1,'8',0,24),(1,'8',1,0),(1,'9',0,24),(1,'9',1,0),(2,'1',0,0),(2,'1',1,0),(2,'10',0,36),(2,'10',1,0),(2,'11',0,24),(2,'11',1,0),(2,'12',0,0),(2,'12',1,0),(2,'13',0,24),(2,'13',1,0),(2,'14',0,0),(2,'14',1,0),(2,'15',0,0),(2,'15',1,0),(2,'16',0,0),(2,'16',1,0),(2,'17',0,36),(2,'17',1,24),(2,'18',0,0),(2,'18',1,0),(2,'19',0,36),(2,'19',1,0),(2,'2',0,0),(2,'2',1,0),(2,'20',0,0),(2,'20',1,0),(2,'21',0,36),(2,'21',1,0),(2,'22',0,24),(2,'22',1,0),(2,'23',0,60),(2,'23',1,0),(2,'24',0,36),(2,'24',1,0),(2,'25',0,36),(2,'25',1,0),(2,'26',0,36),(2,'26',1,0),(2,'27',0,0),(2,'27',1,0),(2,'3',0,0),(2,'3',1,0),(2,'4',0,0),(2,'4',1,0),(2,'5',0,0),(2,'5',1,0),(2,'6',0,0),(2,'6',1,0),(2,'7',0,24),(2,'7',1,0),(2,'8',0,36),(2,'8',1,0),(2,'9',0,36),(2,'9',1,0),(3,'1',0,0),(3,'1',1,0),(3,'10',0,60),(3,'10',1,0),(3,'11',0,36),(3,'11',1,0),(3,'12',0,0),(3,'12',1,0),(3,'13',0,36),(3,'13',1,0),(3,'14',0,0),(3,'14',1,0),(3,'15',0,0),(3,'15',1,0),(3,'16',0,0),(3,'16',1,0),(3,'17',0,36),(3,'17',1,36),(3,'18',0,0),(3,'18',1,0),(3,'19',0,60),(3,'19',1,0),(3,'2',0,0),(3,'2',1,0),(3,'20',0,0),(3,'20',1,0),(3,'21',0,60),(3,'21',1,0),(3,'22',0,36),(3,'22',1,0),(3,'23',0,60),(3,'23',1,0),(3,'24',0,60),(3,'24',1,0),(3,'25',0,60),(3,'25',1,0),(3,'26',0,60),(3,'26',1,0),(3,'27',0,0),(3,'27',1,0),(3,'3',0,0),(3,'3',1,0),(3,'4',0,0),(3,'4',1,0),(3,'5',0,0),(3,'5',1,0),(3,'6',0,0),(3,'6',1,0),(3,'7',0,36),(3,'7',1,0),(3,'8',0,36),(3,'8',1,0),(3,'9',0,60),(3,'9',1,0),(4,'1',0,0),(4,'1',1,0),(4,'10',0,60),(4,'10',1,0),(4,'11',0,36),(4,'11',1,0),(4,'12',0,0),(4,'12',1,0),(4,'13',0,60),(4,'13',1,0),(4,'14',0,0),(4,'14',1,0),(4,'15',0,0),(4,'15',1,0),(4,'16',0,0),(4,'16',1,0),(4,'17',0,60),(4,'17',1,36),(4,'18',0,0),(4,'18',1,0),(4,'19',0,60),(4,'19',1,0),(4,'2',0,0),(4,'2',1,0),(4,'20',0,0),(4,'20',1,0),(4,'21',0,60),(4,'21',1,0),(4,'22',0,36),(4,'22',1,0),(4,'23',0,60),(4,'23',1,0),(4,'24',0,60),(4,'24',1,0),(4,'25',0,60),(4,'25',1,0),(4,'26',0,60),(4,'26',1,0),(4,'27',0,0),(4,'27',1,0),(4,'3',0,0),(4,'3',1,0),(4,'4',0,0),(4,'4',1,0),(4,'5',0,0),(4,'5',1,0),(4,'6',0,0),(4,'6',1,0),(4,'7',0,36),(4,'7',1,0),(4,'8',0,60),(4,'8',1,0),(4,'9',0,60),(4,'9',1,0),(5,'1',0,0),(5,'1',1,0),(5,'10',0,0),(5,'10',1,0),(5,'11',0,0),(5,'11',1,60),(5,'12',0,0),(5,'12',1,0),(5,'13',0,0),(5,'13',1,0),(5,'14',0,0),(5,'14',1,0),(5,'15',0,0),(5,'15',1,0),(5,'16',0,0),(5,'16',1,0),(5,'17',0,0),(5,'17',1,60),(5,'18',0,0),(5,'18',1,0),(5,'19',0,0),(5,'19',1,0),(5,'2',0,0),(5,'2',1,0),(5,'20',0,0),(5,'20',1,0),(5,'21',0,0),(5,'21',1,0),(5,'22',0,0),(5,'22',1,60),(5,'23',0,0),(5,'23',1,0),(5,'24',0,0),(5,'24',1,0),(5,'25',0,0),(5,'25',1,0),(5,'26',0,0),(5,'26',1,0),(5,'27',0,0),(5,'27',1,0),(5,'3',0,0),(5,'3',1,0),(5,'4',0,0),(5,'4',1,0),(5,'5',0,0),(5,'5',1,0),(5,'6',0,0),(5,'6',1,0),(5,'7',0,0),(5,'7',1,60),(5,'8',0,0),(5,'8',1,0),(5,'9',0,0),(5,'9',1,0);
/*!40000 ALTER TABLE `periodicite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `piecejointe`
--

DROP TABLE IF EXISTS `piecejointe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `piecejointe` (
  `ID_PIECEJOINTE` bigint(20) NOT NULL AUTO_INCREMENT,
  `NOM_PIECEJOINTE` varchar(255) NOT NULL,
  `EXTENSION_PIECEJOINTE` varchar(10) NOT NULL,
  `DESCRIPTION_PIECEJOINTE` text,
  `DATE_PIECEJOINTE` date DEFAULT NULL,
  PRIMARY KEY (`ID_PIECEJOINTE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `piecejointe`
--

LOCK TABLES `piecejointe` WRITE;
/*!40000 ALTER TABLE `piecejointe` DISABLE KEYS */;
/*!40000 ALTER TABLE `piecejointe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptionarticle`
--

DROP TABLE IF EXISTS `prescriptionarticle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptionarticle` (
  `ID_PRESCRIPTIONARTICLE` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_PRESCRIPTIONARTICLE` varchar(255) DEFAULT NULL,
  `NUM_PRESCRIPTIONARTICLE` int(11) DEFAULT NULL,
  `ID_PRESCRIPTIONTEXTE` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_PRESCRIPTIONARTICLE`),
  KEY `fk_prescriptionarticle_prescriptiontexte1_idx` (`ID_PRESCRIPTIONTEXTE`),
  CONSTRAINT `fk_prescriptionarticle_prescriptiontexte1` FOREIGN KEY (`ID_PRESCRIPTIONTEXTE`) REFERENCES `prescriptiontexte` (`ID_PRESCRIPTIONTEXTE`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptionarticle`
--

LOCK TABLES `prescriptionarticle` WRITE;
/*!40000 ALTER TABLE `prescriptionarticle` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptionarticle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptionarticleliste`
--

DROP TABLE IF EXISTS `prescriptionarticleliste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptionarticleliste` (
  `ID_ARTICLE` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_ARTICLE` varchar(255) NOT NULL,
  `VISIBLE_ARTICLE` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_ARTICLE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


INSERT INTO `prescriptionarticleliste` (`ID_ARTICLE`,`LIBELLE_ARTICLE`) VALUES (1,"");

--
-- Dumping data for table `prescriptionarticleliste`
--

LOCK TABLES `prescriptionarticleliste` WRITE;
/*!40000 ALTER TABLE `prescriptionarticleliste` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptionarticleliste` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptioncat`
--

DROP TABLE IF EXISTS `prescriptioncat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptioncat` (
  `ID_PRESCRIPTION_CAT` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_PRESCRIPTION_CAT` varchar(255) DEFAULT NULL,
  `NUM_PRESCRIPTION_CAT` int(11) NOT NULL,
  PRIMARY KEY (`ID_PRESCRIPTION_CAT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptioncat`
--

LOCK TABLES `prescriptioncat` WRITE;
/*!40000 ALTER TABLE `prescriptioncat` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptioncat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptiondossier`
--

DROP TABLE IF EXISTS `prescriptiondossier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptiondossier` (
  `ID_PRESCRIPTION_DOSSIER` bigint(20) NOT NULL AUTO_INCREMENT,
  `ID_DOSSIER` bigint(20) NOT NULL,
  `NUM_PRESCRIPTION_DOSSIER` int(11) NOT NULL,
  `ID_PRESCRIPTION_TYPE` bigint(20) DEFAULT NULL,
  `LIBELLE_PRESCRIPTION_DOSSIER` text,
  `TYPE_PRESCRIPTION_DOSSIER` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_PRESCRIPTION_DOSSIER`),
  UNIQUE KEY `ID_PRESCRIPTION_DOSSIER_UNIQUE` (`ID_PRESCRIPTION_DOSSIER`),
  KEY `fk_prescriptiondossier_dossier1_idx` (`ID_DOSSIER`),
  CONSTRAINT `fk_prescriptiondossier_dossier1` FOREIGN KEY (`ID_DOSSIER`) REFERENCES `dossier` (`ID_DOSSIER`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptiondossier`
--

LOCK TABLES `prescriptiondossier` WRITE;
/*!40000 ALTER TABLE `prescriptiondossier` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptiondossier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptiondossierassoc`
--

DROP TABLE IF EXISTS `prescriptiondossierassoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptiondossierassoc` (
  `NUM_PRESCRIPTION_DOSSIERASSOC` int(11) NOT NULL,
  `ID_PRESCRIPTION_DOSSIER` bigint(20) NOT NULL,
  `ID_TEXTE` bigint(20) DEFAULT NULL,
  `ID_ARTICLE` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`NUM_PRESCRIPTION_DOSSIERASSOC`,`ID_PRESCRIPTION_DOSSIER`),
  KEY `fk_prescriptiondossierassoc_prescriptiondossier1_idx` (`ID_PRESCRIPTION_DOSSIER`),
  CONSTRAINT `fk_prescriptiondossierassoc_prescriptiondossier1` FOREIGN KEY (`ID_PRESCRIPTION_DOSSIER`) REFERENCES `prescriptiondossier` (`ID_PRESCRIPTION_DOSSIER`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptiondossierassoc`
--

LOCK TABLES `prescriptiondossierassoc` WRITE;
/*!40000 ALTER TABLE `prescriptiondossierassoc` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptiondossierassoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptiontexte`
--

DROP TABLE IF EXISTS `prescriptiontexte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptiontexte` (
  `ID_PRESCRIPTIONTEXTE` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_PRESCRIPTIONTEXTE` varchar(255) DEFAULT NULL,
  `NUM_PRESCRIPTIONTEXTE` int(11) DEFAULT NULL,
  `ID_PRESCRIPTIONCAT` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_PRESCRIPTIONTEXTE`),
  KEY `fk_prescriptiontexte_prescriptioncat1_idx` (`ID_PRESCRIPTIONCAT`),
  CONSTRAINT `fk_prescriptiontexte_prescriptioncat1` FOREIGN KEY (`ID_PRESCRIPTIONCAT`) REFERENCES `prescriptioncat` (`ID_PRESCRIPTION_CAT`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptiontexte`
--

LOCK TABLES `prescriptiontexte` WRITE;
/*!40000 ALTER TABLE `prescriptiontexte` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptiontexte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptiontexteliste`
--

DROP TABLE IF EXISTS `prescriptiontexteliste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptiontexteliste` (
  `ID_TEXTE` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_TEXTE` varchar(255) NOT NULL,
  `VISIBLE_TEXTE` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_TEXTE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `prescriptiontexteliste` (`ID_TEXTE`,`LIBELLE_TEXTE`) VALUES (1,"");

--
-- Dumping data for table `prescriptiontexteliste`
--

LOCK TABLES `prescriptiontexteliste` WRITE;
/*!40000 ALTER TABLE `prescriptiontexteliste` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptiontexteliste` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptiontype`
--

DROP TABLE IF EXISTS `prescriptiontype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptiontype` (
  `ID_PRESCRIPTIONTYPE` bigint(20) NOT NULL AUTO_INCREMENT,
  `PRESCRIPTIONTYPE_CATEGORIE` bigint(20) DEFAULT NULL,
  `PRESCRIPTIONTYPE_TEXTE` bigint(20) DEFAULT NULL,
  `PRESCRIPTIONTYPE_ARTICLE` bigint(20) DEFAULT NULL,
  `PRESCRIPTIONTYPE_LIBELLE` text,
  `PRESCRIPTIONTYPE_NUM` int(3) DEFAULT 999,
  PRIMARY KEY (`ID_PRESCRIPTIONTYPE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptiontype`
--

LOCK TABLES `prescriptiontype` WRITE;
/*!40000 ALTER TABLE `prescriptiontype` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptiontype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptiontypeassoc`
--

DROP TABLE IF EXISTS `prescriptiontypeassoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescriptiontypeassoc` (
  `ID_PRESCRIPTIONTYPE` bigint(20) NOT NULL,
  `NUM_PRESCRIPTIONASSOC` int(11) NOT NULL,
  `ID_TEXTE` bigint(20) NOT NULL,
  `ID_ARTICLE` bigint(20) NOT NULL,
  PRIMARY KEY (`ID_PRESCRIPTIONTYPE`,`NUM_PRESCRIPTIONASSOC`),
  KEY `fk_prescriptiontypeassoc_prescriptiontype1_idx` (`ID_PRESCRIPTIONTYPE`),
  CONSTRAINT `fk_prescriptiontypeassoc_prescriptiontype1` FOREIGN KEY (`ID_PRESCRIPTIONTYPE`) REFERENCES `prescriptiontype` (`ID_PRESCRIPTIONTYPE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptiontypeassoc`
--

LOCK TABLES `prescriptiontypeassoc` WRITE;
/*!40000 ALTER TABLE `prescriptiontypeassoc` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptiontypeassoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `privileges`
--

DROP TABLE IF EXISTS `privileges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `privileges` (
  `id_privilege` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `text` varchar(45) DEFAULT NULL,
  `id_resource` bigint(19) unsigned NOT NULL,
  PRIMARY KEY (`id_privilege`),
  KEY `fk_privileges_resources1_idx` (`id_resource`),
  CONSTRAINT `fk_privileges_resources1` FOREIGN KEY (`id_resource`) REFERENCES `resources` (`id_resource`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `privileges`
--

LOCK TABLES `privileges` WRITE;
/*!40000 ALTER TABLE `privileges` DISABLE KEYS */;
INSERT INTO `privileges` VALUES (1,'docs','Gestion des documents',1),(2,'groupement_communes','Gestion des groupements de communes',1),(3,'gestion_prescriptions','Gestion des prescriptions',1),(4,'gestion_textes_applicables','Gestion des textes applicables',1),(5,'fil_actus','Écriture dans le fil d\'actualités',1),(6,'gestion_commissions','Gestion des commissions',1),(7,'lecture_commission','Lecture',2),(8,'ecriture_commission','Écriture',2),(9,'creation_commission','Création',2),(10,'modification_odj','Modification de l\'Ordre du Jour',2),(11,'admin','Accès à l\'administration',1),(12,'communes','Gestion des communes',1),(13,'periodicites','Gestion des périodicités',1),(14,'add_etablissement','Création d\'un établissement',3),(15,'add_dossier','Création d\'un dossier',3),(16,'generation_doc_com','Génération des documents de la commission',2),(17,'view_ets','Lecture',4),(18,'edit_ets','Modifier',4),(19,'view_ets','Lecture',5),(20,'edit_ets','Modifier',5),(21,'view_ets','Lecture',6),(22,'edit_ets','Modifier',6),(23,'view_ets','Lecture',7),(24,'edit_ets','Modifier',7),(25,'view_ets','Lecture',8),(26,'edit_ets','Modifier',8),(27,'view_doss','Lecture',9),(28,'edit_doss','Modifier',9),(29,'verrouillage_dossier','Verrouillage d\'un dossier',9);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(50,"edit_avis_com", "Modifier",50);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(51,"edit_statut", "Modifier",51);
INSERT INTO `privileges` VALUES 
(52,'view_ets','Lecture',52),
(53,'edit_ets','Modifier',52),
(54,'view_ets','Lecture',53),
(55,'edit_ets','Modifier',53),
(56,'view_ets','Lecture',54),
(57,'edit_ets','Modifier',54),
(58,'view_ets','Lecture',55),
(59,'edit_ets','Modifier',55),
(60,'alerte_statut','Changement de statut',56),
(61,'alerte_avis','Changement d''avis',56),
(62,'alerte_classement','Changement de classement',56),
(63,'search_ets','Etablissements',57),
(64,'search_dossiers','Dossiers',57);

INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(100,"view_ets_avis_defavorable", "Voir les établissements sous avis défavorable",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(101,"view_doss_sans_avis", "Voir dossiers de commissions échus sans avis",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(102,"view_ets_ouverts_sans_prochaine_vp", "Voir ets sans prochaine visite périodique",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(103,"view_courrier_sans_reponse", "Voir les courriers sans réponse",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(104,"view_ets_sans_preventionniste", "Voir ets sans préventionniste",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(105,"view_doss_avis_differe", "Voir dossiers avec avis différés",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(106,"view_ets_avis_defavorable_sur_commune", "Voir ets défavorable sur commune utilisateur",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(107,"view_ets_suivis", "Voir ets suivis",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(108,"view_doss_suivis_unlocked", "Voir les dossiers suivis déverrouillés",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(109,"view_doss_suivis_sans_avis", "Voir les dossiers suivis sans avis",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(110,"view_ets_avis_defavorable_suivis", "Voir les établissements défavorables suivis",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(111,"view_next_commissions", "Voir les prochaines commissions",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(112,"view_next_commissions_odj", "Voir les odj des prochaines commissions",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(113,"view_doss_levee_prescriptions", "Date de levée des prescriptions",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(114,"view_doss_absence_quorum", "Dossier avec absence de quorum",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(115,"view_doss_npsp", "Dossiers avec statut ne peut se prononcer",100);
/*!40000 ALTER TABLE `privileges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resources` (
  `id_resource` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id_resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resources`
--

LOCK TABLES `resources` WRITE;
/*!40000 ALTER TABLE `resources` DISABLE KEYS */;
INSERT INTO `resources` VALUES (1,'gestion_parametrages','Gestion et Paramétrages'),(2,'commission','Gestion des calendriers des commissions'),(3,'creations','Gestion des droits de création'),(4,'etablissement_erp_0_0_0_0_0','Établissement (Tous les types - Toutes les catégories - Ignorer les commissions - Ignorer les groupements - Ignorer la commune)'),(5,'etablissement_cell_0_0','Cellule (Tous les types - Toutes les catégories)'),(6,'etablissement_hab_0_0_0','Habitation (Toutes les familles - Ignorer les groupements - Ignorer la commune)'),(7,'etablissement_igh_0_0_0_0','IGH (Toutes les classes - Ignorer les commissions - Ignorer les groupements - Ignorer la commune)'),(8,'etablissement_eic_0_0','EIC (Ignorer les groupements - Ignorer la commune)'),(9,'dossier_0','Dossier (Toutes les natures)');
INSERT INTO `resources`(`id_resource`,`name`, `text`) VALUES(50,"avis_commission", "Gestion de l'avis de la commission");
INSERT INTO `resources`(`id_resource`,`name`, `text`) VALUES(51,"statut_etablissement", "Gestion du statut d'un établissement");
INSERT INTO `resources`(`id_resource`,`name`, `text`) VALUES(52,'etablissement_camp_0_0','Camping (Ignorer les groupements - Ignorer la commune)'),
(53,'etablissement_temp_0_0','Manifestation temporaire (Ignorer les groupements - Ignorer la commune)'),
(54,'etablissement_iop_0_0','IOP (Ignorer les groupements - Ignorer la commune)'),
(55,'etablissement_zone_0_0_0','Zone (Toutes les classes - Ignorer les groupements - Ignorer la commune)'),
(56,'alerte_email','Alertes'),
(57,'search','Recherche');
INSERT INTO `resources`(`id_resource`,`name`, `text`) VALUES(100,"dashboard", "Tableau de bord");
/*!40000 ALTER TABLE `resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statut`
--

DROP TABLE IF EXISTS `statut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statut` (
  `ID_STATUT` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_STATUT` varchar(30) NOT NULL,
  PRIMARY KEY (`ID_STATUT`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statut`
--

LOCK TABLES `statut` WRITE;
/*!40000 ALTER TABLE `statut` DISABLE KEYS */;
INSERT INTO `statut` VALUES (1,'Projet'),(2,'Ouvert'),(3,'Fermé'),(4,'Itinérant / Périodique');
/*!40000 ALTER TABLE `statut` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `textesappl`
--

DROP TABLE IF EXISTS `textesappl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `textesappl` (
  `ID_TEXTESAPPL` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_TEXTESAPPL` text,
  `VISIBLE_TEXTESAPPL` tinyint(4) DEFAULT NULL,
  `ID_TYPETEXTEAPPL` bigint(20) NOT NULL,
  `NUM_TEXTESAPPL` int(11) NOT NULL DEFAULT '99999',
  PRIMARY KEY (`ID_TEXTESAPPL`),
  KEY `fk_textesappl_typetextesappl1_idx` (`ID_TYPETEXTEAPPL`),
  CONSTRAINT `fk_textesappl_typetextesappl1` FOREIGN KEY (`ID_TYPETEXTEAPPL`) REFERENCES `typetextesappl` (`ID_TYPETEXTEAPPL`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `textesappl`
--

LOCK TABLES `textesappl` WRITE;
/*!40000 ALTER TABLE `textesappl` DISABLE KEYS */;
/*!40000 ALTER TABLE `textesappl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type` (
  `ID_TYPE` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_TYPE` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_TYPE`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type`
--

LOCK TABLES `type` WRITE;
/*!40000 ALTER TABLE `type` DISABLE KEYS */;
INSERT INTO `type` VALUES (1,'CTS'),(2,'EF'),(3,'EM'),(4,'EP'),(5,'GA'),(6,'GEEM'),(7,'J'),(8,'L'),(9,'M'),(10,'N'),(11,'O'),(12,'OA'),(13,'P'),(14,'PA'),(15,'PE2§2'),(16,'PS'),(17,'R'),(18,'REF'),(19,'S'),(20,'SG'),(21,'T'),(22,'U'),(23,'V'),(24,'W'),(25,'X'),(26,'Y'),(27,'Z');
/*!40000 ALTER TABLE `type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typeactivite`
--

DROP TABLE IF EXISTS `typeactivite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typeactivite` (
  `ID_TYPEACTIVITE` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_ACTIVITE` varchar(255) NOT NULL,
  `ID_TYPE` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID_TYPEACTIVITE`),
  KEY `fk_typeactivite_type1_idx` (`ID_TYPE`),
  CONSTRAINT `fk_typeactivite_type1` FOREIGN KEY (`ID_TYPE`) REFERENCES `type` (`ID_TYPE`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typeactivite`
--

LOCK TABLES `typeactivite` WRITE;
/*!40000 ALTER TABLE `typeactivite` DISABLE KEYS */;
INSERT INTO `typeactivite` VALUES (1,'Châpiteau',1),(2,'Structures',1),(3,'Tentes',1),(4,'Bateaux en stationnement sur les eaux intérieures',2),(5,'Bateaux stationnaires',2),(6,'Etablissements flottants',2),(7,'Gares',5),(8,'Etablissements d’enseignement avec internat pour jeunes handicapés ou inadaptés',7),(9,'Etablissements d’hébergement pour adultes handicapés',7),(10,'Etablissements médico-éducatifs avec internat pour jeunes handicapés ou inadaptés',7),(11,'Structures d’accueil pour personnes âgées',7),(12,'Structures d’accueil pour personnes handicapées',7),(13,'Cabarets',8),(14,'Cinéma',8),(15,'Cirques non forains',8),(16,'Salles d\'audition',8),(17,'Salle de conférences',8),(18,'Salles de pari',8),(19,'Salles de projection',8),(20,'Salles de quartier (ou assimilée)',8),(21,'Salles de réunions',8),(22,'Salles de spectacles',8),(23,'Salles multimédia',8),(24,'Salles polyvalentes à dominante sportive, dont la superficie unitaire est supérieure ou égale à 1 200 m2',8),(25,'Salles polyvalentes non visée par le Type X (salle polyvalente qui n’a pas une destination unique)',8),(26,'Salles réservées aux associations',8),(27,'Aires de vente',9),(28,'Boutiques',9),(29,'Centres commerciaux',9),(30,'Locaux de vente',9),(31,'Magasin de vente',9),(32,'Bars',10),(33,'Brasseries',10),(34,'Cafétaria',10),(35,'Cafés',10),(36,'Cantines',10),(37,'Débits de boissons',10),(38,'Restaurants',10),(39,'Hôtels',11),(40,'Motels',11),(41,'Pensions de famille',11),(42,'Hôtels-restaurants d’altitude',12),(43,'Bals',13),(46,'Salles de danse',13),(47,'Salles de jeux',13),(48,'Arènes',14),(49,'Hippodromes',14),(50,'Piscines',14),(51,'Pistes de patinage',14),(52,'Stades',14),(53,'Terrains de sport',14),(54,'Parcs de stationnement couverts',16),(55,'Auberges de jeunesse (comprenant au moins un local collectif à sommeil)',17),(56,'Auto-écoles',17),(57,'Centres aérés',17),(58,'Centres de loisirs (sans hébergement)',17),(59,'Centres de vacances',17),(60,'Colonies de vacances',17),(61,'Crèches',17),(62,'Ecoles maternelles',17),(63,'Etablissements d’enseignement',17),(64,'Etablissements de formation',17),(65,'Haltes-garderies',17),(66,'Internats des établissements de l\'enseignement primaire et secondaire',17),(67,'Jardins d\'enfant',17),(68,'Lycee public',17),(69,'Refuges de montagne',18),(70,'Bibliothèques',19),(71,'Centres de documentation et de consultation d’archives',19),(72,'Structures gonflables',20),(73,'Etablissements à vocation commerciale destinés à des expositions',21),(74,'Foires-expositions',21),(75,'Salles d’exposition à caractère permanent n’ayant pas une vocation de foire ou de salons ',21),(76,'Salles d’expositions de véhicules automobiles, bateaux, machines et autres volumineux biens d’équipements assimilables',21),(77,'Salons à caractère temporaire',21),(78,'Etablissements de cure thermale ou de thalassothérapie',22),(79,'Etablissements de santé publics ou privés dispensant des soins de courte durée en médecine, chirurgie, obstétrique',22),(80,'Etablissements de santé publics ou privés dispensant des soins de psychiatrie, de suite ou de réadaptation, des soins de longue durée, à des personnes n\'ayant pas leur autonomie de vie dont l\'état nécessite une surveillance médicale constante',22),(81,'Etablissements de thalassothérapie',22),(82,'Pouponnières',22),(83,'Eglises',23),(84,'Mosquées',23),(85,'Synagogues',23),(86,'Temples',23),(87,'Administrations',24),(88,'Banques',24),(89,'Bureaux',24),(90,'Hôtels de ville',24),(91,'Manèges',25),(92,'Patinoires',25),(93,'Piscines couvertes, transformables et mixtes',25),(94,'Salles d\'éducation physique et sportive',25),(95,'Salles omnisports',25),(96,'Salles polyvalentes à dominante sportive, dont l\'aire d\'activité est inférieure à 1200m² et la hauteur sous plafond supérieure ou égale à 6,50 mètres, etc',25),(97,'Salles sportives spécialisées',25),(98,'Musées',26),(99,'Salles destinées à recevoir des expositions à vocation culturelle, scientifique, technique, artistique, etc. ayant un caractère temporaire',26),(101,'Collège public',17),(103,'En attente de classement',27),(104,'Parc d\'attraction',14),(105,'Locaux à usage collectif d\'une surface unitaire supérieure à 50 mètres carrés des logements-foyers et de l\'habitat de loisirs à gestion collective',15),(106,'Bâtiments ou locaux à usage d\'hébergement qui ne relèvent d\'aucun type défini à l\'article GN 1 et qui permettent d\'accueillir plus de 15 et moins de 100 personnes n\'y élisant pas domicile',15),(107,'Hébergement de mineurs en dehors de leurs familles, le seuil de l\'effectif est fixé à 7 mineurs',15),(108,'Maisons d\'assistants maternels (MAM) dont les locaux accessibles au public sont strictement limités à un seul étage sur rez-de-chaussée et dont l\'effectif ne dépasse pas 16 enfants',15),(109,'Ecoles primaires',17),(110,'Lycee privé',17),(111,'Collège privé',17),(112,'Lycée agricole',17),(113,'Lycée maritime',17),(115, 'Parc de stationnement mixte', 16),(116, 'Parc de stationnement largement ventilé', 16),(117, 'Parc de stationnement à rangement automatisé', 16);
INSERT INTO `typeactivite` VALUES (NULL,'EM',3);
INSERT INTO `typeactivite` VALUES (NULL,'Établissements pénitentiaires',4);
INSERT INTO `typeactivite` VALUES (NULL,'Grands établissements à exploitation multiple',6);
/*!40000 ALTER TABLE `typeactivite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typeplan`
--

DROP TABLE IF EXISTS `typeplan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typeplan` (
  `ID_TYPEPLAN` int(11) NOT NULL AUTO_INCREMENT,
  `LIBELLE_TYPEPLAN` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_TYPEPLAN`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typeplan`
--

LOCK TABLES `typeplan` WRITE;
/*!40000 ALTER TABLE `typeplan` DISABLE KEYS */;
INSERT INTO `typeplan` VALUES (1,'Plan ER'),(2,'PPI'),(3,'POI'),(4,'PPMS'),(5,'Plan de sauvegarde des oeuvres');
/*!40000 ALTER TABLE `typeplan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typetextesappl`
--

DROP TABLE IF EXISTS `typetextesappl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typetextesappl` (
  `ID_TYPETEXTEAPPL` bigint(20) NOT NULL AUTO_INCREMENT,
  `LIBELLE_TYPETEXTEAPPL` text,
  PRIMARY KEY (`ID_TYPETEXTEAPPL`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typetextesappl`
--

LOCK TABLES `typetextesappl` WRITE;
/*!40000 ALTER TABLE `typetextesappl` DISABLE KEYS */;
INSERT INTO `typetextesappl` VALUES (1,'Dispositions générales'),(2,'Dispositions particulières'),(3,'Dispositions établissement de 5ème Cat'),(4,'Dispositions spéciales'),(5,'Textes relatifs aux bâtiments d’habitation'),(6,'Textes relatifs aux immeubles de grande hauteur');
/*!40000 ALTER TABLE `typetextesappl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur` (
  `ID_UTILISATEUR` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `USERNAME_UTILISATEUR` varchar(100) NOT NULL,
  `PASSWD_UTILISATEUR` varchar(32) DEFAULT NULL,
  `LASTACTION_UTILISATEUR` timestamp NULL DEFAULT NULL,
  `ACTIF_UTILISATEUR` tinyint(1) NOT NULL DEFAULT '1',
  `FAILED_LOGIN_ATTEMPTS_UTILISATEUR` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `IP_UTILISATEUR` VARCHAR(45) DEFAULT NULL,
  `NUMINSEE_COMMUNE` varchar(5) DEFAULT NULL,
  `ID_UTILISATEURINFORMATIONS` bigint(20) unsigned NOT NULL,
  `ID_GROUPE` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_UTILISATEUR`),
  KEY `fk_utilisateur_utilisateurinformations1_idx` (`ID_UTILISATEURINFORMATIONS`),
  KEY `fk_utilisateur_groupe1_idx` (`ID_GROUPE`),
  CONSTRAINT `fk_utilisateur_groupe1` FOREIGN KEY (`ID_GROUPE`) REFERENCES `groupe` (`ID_GROUPE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_utilisateur_utilisateurinformations1` FOREIGN KEY (`ID_UTILISATEURINFORMATIONS`) REFERENCES `utilisateurinformations` (`ID_UTILISATEURINFORMATIONS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'root','0ab182b5717693a278cd986898742e76',NULL,1,0,NULL,NULL,1,1);
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurcivilite`
--

DROP TABLE IF EXISTS `utilisateurcivilite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurcivilite` (
  `ID_UTILISATEURCIVILITE` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_UTILISATEURCIVILITE` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_UTILISATEURCIVILITE`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurcivilite`
--

LOCK TABLES `utilisateurcivilite` WRITE;
/*!40000 ALTER TABLE `utilisateurcivilite` DISABLE KEYS */;
INSERT INTO `utilisateurcivilite` VALUES (1,'Monsieur'),(2,'Madame');
/*!40000 ALTER TABLE `utilisateurcivilite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurcommission`
--

DROP TABLE IF EXISTS `utilisateurcommission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurcommission` (
  `ID_UTILISATEUR` bigint(20) unsigned NOT NULL,
  `ID_COMMISSION` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_UTILISATEUR`,`ID_COMMISSION`),
  KEY `fk_utilisateurcommission_utilisateur1_idx` (`ID_UTILISATEUR`),
  KEY `fk_utilisateurcommission_commission1_idx` (`ID_COMMISSION`),
  CONSTRAINT `fk_utilisateurcommission_commission1` FOREIGN KEY (`ID_COMMISSION`) REFERENCES `commission` (`ID_COMMISSION`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_utilisateurcommission_utilisateur1` FOREIGN KEY (`ID_UTILISATEUR`) REFERENCES `utilisateur` (`ID_UTILISATEUR`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurcommission`
--

LOCK TABLES `utilisateurcommission` WRITE;
/*!40000 ALTER TABLE `utilisateurcommission` DISABLE KEYS */;
/*!40000 ALTER TABLE `utilisateurcommission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurgroupement`
--

DROP TABLE IF EXISTS `utilisateurgroupement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurgroupement` (
  `ID_UTILISATEUR` bigint(20) unsigned NOT NULL,
  `ID_GROUPEMENT` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID_UTILISATEUR`,`ID_GROUPEMENT`),
  KEY `fk_utilisateurgroupement_utilisateur1_idx` (`ID_UTILISATEUR`),
  KEY `fk_utilisateurgroupement_groupement1_idx` (`ID_GROUPEMENT`),
  CONSTRAINT `fk_utilisateurgroupement_groupement1` FOREIGN KEY (`ID_GROUPEMENT`) REFERENCES `groupement` (`ID_GROUPEMENT`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_utilisateurgroupement_utilisateur1` FOREIGN KEY (`ID_UTILISATEUR`) REFERENCES `utilisateur` (`ID_UTILISATEUR`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurgroupement`
--

LOCK TABLES `utilisateurgroupement` WRITE;
/*!40000 ALTER TABLE `utilisateurgroupement` DISABLE KEYS */;
/*!40000 ALTER TABLE `utilisateurgroupement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurinformations`
--

DROP TABLE IF EXISTS `utilisateurinformations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurinformations` (
  `ID_UTILISATEURINFORMATIONS` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `NOM_UTILISATEURINFORMATIONS` varchar(50) DEFAULT NULL,
  `PRENOM_UTILISATEURINFORMATIONS` varchar(50) DEFAULT NULL,
  `TELFIXE_UTILISATEURINFORMATIONS` varchar(20) DEFAULT NULL,
  `TELPORTABLE_UTILISATEURINFORMATIONS` varchar(20) DEFAULT NULL,
  `TELFAX_UTILISATEURINFORMATIONS` varchar(20) DEFAULT NULL,
  `MAIL_UTILISATEURINFORMATIONS` varchar(50) DEFAULT NULL,
  `SOCIETE_UTILISATEURINFORMATIONS` varchar(100) DEFAULT NULL,
  `NUMEROADRESSE_UTILISATEURINFORMATIONS` varchar(10) DEFAULT NULL,
  `RUEADRESSE_UTILISATEURINFORMATIONS` varchar(255) DEFAULT NULL,
  `CPADRESSE_UTILISATEURINFORMATIONS` varchar(5) DEFAULT NULL,
  `VILLEADRESSE_UTILISATEURINFORMATIONS` varchar(255) DEFAULT NULL,
  `WEB_UTILISATEURINFORMATIONS` varchar(100) DEFAULT NULL,
  `OBS_UTILISATEURINFORMATIONS` text,
  `DATE_PRV2` timestamp NULL DEFAULT NULL,
  `DATE_RECYCLAGE` timestamp NULL DEFAULT NULL,
  `DATE_SID` timestamp NULL DEFAULT NULL,
  `ID_UTILISATEURCIVILITE` int(11) unsigned DEFAULT NULL,
  `ID_FONCTION` bigint(20) unsigned DEFAULT NULL,
  `GRADE_UTILISATEURINFORMATIONS` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`ID_UTILISATEURINFORMATIONS`),
  KEY `fk_utilisateurinformations_utilisateurcivilite1_idx` (`ID_UTILISATEURCIVILITE`),
  KEY `fk_utilisateurinformations_fonction1_idx` (`ID_FONCTION`),
  CONSTRAINT `fk_utilisateurinformations_fonction1` FOREIGN KEY (`ID_FONCTION`) REFERENCES `fonction` (`ID_FONCTION`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_utilisateurinformations_utilisateurcivilite1` FOREIGN KEY (`ID_UTILISATEURCIVILITE`) REFERENCES `utilisateurcivilite` (`ID_UTILISATEURCIVILITE`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurinformations`
--

LOCK TABLES `utilisateurinformations` WRITE;
/*!40000 ALTER TABLE `utilisateurinformations` DISABLE KEYS */;
INSERT INTO `utilisateurinformations` VALUES (1,'ROOT','ROOT',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,99,NULL);
/*!40000 ALTER TABLE `utilisateurinformations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

-- -----------------------------------------------------
-- Table `classement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `classement` ;

CREATE TABLE IF NOT EXISTS `classement` (
  `ID_CLASSEMENT` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LIBELLE_CLASSEMENT` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_CLASSEMENT`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

LOCK TABLES `classement` WRITE;
INSERT INTO `classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(1, "Artisanale");
INSERT INTO `classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(2, "Commerciale");
INSERT INTO `classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(3, "Industrielle");
INSERT INTO `classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(4, "Lotissement");
INSERT INTO `classement`(`ID_CLASSEMENT`, `LIBELLE_CLASSEMENT`) VALUES(5, "Autre");
UNLOCK TABLES;

-- -----------------------------------------------------
-- Table`etablissementclassement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `etablissementclassement`;

CREATE TABLE IF NOT EXISTS `etablissementclassement` (
  `ID_ETABLISSEMENT` BIGINT(20) UNSIGNED NOT NULL,
  `ID_CLASSEMENT` INT(11) UNSIGNED NOT NULL,
  INDEX `fk_etablissementclassement_classement1_idx` (`ID_CLASSEMENT` ASC),
  INDEX `fk_etablissementclassement_etablissement1_idx` (`ID_ETABLISSEMENT` ASC),
  PRIMARY KEY (`ID_ETABLISSEMENT`),
  CONSTRAINT `fk_etablissementclassement_classement1`
    FOREIGN KEY (`ID_CLASSEMENT`)
    REFERENCES `classement` (`ID_CLASSEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `etablissementclassement_etablissement1`
    FOREIGN KEY (`ID_ETABLISSEMENT`)
    REFERENCES `etablissement` (`ID_ETABLISSEMENT`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateurpreferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurpreferences` (
  `ID_UTILISATEURPREFERENCES` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ID_UTILISATEUR` bigint(20) unsigned NOT NULL,
  `DASHBOARD_BLOCS` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`ID_UTILISATEURPREFERENCES`),
  KEY `fk_utilisateurpreferences_utilisateur1_idx` (`ID_UTILISATEUR`),
  CONSTRAINT `fk_utilisateurpreferences_utilisateur1` FOREIGN KEY (`ID_UTILISATEUR`) REFERENCES `utilisateur` (`ID_UTILISATEUR`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


LOCK TABLES `utilisateurpreferences` WRITE;
/*!40000 ALTER TABLE `utilisateurpreferences` DISABLE KEYS */;
INSERT INTO `utilisateurpreferences` VALUES (1,1,NULL);
/*!40000 ALTER TABLE `utilisateurpreferences` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-21 20:53:01


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


-- -----------------------------------------------------
-- Table`cache` 
-- -----------------------------------------------------

CREATE TABLE `cache` (
  `ID_CACHE` varchar(250) NOT NULL,
  `VALUE_CACHE` text,
  `EXPIRE_CACHE` int,
  PRIMARY KEY (`ID_CACHE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table`changement` 
-- -----------------------------------------------------

DROP TABLE IF EXISTS `changement`;

CREATE TABLE `changement` (
  `ID_CHANGEMENT` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LIBELLE_CHANGEMENT` varchar(255) DEFAULT NULL,
  `MESSAGE_CHANGEMENT` text,
  PRIMARY KEY (`ID_CHANGEMENT`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `changement` WRITE;
INSERT INTO `changement` VALUES (1,'Changement de statut','<p>Bonjour,</p>\r\n<p>L\'&eacute;tablissement {etablissementNumeroId} {etablissementLibelle} est pass&eacute; au statut {etablissementStatut}.</p>\r\n<p>Bonne journ&eacute;e,</p>\r\n<p>Pr&eacute;varisc.</p>'),(2,'Changement d\'avis','<p>Bonjour,</p>\r\n<p>L\'&eacute;tablissement {etablissementNumeroId} {etablissementLibelle} est maintenant sous avis {etablissementAvis}.</p>\r\n<p>Bonne journ&eacute;e,</p>\r\n<p>Pr&eacute;varisc.</p>'),(3,'Changement de classement','<p>Bonjour,</p>\r\n<p>L\'&eacute;tablissement {etablissementNumeroId} {etablissementLibelle} est maintenant de cat&eacute;gorie {categorieEtablissement}, de type {typePrincipalEtablissement} - {activitePrincipaleEtablissement}.</p>\r\n<p>Bonne journ&eacute;e,</p>\r\n<p>Pr&eacute;varisc.</p>');
UNLOCK TABLES;

-- -----------------------------------------------------
-- View`etablissementinformationsactuel` 
-- -----------------------------------------------------

CREATE VIEW etablissementinformationsactuel AS 
SELECT * FROM `etablissementinformations` ei WHERE ei.DATE_ETABLISSEMENTINFORMATIONS = 
( 
SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations 
WHERE etablissementinformations.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT 
);


-- -----------------------------------------------------
-- View `etablissementinformationsactuel` 
-- -----------------------------------------------------

DROP VIEW IF EXISTS `etablissementinformationsactuel`;

CREATE VIEW `etablissementinformationsactuel` AS 
SELECT * FROM `etablissementinformations` ei WHERE ei.DATE_ETABLISSEMENTINFORMATIONS = 
( 
    SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations 
    WHERE etablissementinformations.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT 
);


-- -----------------------------------------------------
-- View `dossierdernierevisite` 
-- -----------------------------------------------------

DROP VIEW IF EXISTS `dossierdernierevisite`;

CREATE VIEW `dossierdernierevisite` AS 
SELECT 
    ed.ID_ETABLISSEMENT,
    MAX(d.DATEVISITE_DOSSIER) as DATEVISITE_DOSSIER,
    DATE_ADD(MAX(d.DATEVISITE_DOSSIER), INTERVAL ei.PERIODICITE_ETABLISSEMENTINFORMATIONS MONTH) as DATEPROCHAINEVISITE_DOSSIER
FROM dossier d
    INNER JOIN dossiernature n on d.ID_DOSSIER = n.ID_DOSSIER
    INNER JOIN etablissementdossier ed on ed.ID_DOSSIER = d.ID_DOSSIER
    INNER JOIN etablissementinformationsactuel ei on ei.ID_ETABLISSEMENT = ed.ID_ETABLISSEMENT
WHERE 
    n.ID_NATURE IN(26,21,47,48)
    AND d.AVIS_DOSSIER_COMMISSION IS NOT NULL
    AND d.AVIS_DOSSIER_COMMISSION > 0
GROUP BY ed.ID_ETABLISSEMENT;