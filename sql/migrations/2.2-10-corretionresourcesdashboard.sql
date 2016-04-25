SET NAMES 'utf8';

UPDATE `privileges` SET text= "Voir dossiers de commissions échus sans avis" WHERE id_privilege = 101; 
UPDATE `privileges` SET text= "Voir ets sans prochaine visite périodique" WHERE id_privilege = 102; 
UPDATE `privileges` SET text= "Voir les courriers sans réponse" WHERE id_privilege = 103; 
UPDATE `privileges` SET text= "Voir ets sans préventionniste" WHERE id_privilege = 104; 
UPDATE `privileges` SET text= "Voir dossiers avec avis différés" WHERE id_privilege = 105; 
UPDATE `privileges` SET text= "Voir ets défavorable sur commune utilisateur" WHERE id_privilege = 106; 
UPDATE `privileges` SET text= "Voir ets suivis" WHERE id_privilege = 107;