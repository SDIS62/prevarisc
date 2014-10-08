SET NAMES 'utf8';
use PRV_prevarisc_v2;

INSERT INTO `PRV_prevarisc_v2`.`resources`(`id_resource`,`name`, `text`) VALUES(50,"avis_commission", "Gestion de l'avis de la commission");
INSERT INTO `PRV_prevarisc_v2`.`privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(50,"edit_avis_com", "Modifier",50);

INSERT INTO `PRV_prevarisc_v2`.`resources`(`id_resource`,`name`, `text`) VALUES(51,"statut_etablissement", "Gestion du statut d'un établissement");
INSERT INTO `PRV_prevarisc_v2`.`privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(51,"edit_statut", "Modifier",51);