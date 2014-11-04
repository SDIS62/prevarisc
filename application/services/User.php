<?php

class Service_User
{
    /**
     * Récupération d'un utilisateur
     *
     * @param int $id_user
     * @return array
     */
    public function find($id_user)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        if(($user = unserialize($cache->load('user_id_' . $id_user))) === false) {

            $model_user = new Model_DbTable_Utilisateur;
            $model_userinformations = new Model_DbTable_UtilisateurInformations;
            $model_groupe = new Model_DbTable_Groupe;
            $model_fonction = new Model_DbTable_Fonction;

            $user = $model_user->find($id_user)->current()->toArray();
            $user = array_merge($user, array('uid' => $user['ID_UTILISATEUR']));
            $user = array_merge($user, array('infos' => $model_userinformations->find($user['ID_UTILISATEURINFORMATIONS'])->current()->toArray()));
            $user = array_merge($user, array('group' => $model_groupe->find($user['ID_GROUPE'])->current()->toArray()));
            $user = array_merge($user, array('groupements' => $model_user->getGroupements($user['ID_UTILISATEUR']) == null ? null : $model_user->getGroupements($user['ID_UTILISATEUR'])->toArray()));
            $user = array_merge($user, array('commissions' => $model_user->getCommissions($user['ID_UTILISATEUR']) == null ? null : $model_user->getCommissions($user['ID_UTILISATEUR'])->toArray()));
            $user['infos'] = array_merge($user['infos'], array('LIBELLE_FONCTION' => $model_fonction->find($user['infos']['ID_FONCTION'])->current()->toArray()['LIBELLE_FONCTION']));


            $cache->save(serialize($user));
        }

        return $user;
    }

    /**
     * Récupération des données du tableau de bord utilisateur
     *
     * @param int $id_user
     * @return array
     */
    public function getDashboardData($id_user)
    {
        // Récupération des ACL
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $acl = unserialize($cache->load('acl'));

        // Récupération de l'utilisateur & son profil
        $user = $this->find($id_user);
        $profil = Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'];

        $etablissements =
                $commissions =
                $dossiers =
                $erpSansPreventionniste =
                $etablissementAvisDefavorable =
                $listeDesDossierDateCommissionEchu =
                $listeDesCourrierSansReponse =
                $prochainesCommission =
                $NbrDossiersAffect =
                $commissionsUser =
                $listeErpOuvertSansProchainesVisitePeriodiques = array();

        $dbDateCommission = new Model_DbTable_DateCommission;
        $dbEtablissement = new Model_DbTable_Etablissement;
        $dbDossier = new Model_DbTable_Dossier ;
        $dbDossierAffectation = new Model_DbTable_DossierAffectation;
        $service_etablissement = new Service_Etablissement;

        foreach($user['commissions'] as $commission) {
            $commissionsUser[] = $commission["ID_COMMISSION"];
        }

        if ($acl->isAllowed($profil, "commission", "lecture_commission")) {
            $days = getenv('PREVARISC_DASHBOARD_NEXT_COMMISSIONS_DAYS') ? (int) getenv('PREVARISC_DASHBOARD_NEXT_COMMISSIONS_DAYS')  : 15;
            $prochainesCommission = $dbDateCommission->getNextCommission($commissionsUser, time(), time() + 3600 * 24 * $days);
        }

        if ($acl->isAllowed($profil, "dashboard", "view_ets_avis_defavorable")) {
            $etablissementAvisDefavorable = $dbEtablissement->listeDesERPOuvertsSousAvisDefavorable($commissionsUser);
        }

        if ($acl->isAllowed($profil, "dashboard", "view_doss_sans_avis")) {
            $days = getenv('PREVARISC_DASHBOARD_DOSSIERS_SANS_AVIS_DAYS') ? (int) getenv('PREVARISC_DASHBOARD_DOSSIERS_SANS_AVIS_DAYS')  : 15;
            $listeDesDossierDateCommissionEchu = $dbDossier->listeDesDossierDateCommissionEchu($commissionsUser, $days);
        }

        //Liste des Erp sans commission périodique alors que c'est ouvert
        if ($acl->isAllowed($profil, "dashboard", "view_ets_ouverts_sans_prochaine_vp")) {
            $listeErpOuvertSansProchainesVisitePeriodiques = $dbEtablissement->listeErpOuvertsSansProchainesVisitePeriodiques($commissionsUser);
        }

         // Courriers sans réponse depuis N jours
        if ($acl->isAllowed($profil, "dashboard", "view_courrier_sans_reponse")) {
            $days = getenv('PREVARISC_DASHBOARD_COURRIER_SANS_REPONSE_DAYS') ? (int) getenv('PREVARISC_DASHBOARD_COURRIER_SANS_REPONSE_DAYS')  : 10;
            $listeDesCourrierSansReponse = $dbDossier->listeDesCourrierSansReponse($days);
        }

        // Etablissements Sans Preventionniste
        if ($acl->isAllowed($profil, "dashboard", "view_ets_sans_preventionniste")) {
            $erpSansPreventionniste = $dbEtablissement->listeERPSansPreventionniste();
        }

        // Dossiers avec avis différé
        if ($acl->isAllowed($profil, "dashboard", "view_doss_avis_differe")) {
            $dossiers = array_merge($dbDossier->listeDossierAvecAvisDiffere($commissionsUser), $dossiers);
        }

        // Etablissements sous avis défavorables sur la commune de l'utilisateur
        if($user["NUMINSEE_COMMUNE"] != null) {
            if ($acl->isAllowed($profil, "dashboard", "view_ets_avis_defavorable_sur_commune")) {
                $etablissementAvisDefavorable = array_merge($dbEtablissement->listeDesERPOuvertsSousAvisDefavorableSurCommune($user["NUMINSEE_COMMUNE"] ), $etablissementAvisDefavorable);
            }
        }

        // on récupère pour chaque prochaine commission le nombre de dossiers affectés
        foreach($prochainesCommission as $commissiondujour)
        {
            //Si on prend en compte les heures on récupère uniquement les dossiers n'ayant pas d'heure de passage
            $listeDossiersAffect = $dbDossierAffectation->getListDossierAffect($commissiondujour['ID_DATECOMMISSION']);
            $NbrDossiersAffect[$commissiondujour['ID_DATECOMMISSION']] = array(
                'total' => 0,
                'verrouilles' => 0,
            );

            foreach($listeDossiersAffect as $ue)
            {
                $NbrDossiersAffect[$commissiondujour['ID_DATECOMMISSION']]['total']++;
                if ($ue['VERROU_DOSSIER'] == 1) {
                    $NbrDossiersAffect[$commissiondujour['ID_DATECOMMISSION']]['verrouilles']++;
                }
           }


           // En doublons avec la partie précédente, à harmoniser
           $listeDossiersNonAffect = $dbDossierAffectation->getDossierNonAffect($commissiondujour["ID_DATECOMMISSION"]);
           $listeDossiersAffect = $dbDossierAffectation->getDossierAffect($commissiondujour["ID_DATECOMMISSION"]);
           $odj = array_merge($listeDossiersNonAffect, $listeDossiersAffect);
           $odj = array_unique($odj, SORT_REGULAR);
           $dateFormatter = new DateTime($commissiondujour["DATE_COMMISSION"]);
           $commissions[] = array(
               "id"   => $commissiondujour['ID_DATECOMMISSION'],
               "name" => $commissiondujour["LIBELLE_COMMISSION"] . ' - ' . $commissiondujour['LIBELLE_DATECOMMISSION'],
               "date" => $dateFormatter->format('d/m/Y'),
               "heure" => substr($commissiondujour["HEUREDEB_COMMISSION"], 0, 5) . ' - ' . substr($commissiondujour["HEUREFIN_COMMISSION"], 0, 5),
               "odj" => $odj,
           );
        }

        // Etablissements suivis
        if ($acl->isAllowed($profil, "dashboard", "view_ets_suivis")) {

            // Ets 1 - 4ème catégorie
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("etablissementinformations.ID_STATUT", array('2', '4'));
            $search->sup("etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS", 0);
            $search->setCriteria("etablissementinformations.ID_CATEGORIE", array("1","2","3","4"));
            $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

            // 5ème catégorie defavorable
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("etablissementinformations.ID_STATUT", array('2', '4'));
            $search->sup("etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS", 0);
            $search->setCriteria("etablissementinformations.ID_CATEGORIE", "5");
            $search->setCriteria("avis.ID_AVIS", 2);
            $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

            // 5ème catégorie avec local à sommeil
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("etablissementinformations.ID_STATUT", array('2', '4'));
            $search->sup("etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS", 0);
            $search->setCriteria("etablissementinformations.ID_CATEGORIE", "5");
            $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            $search->setCriteria("etablissementinformations.LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS", "1");
            $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

            // EIC - IGH - HAB - Autres
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("etablissementinformations.ID_STATUT", array('2', '4'));
            $search->sup("etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS", 0);
            $search->setCriteria("etablissementinformations.ID_GENRE", array("6","5","4", '7', '8', '9', '10'));
            $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

            $etablissements = array_unique($etablissements, SORT_REGULAR);

            // Dossiers avec avis différé
            if ($acl->isAllowed($profil, "dashboard", "view_doss_avis_differe")) {
                $dossiers = array();
                foreach($etablissements as $etablissement) {
                  $dossiers_ets = $service_etablissement->getDossiers($etablissement['ID_ETABLISSEMENT']);
                  $dossiers_merged = $dossiers_ets['etudes'];
                  $dossiers_merged = array_merge($dossiers_merged, $dossiers_ets['visites']);
                  $dossiers_merged = array_merge($dossiers_merged, $dossiers_ets['autres']);
                  foreach($dossiers_merged as $dossier_ets) {
                    if($dossier_ets['DIFFEREAVIS_DOSSIER'] == 1) {
                      $dossiers[] = $dossier_ets;
                    }
                  }
                }
            }
        }

        return array(
          'etablissements' => $etablissements,
          'dossiers' => $dossiers,
          'commissions' => $commissions,
          'erpSansPreventionniste' => $erpSansPreventionniste,
          'etablissementAvisDefavorable' => $etablissementAvisDefavorable,
          'dossierCommissionEchu' => $listeDesDossierDateCommissionEchu,
          'CourrierSansReponse' => $listeDesCourrierSansReponse,
          'prochainesCommission' => $prochainesCommission,
          'NbrDossiersAffect' =>  $NbrDossiersAffect,
          'ErpSansProchaineVisitePeriodeOuvert'=>$listeErpOuvertSansProchainesVisitePeriodiques
        );
    }

    /**
     * Récupération d'un utilisateur via son nom d'utilisateur
     *
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username)
    {
        $model_user = new Model_DbTable_Utilisateur;
        $user = $model_user->fetchRow($model_user->select()->where('USERNAME_UTILISATEUR = ?', $username));
        return $user !== null ? $this->find($user->ID_UTILISATEUR) : null;
    }

    /**
     * Mise à jour de la dernière action de l'utilisateur
     *
     * @param int $id_user
     * @param string $last_action_date Par défaut date("Y:m-d H:i:s")
     */
    public function updateLastActionDate($id_user, $last_action_date = '')
    {
        $model_user = new Model_DbTable_Utilisateur;
        $user = $model_user->find($id_user)->current();
        $user->LASTACTION_UTILISATEUR = $last_action_date == '' ? date("Y:m-d H:i:s") : $last_action_date;
        $user->save();
    }

    /**
     * Récupération des groupes d'utilisateurs
     *
     * @return array
     */
    public function getAllGroupes()
    {
        $DB_groupe = new Model_DbTable_Groupe;
        return $DB_groupe->fetchAll()->toArray();
    }

    /**
     * Récupération de toutes les fonctions des utilisateurs
     *
     * @return array
     */
    public function getAllFonctions()
    {
        $DB_fonction = new Model_DbTable_Fonction();
        return $DB_fonction->fetchAll()->toArray();
    }

    /**
     * Sauvegarde d'un utilisateur
     *
     * @param array $data
     * @param array $avatar optionnel
     * @param int $id_user optionnel
     * @return int Id de l'utilisateur édité
     */
    public function save($data, $avatar = null, $id_user = null)
    {
        $DB_user = new Model_DbTable_Utilisateur;
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $DB_groupementsUser = new Model_DbTable_UtilisateurGroupement;
        $DB_commissionsUser = new Model_DbTable_UtilisateurCommission;

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $user = $id_user == null ? $DB_user->createRow() : $DB_user->find($id_user)->current();
            $informations = $id_user == null ? $DB_informations->createRow() : $DB_informations->find($user->ID_UTILISATEURINFORMATIONS)->current();

            $informations->NOM_UTILISATEURINFORMATIONS = $data['NOM_UTILISATEURINFORMATIONS'];
            $informations->PRENOM_UTILISATEURINFORMATIONS = $data['PRENOM_UTILISATEURINFORMATIONS'];
            $informations->GRADE_UTILISATEURINFORMATIONS = $data['GRADE_UTILISATEURINFORMATIONS'];
            $informations->TELFIXE_UTILISATEURINFORMATIONS = $data['TELFIXE_UTILISATEURINFORMATIONS'];
            $informations->TELPORTABLE_UTILISATEURINFORMATIONS = $data['TELPORTABLE_UTILISATEURINFORMATIONS'];
            $informations->TELFAX_UTILISATEURINFORMATIONS = $data['TELFAX_UTILISATEURINFORMATIONS'];
            $informations->MAIL_UTILISATEURINFORMATIONS = $data['MAIL_UTILISATEURINFORMATIONS'];
            $informations->WEB_UTILISATEURINFORMATIONS = $data['WEB_UTILISATEURINFORMATIONS'];
            $informations->OBS_UTILISATEURINFORMATIONS = $data['OBS_UTILISATEURINFORMATIONS'];
            $informations->ID_FONCTION = $data['ID_FONCTION'];

            $informations->save();

            $user->USERNAME_UTILISATEUR = $data['USERNAME_UTILISATEUR'];
            $user->NUMINSEE_COMMUNE = array_key_exists('NUMINSEE_COMMUNE', $data) ? $data['NUMINSEE_COMMUNE'] : null;
            $user->ID_GROUPE = $data['ID_GROUPE'];
            $user->ACTIF_UTILISATEUR = $data['ACTIF_UTILISATEUR'];
            $user->ID_UTILISATEURINFORMATIONS = $informations->ID_UTILISATEURINFORMATIONS;

            if(array_key_exists('PASSWD_INPUT', $data)) {
                $user->PASSWD_UTILISATEUR = $data['PASSWD_INPUT'] == '' ? null : md5($user->USERNAME_UTILISATEUR . getenv('PREVARISC_SECURITY_SALT') . $data['PASSWD_INPUT']);
            }

            $user->save();

            $DB_groupementsUser->delete("ID_UTILISATEUR = " . $user->ID_UTILISATEUR);
            $DB_commissionsUser->delete("ID_UTILISATEUR = " . $user->ID_UTILISATEUR);

            if(array_key_exists('commissions', $data)) {
                foreach($data["commissions"] as $id) {
                    $row = $DB_commissionsUser->createRow();
                    $row->ID_UTILISATEUR = $user->ID_UTILISATEUR;
                    $row->ID_COMMISSION = $id;
                    $row->save();
                }
            }

            if(array_key_exists('groupements', $data)) {
                foreach($data["groupements"] as $id) {
                    $row = $DB_groupementsUser->createRow();
                    $row->ID_UTILISATEUR = $user->ID_UTILISATEUR;
                    $row->ID_GROUPEMENT = $id;
                    $row->save();
                }
            }

            Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch')->clean(Zend_Cache::CLEANING_MODE_ALL);
            $db->commit();

            $cache->remove('user_id_' . $user->ID_UTILISATEUR);

            // Gestion de l'avatar
            if($avatar !== null) {

                $path = REAL_DATA_PATH . DS . 'uploads' . DS . 'avatars' . DS;
                $extension = strtolower(strrchr($avatar['name'], "."));

                GD_Resize::run($avatar["tmp_name"], $path . "small" . DS . $user->ID_UTILISATEUR . ".jpg", 25, 25);
                GD_Resize::run($avatar["tmp_name"], $path . "medium" . DS . $user->ID_UTILISATEUR . ".jpg", 150);
                GD_Resize::run($avatar["tmp_name"], $path . "large" . DS . $user->ID_UTILISATEUR . ".jpg", 224);
            }

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $user->ID_UTILISATEUR;
    }

    /**
     * Récupération d'un groupe
     *
     * @param int $id_group
     * @return array
     */
    public function getGroup($id_group)
    {
        $model_groupe = new Model_DbTable_Groupe;
        return $model_groupe->find($id_group)->current()->toArray();
    }

    /**
     * Sauvegarde d'un groupe
     *
     * @param array $data
     * @param int $id_group Optionnel
     * @return int
     */
    public function saveGroup($data, $id_group = null)
    {
        $model_groupe = new Model_DbTable_Groupe;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $group = $id_group == null ? $model_groupe->createRow() : $model_groupe->find($id_group)->current();
            $group->LIBELLE_GROUPE = $data['LIBELLE_GROUPE'];
            $group->DESC_GROUPE = $data['DESC_GROUPE'];
            $group->save();
            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $group->ID_GROUPE;
    }

    /**
     * Suppression d'un groupe
     *
     * @param int $id_group
     */
    public function deleteGroup($id_group)
    {
        $DB_user = new Model_DbTable_Utilisateur;
        $DB_groupe = new Model_DbTable_Groupe;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        if ($id_group > 1) {

            try {
                // récupération de tous les utilisateurs du groupe à supprimer
                $all_users = $DB_user->fetchAll("ID_GROUPE = " . $id_group);

                // On bouge les users du groupe à supprimer dans le groupe par défaut
                if ($all_users != null) {
                    foreach ($all_users as $item) {
                        $user = $DB_user->find( $item->ID_UTILISATEUR )->current();
                        $user->ID_GROUPE = 1;
                        $user->save();
                    }
                }

                $DB_groupe->delete($id_group);

                $db->commit();

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }
}
