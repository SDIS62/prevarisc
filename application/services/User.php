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
        // Récupération de l'utilisateur & son profil
        $user = $this->find($id_user);
        $profil = $user['infos']['LIBELLE_FONCTION'];

        $etablissements = $commissions = $dossiers = $erpSansPreventionniste = $etablissementAvisDefavorable = $listeDesDossierDateCommissionEchu = $listeDesCourrierSansReponse = $prochainesCommission = $NbrDossiersAffect = $listeErpOuvertSansProchainesVisitePeriodiques = array();

        $dateCommission = new Model_DbTable_DateCommission;
        $prochainesCommission = $dateCommission->getNextCommission(time(), time() + 3600 * 24 * 15);
        $dbEtablissement = new Model_DbTable_Etablissement;
        $etablissementAvisDefavorable = $dbEtablissement->listeDesERPSousAvisDefavorable();
        $dbDossier = new Model_DbTable_Dossier ;
        $listeDesDossierDateCommissionEchu = $dbDossier->listeDesDossierDateCommissionEchu();
        $dbDossierAffectation = new Model_DbTable_DossierAffectation;
        foreach($prochainesCommission as $commissiondujour)
        {
            //Si on prend en compte les heures on récupère uniquement les dossiers n'ayant pas d'heure de passage
            $listeDossiersAffect = $dbDossierAffectation->getListDossierAffect($commissiondujour['ID_DATECOMMISSION']);
            $dbDossier = new Model_DbTable_Dossier;
            $service_etablissement = new Service_Etablissement;
            $nbrdossier=0;
            $NbrDossiersAffect[$commissiondujour['ID_DATECOMMISSION']] = 0;
                foreach($listeDossiersAffect as $val => $ue)
                {
                    //On recupere la liste des établissements qui concernent le dossier
                    $listeEtab = $dbDossier->getEtablissementDossierGenConvoc($ue['ID_DOSSIER']);
                     //on recupere la liste des infos des établissement
                    if($nbrdossier == 0)
                    {
                       $NbrDossiersAffect[$commissiondujour['ID_DATECOMMISSION']] = 0;
                    }
                    if(count($listeEtab) > 0)
                    {  $nbrdossier++;
                       $NbrDossiersAffect[$commissiondujour['ID_DATECOMMISSION']] = $nbrdossier ;
                    }
               }
        }

        //Liste des Erp sans commission périodique alors que c'est ouvert
         $listeErpOuvertSansProchainesVisitePeriodiques = $dbEtablissement->listeErpOuvertSansProchainesVisitePeriodiques();



        // Définition des données types par profil
        switch($profil) {
          case 'Secrétariat':
            $listeDesCourrierSansReponse = $dbDossier->listeDesCourrierSansReponse(5);
            if(count($user['commissions']) > 0) {
              $dbDossierAffectation = new Model_DbTable_DossierAffectation;
              $dbDateCommission = new Model_DbTable_DateCommission;

              foreach($user['commissions'] as $commission) {
                // Dossiers avec avis différé
                $search = new Model_DbTable_Search;
                $search->setItem("dossier");
                $search->setCriteria("d.COMMISSION_DOSSIER", $commission["ID_COMMISSION"]);
                $search->setCriteria("d.DIFFEREAVIS_DOSSIER", 1);
                $dossiers = array_merge($search->run(false, null, false)->toArray(), $dossiers);

                // Récupération de l'ordre du jour des 3 prochaines commissions
                foreach($dbDateCommission->fetchAll("COMMISSION_CONCERNE = '" . $commission["ID_COMMISSION"] . "' AND DATE_COMMISSION >= NOW()", "DATE_COMMISSION ASC", 3, 0)->toArray() as $date) {
                  $listeDossiersNonAffect = $dbDossierAffectation->getDossierNonAffect($date["ID_DATECOMMISSION"]);
                  $listeDossiersAffect = $dbDossierAffectation->getDossierAffect($date["ID_DATECOMMISSION"]);
                  $odj = array_merge($listeDossiersNonAffect, $listeDossiersAffect);
                  $odj = array_unique($odj, SORT_REGULAR);
                  $commissions[] = array("name" => $commission["LIBELLE_COMMISSION"] . ' - ' . $date['LIBELLE_DATECOMMISSION'], "date" => $date["DATE_COMMISSION"], "heure" => $date["HEUREDEB_COMMISSION"] . ' - ' . $date["HEUREFIN_COMMISSION"], "odj" => $odj);
                }
              }
            }

            break;

          case 'Préfet':
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("avis.ID_AVIS", 2);
            $etablissements = $search->run(false, null, false)->toArray();

            if(count($user['commissions']) > 0) {

              $dbDossierAffectation = new Model_DbTable_DossierAffectation;
              $dbDateCommission = new Model_DbTable_DateCommission;

              foreach($user['commissions'] as $commission) {
                // Récupération de l'ordre du jour des 3 prochaines commissions
                foreach($dbDateCommission->fetchAll("COMMISSION_CONCERNE = '" . $commission["ID_COMMISSION"] . "' AND DATE_COMMISSION >= NOW()", "DATE_COMMISSION ASC", 3, 0)->toArray() as $date) {
                  $listeDossiersNonAffect = $dbDossierAffectation->getDossierNonAffect($date["ID_DATECOMMISSION"]);
                  $listeDossiersAffect = $dbDossierAffectation->getDossierAffect($date["ID_DATECOMMISSION"]);
                  $odj = array_merge($listeDossiersNonAffect, $listeDossiersAffect);
                  $odj = array_unique($odj, SORT_REGULAR);
                  $commissions[] = array("name" => $commission["LIBELLE_COMMISSION"] . ' - ' . $date['LIBELLE_DATECOMMISSION'], "date" => $date["DATE_COMMISSION"], "heure" => $date["HEUREDEB_COMMISSION"] . ' - ' . $date["HEUREFIN_COMMISSION"], "odj" => $odj);
                }
              }
            }

            break;

          case 'Maire':
            if($user["NUMINSEE_COMMUNE"] != null) {
              $search = new Model_DbTable_Search;
              $search->setItem("etablissement");
              $search->setCriteria("etablissementadresse.NUMINSEE_COMMUNE", $user["NUMINSEE_COMMUNE"]);
              $search->setCriteria("avis.ID_AVIS", 2);
              $etablissements = $search->run(false, null, false)->toArray();
            }

            break;

          case 'Préventionniste':
            // Etablissements Sans Preventionniste
            $erpSansPreventionniste = $dbEtablissement->listeERPSansPreventionniste();
            // Ets 1 - 4ème catégorie
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("etablissementinformations.ID_CATEGORIE", array("1","2","3","4"));
            $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

            // 5ème catégorie defavorable
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("etablissementinformations.ID_CATEGORIE", "5");
            $search->setCriteria("avis.ID_AVIS", 2);
            $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

            // 5ème catégorie avec local à sommeil
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("etablissementinformations.ID_CATEGORIE", "5");
            $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            $search->setCriteria("etablissementinformations.LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS", "1");
            $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

            // EIC - IGH - HAB
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("etablissementinformations.ID_GENRE", array("6","5","4"));
            $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

            $etablissements = array_unique($etablissements, SORT_REGULAR);

            // Dossiers avec avis différé
            $service_etablissement = new Service_Etablissement;
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

            break;
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

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $user = $id_user == null ? $DB_user->createRow() : $DB_user->find($id_user)->current();
            $informations = $id_user == null ? $DB_informations->createRow() : $DB_informations->find($user->ID_UTILISATEURINFORMATIONS)->current();

            $informations->NOM_UTILISATEURINFORMATIONS = $data['NOM_UTILISATEURINFORMATIONS'];
            $informations->PRENOM_UTILISATEURINFORMATIONS = $data['PRENOM_UTILISATEURINFORMATIONS'];
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
