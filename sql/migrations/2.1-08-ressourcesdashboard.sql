SET NAMES 'utf8';

INSERT INTO `resources`(`id_resource`,`name`, `text`) VALUES(100,"dashboard", "Tableau de bord");

INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(100,"view_ets_avis_defavorable", "Voir les établissements sous avis défavorable",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(101,"view_doss_sans_avis", "Voir les établissements sous avis défavorable",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(102,"view_ets_ouverts_sans_prochaine_vp", "Voir les établissements sous avis défavorable",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(103,"view_courrier_sans_reponse", "Voir les établissements sous avis défavorable",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(104,"view_ets_sans_preventionniste", "Voir les établissements sous avis défavorable",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(105,"view_doss_avis_differe", "Voir les établissements sous avis défavorable",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(106,"view_ets_avis_defavorable_sur_commune", "Voir les établissements sous avis défavorable",100);
INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES(107,"view_ets_suivis", "Voir les établissements sous avis défavorable",100);