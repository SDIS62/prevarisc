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
        
        if (!$user["NUMINSEE_COMMUNE"]) {
            return array();
        }
        
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
        $search->order('IFNULL(d.DATEVISITE_DOSSIER, d.DATEINSERT_DOSSIER) desc');
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
        
        $conditionEtudesSansAvis = "d.AVIS_DOSSIER IS NULL AND (d.AVIS_DOSSIER_COMMISSION IS NULL OR d.AVIS_DOSSIER_COMMISSION = 0) AND d.TYPE_DOSSIER = 1";
        $conditionCourriersSansReponse = "d.DATEREP_DOSSIER IS NULL AND d.TYPE_DOSSIER = 5";
        
        $search->setCriteria("($conditionEtudesSansAvis) OR ($conditionCourriersSansReponse)");        
        
        $search->order('d.DATEINSERT_DOSSIER desc');
        
        $dossiers = $search->run(false, null, false)->toArray();
        
        return $dossiers;
    }
}
