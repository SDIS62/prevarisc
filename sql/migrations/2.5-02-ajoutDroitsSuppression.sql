set names 'utf8';

#Ajout de la resource de suppression
INSERT INTO PRV_prevarisc_v2.resources (id_resource, name, text) VALUES (60, 'suppression', 'Suppression');

#Ajout des privileges de suppression
INSERT INTO PRV_prevarisc_v2.privileges (id_privilege, name, text, id_resource) VALUES (70, 'delete_dossier', 'Suppression des dossiers', 60);
INSERT INTO PRV_prevarisc_v2.privileges (id_privilege, name, text, id_resource) VALUES (71, 'delete_etablissement', 'Suppression des établissements', 60);

#Ajout flag dossier pour suppression
ALTER TABLE PRV_prevarisc_v2.dossier ADD DATESUPPRESSION_DOSSIER DATE;

#Ajout flag etablissement pour suppression
ALTER TABLE PRV_prevarisc_v2.etablissement ADD DATESUPPRESSION_ETABLISSEMENT DATE;
