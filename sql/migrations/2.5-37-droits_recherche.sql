set names 'utf8';

INSERT INTO `resources`(`id_resource`,`name`, `text`) VALUES (57,'search','Recherche');

INSERT INTO `privileges`(`id_privilege`,`name`, `text`,`id_resource`) VALUES 
(63,'search_ets','Etablissements',57),
(64,'search_dossiers','Dossiers',57);

INSERT INTO `groupe-privileges`(ID_GROUPE, id_privilege) select id_groupe,63 from groupe;
INSERT INTO `groupe-privileges`(ID_GROUPE, id_privilege) select id_groupe,64 from groupe;
