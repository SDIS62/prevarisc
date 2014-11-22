<?php

class Service_Dashboard
{
    protected $options = array();
    
    public function __construct() {
        
        // default options
        $this->options = array(
            'next_commissions_days' => 15,
            'dossiers_sans_avis_days' => 15,
            'courrier_sans_reponse_days' => 10,
        );
        
        // custom configurations
        if (getenv('PREVARISC_DASHBOARD_NEXT_COMMISSIONS_DAYS')) {
            $this->options['next_commissions_days'] = (int) getenv('PREVARISC_DASHBOARD_NEXT_COMMISSIONS_DAYS');
        }
        
        if (getenv('PREVARISC_DASHBOARD_DOSSIERS_SANS_AVIS_DAYS')) {
            $this->options['dossiers_sans_avis_days'] = (int) getenv('PREVARISC_DASHBOARD_DOSSIERS_SANS_AVIS_DAYS');
        }
        
        if (getenv('PREVARISC_DASHBOARD_COURRIER_SANS_REPONSE_DAYS')) {
            $this->options['courrier_sans_reponse_days'] = (int) getenv('PREVARISC_DASHBOARD_COURRIER_SANS_REPONSE_DAYS');
        }
    }
    
    protected function getCommissionUser($user) {
        $commissionsUser = array();
        
        if (isset($user['commissions']) && is_array($user['commissions'])) {
            foreach($user['commissions'] as $commission) {
                $commissionsUser[] = $commission["ID_COMMISSION"];
            }
        }
        
        return $commissionsUser;
    }
    
    protected function getCommissionODJ($commission) {
        $dbDossierAffectation = new Model_DbTable_DossierAffectation;
        $odj = array_merge(
                $dbDossierAffectation->getDossierNonAffect($commission["ID_DATECOMMISSION"]), 
                $dbDossierAffectation->getDossierAffect($commission["ID_DATECOMMISSION"])
        );
        $odj = array_unique($odj, SORT_REGULAR);
        return $odj;
    }
    
    public function getNextCommission($user) {
        
        $dbDateCommission = new Model_DbTable_DateCommission;
        
        $prochainesCommission = $dbDateCommission->getNextCommission(
                $this->getCommissionUser($user), 
                time(), time() + 3600 * 24 * $this->options['next_commissions_days']
        );
        
        // on récupère pour chaque prochaine commission le nombre de dossiers affectés
        $commissions = array();
        foreach($prochainesCommission as $commissiondujour)
        {
           $commissions[] = array(
               "id"   => $commissiondujour['ID_DATECOMMISSION'],
               "LIBELLE_COMMISSION" => $commissiondujour["LIBELLE_COMMISSION"],
               "LIBELLE_DATECOMMISSION" => $commissiondujour['LIBELLE_DATECOMMISSION'],
               "ID_COMMISSIONTYPEEVENEMENT" => $commissiondujour["ID_COMMISSIONTYPEEVENEMENT"],
               "DATE_COMMISSION" => $commissiondujour["DATE_COMMISSION"],
               "HEUREDEB_COMMISSION" => $commissiondujour["HEUREDEB_COMMISSION"],
               "HEUREFIN_COMMISSION" => $commissiondujour["HEUREFIN_COMMISSION"],
               "heure" => substr($commissiondujour["HEUREDEB_COMMISSION"], 0, 5) . ' - ' . substr($commissiondujour["HEUREFIN_COMMISSION"], 0, 5),
               "odj" => $this->getCommissionODJ($commissiondujour),
           );
        }
        
        return $commissions;
    }
    
    public function getDossierDateCommissionEchu($user) {
        $dbDossier = new Model_DbTable_Dossier();
        $commissionsUser = $this->getCommissionUser($user);
        return $dbDossier->listeDesDossierDateCommissionEchu($commissionsUser, $this->options['dossiers_sans_avis_days']);
    }
    
    public function getERPOuvertsSousAvisDefavorable($user) {
        $dbEtablissement = new Model_DbTable_Etablissement;
        return $dbEtablissement->listeDesERPOuvertsSousAvisDefavorable();
    }
    
    public function getERPOuvertsSousAvisDefavorableSuivis($user) {
        $dbEtablissement = new Model_DbTable_Etablissement;
        return $dbEtablissement->listeDesERPOuvertsSousAvisDefavorable(null, null, $user["ID_UTILISATEUR"]);
    }
    
    public function getERPOuvertsSousAvisDefavorableSurCommune($user) {
        $dbEtablissement = new Model_DbTable_Etablissement;
        return $dbEtablissement->listeDesERPOuvertsSousAvisDefavorable(null, $user["NUMINSEE_COMMUNE"]);
    }
    
    public function getERPOuvertsSansProchainesVisitePeriodiques($user) {
        $dbEtablissement = new Model_DbTable_Etablissement;
        $commissionsUser = $this->getCommissionUser($user);
        return $dbEtablissement->listeErpOuvertsSansProchainesVisitePeriodiques($commissionsUser);
    }
    
    public function getERPSansPreventionniste($user) {
        $dbEtablissement = new Model_DbTable_Etablissement;
        return $dbEtablissement->listeERPSansPreventionniste();
    }
    
    public function getERPSuivis($user) {
        
        $etablissements = array();
        $id_user = $user['ID_UTILISATEUR'];
        
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
        
        return $etablissements;
    }
    
    public function getDossierAvecAvisDiffere($user) {
        $dbDossier = new Model_DbTable_Dossier ;
        $commissionsUser = $this->getCommissionUser($user);
        return $dbDossier->listeDossierAvecAvisDiffere($commissionsUser);
    }
    
    public function getCourrierSansReponse($user) {
        $dbDossier = new Model_DbTable_Dossier ;
        return $dbDossier->listeDesCourrierSansReponse($this->options['courrier_sans_reponse_days']);
    }
    
    public function getDossiersSuivisNonVerrouilles($user) {
        
        $dossiers = array();
        $id_user = $user['ID_UTILISATEUR'];
        
        // Dossiers suivis
        $search = new Model_DbTable_Search;
        $search->setItem("dossier");
        $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
        $search->setCriteria("d.VERROU_DOSSIER", 0);
        $dossiers = $search->run(false, null, false)->toArray();
        
        return $dossiers;
    }
    
    public function getDossiersSuivisSansAvis($user) {
        
        $dossiers = array();
        $id_user = $user['ID_UTILISATEUR'];
        
        // Dossiers suivis
        $search = new Model_DbTable_Search;
        $search->setItem("dossier");
        $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
        $search->setCriteria("d.VERROU_DOSSIER", 0);
        $search->setCriteria("d.AVIS_DOSSIER IS NULL");
        $dossiers = $search->run(false, null, false)->toArray();
        
        return $dossiers;
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
        
        $service_user = new Service_User;
        
        // Récupération de l'utilisateur & son profil
        $user = $service_user->find($id_user);
        $profil = Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'];

        $etablissements =
                $dossiers_suivis =
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

            // Dossiers suivis
            $search = new Model_DbTable_Search;
            $search->setItem("dossier");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
            $search->setCriteria("d.VERROU_DOSSIER", 0);
            $dossiers_suivis = $search->run(false, null, false)->toArray();

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
          'dossiers_suivis' => $dossiers_suivis,
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
}
