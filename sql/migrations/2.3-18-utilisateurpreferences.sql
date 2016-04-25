SET NAMES 'utf8';

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

INSERT INTO utilisateurpreferences(ID_UTILISATEURPREFERENCES, ID_UTILISATEUR, DASHBOARD_BLOCS)
SELECT NULL, ID_UTILISATEUR, NULL FROM utilisateur;