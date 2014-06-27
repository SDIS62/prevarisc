<?php

    class StatistiquesController extends Zend_Controller_Action
    {
        // Liste des differentes extractions et stats avec le lien vers la page correspondant
        private $liste = array (
            "ccdsa-liste-erp-en-exploitation-connus-soumis-a-controle" => "Extraction 1 : CCDSA Liste ERP en exploitation connus soumis à contrôle",
            "liste-des-erp-sous-avis-defavorable" => "Extraction 2 : Liste des ERP sous avis défavorable",
            "prochaines-visites-de-controle-periodique-a-faire-sur-une-commune" => "Extraction 3 : Prochaines visites de contrôle périodique à faire sur une commune",
            "liste-erp-avec-visite-periodique-sur-un-an" => "Extraction 4 : Liste ERP avec des visites périodiques sur 1 an"
        );

        public function init()
        {
            ob_end_clean();
            $this->_helper->layout->setLayout('dashboard');
            // On prépare me XML pour l'extraction et la génération
            while (list($key, $val) = each($this->liste)) {
                $this->_helper->contextSwitch()->addActionContext($key, array('json', 'xml'));
            }

            $this->_helper->contextSwitch()->initContext();
        }

        // Accueil
        public function indexAction()
        {

            $this->view->title = "Statistiques";
            $this->view->liste = $this->liste;
        }

        public function extractionProcess($champs_supplementaires, $noms_des_colonnes_a_afficher, Model_DbTable_Statistiques $requete)
        {
          
            // Si on interroge l'action en json, on demande les champs supplémentaires
            if ($this->_getParam("format") == "json" ) {
                $this->view->result = $champs_supplementaires;
            } else {
               $model_stat = new Model_DbTable_Statistiques;
                $this->view->columns = $noms_des_colonnes_a_afficher;
                $this->view->results = $requete->go();
                $this->view->titre = array(
                    "normalize" => $this->_request->getActionName(),
                    "full" => $this->liste[$this->_request->getActionName()]
                );
                
            }
        }

        // Extraction 1 : CCDSA Liste ERP en exploitation connus soumis à contrôle
        public function ccdsaListeErpEnExploitationConnusSoumisAControleAction()
        {
            $model_stat = new Model_DbTable_Statistiques;

            if ($this->_getParam("format") != "json") 
            {
                $date = new Zend_Date($this->_getParam("date"), Zend_Date::DATES);
                $this->view->resume = "Liste ERP en exploitation connus soumis à contrôle à la date du " . $date->get( Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR );
            }

            $this->extractionProcess(
                array (
                    "date" => array (
                        "label" => "Soumis à un contrôle périodique obligatoire à la date du", "type" => "date", "data" => date("d/m/Y", time())
                    )
                ), array(
                    "Libellé" => "LIBELLE_ETABLISSEMENTINFORMATIONS",
                    "Commune" => "LIBELLE_COMMUNE",
                    "Arrondissement" => "ARRONDISSEMENT",
                    "Type" => "LIBELLE_TYPE",
                    "Catégorie" => "LIBELLE_CATEGORIE",
                    "Date dernière visite de contrôle" => "DATEVISITE_DOSSIER",
                    //"Date prochaine visite de contrôle" => "DATEVISITE_DOSSIER",
                    "Commission"=> "LIBELLE_COMMISSION"
                ), $model_stat->listeDesERP($this->_getParam("date"))->enExploitation()->sousmisAControle()
            );
            if ($this->_getParam("format") != "json") 
            {
                $this->render("extraction");
            }
        }
        // Extraction 2 : Liste des ERP sous avis défavorable
        public function listeDesErpSousAvisDefavorableAction()
        {
            $model_stat = new Model_DbTable_Statistiques;

            if ($this->_getParam("format") != "json") 
            {
                $date = new Zend_Date($this->_getParam("date"), Zend_Date::DATES);
                $this->view->resume = "Liste ERP en exploitation sous avis défavorable à la date du " . $date->get( Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR );
            }

            $this->extractionProcess(
                array (
                    "tri" => array(
                        "label" => "Tri sur une colonne",
                        "type" => "select",
                        "data" => array (
                            "Arrondissement" => "ARRONDISSEMENT",
                            "Commission" => "ID_COMMISSION",
                            "Commune" => "NUMINSEE_COMMUNE",
                            "Type" => "ID_TYPE",
                            "Catégorie" => "ID_CATEGORIE",
                            "Date de la dernière visite de contrôle" => "DATEVISITE_DOSSIER",
                            "Nombre de jours écoulés sous avis défavorable par rapport à la date renseignée" => "NBJOURS_DEFAVORABLE"
                        )
                    ),
                    "date" => array (
                        "label" => "Liste des ERP sous avis défavorable à la date",
                        "type" => "date",
                        "data" => date("d/m/Y", time())
                    )
                ), array(
                    "Libellé" => "LIBELLE_ETABLISSEMENTINFORMATIONS",
                    "Commune" => "LIBELLE_COMMUNE",
                    "Arrondissement" => "ARRONDISSEMENT",
                    "Type" => "ID_TYPE",
                    "Catégorie" => "LIBELLE_CATEGORIE",
                    "Date dernière visite de contrôle" => "DATEVISITE_DOSSIER",
                    "Commission"=> "LIBELLE_COMMISSION",
                    "Nombre de jours écoulés sous avis défavorable par rapport à la date renseignée"=> "NBJOURS_DEFAVORABLE"
                ), $model_stat->listeDesERP($this->_getParam("date"))->enExploitation()->sousAvisDefavorable()->trierPar($this->_getParam("tri"))
            );
           if ($this->_getParam("format") != "json") {
            $this->render("extraction");
            }
        }

        // Extraction 3 : Prochaines visites de contrôle périodique à faire sur une commune
        public function prochainesVisitesDeControlePeriodiqueAFaireSurUneCommuneAction()
        {

            $model_stat = new Model_DbTable_Statistiques;

            // Récupération des communes
            $model_commune = new Model_DbTable_AdresseCommune;
            $rowset_communes = $model_commune->fetchAll(null, "LIBELLE_COMMUNE");
            $communes = array();
            foreach ($rowset_communes as $commune) {
                $communes[$commune["LIBELLE_COMMUNE"]] = $commune["NUMINSEE_COMMUNE"];
            }

            if ($this->_getParam("format") != "json") {
                $date = new Zend_Date($this->_getParam("date"), Zend_Date::DATES);
                $this->view->resume = "Prochaines visites de contrôle périodique à faire sur " . array_search($this->_getParam("commune"), $communes) . " à la date du " . $date->get( Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR );
            }

            $this->extractionProcess(
                array (
                    "date" => array(
                        "label" => "Date",
                        "type" => "date",
                        "data" => date("d/m/Y", time())
                    ),
                    "commune" => array(
                        "label" => "Commune",
                        "type" => "select",
                        "data" => $communes
                    ),
                    "tri" => array(
                        "label" => "Tri sur une colonne",
                        "type" => "select",
                        "data" => array (
                            "Type" => "ID_TYPE",
                            "Catégorie" => "ID_CATEGORIE",
                            "Date de la dernière visite de contrôle" => "DATEVISITE_DOSSIER",
                            "Avis de la dernière visite" => "LIBELLE_AVIS"
                        )
                    ),
                ), array(
                    "Libellé" => "LIBELLE_ETABLISSEMENTINFORMATIONS",
                    "Commune" => "LIBELLE_COMMUNE",
                    "Type" => "ID_TYPE",
                    "Catégorie" => "LIBELLE_CATEGORIE",
                    "Date dernière visite de contrôle" => "DATEVISITE_DOSSIER",
                    "Avis de la dernière visite" => "LIBELLE_AVIS",
                    "Commission"=> "LIBELLE_COMMISSION"
                ), $model_stat->listeDesERP($this->_getParam("date"))->enExploitation()->sousmisAControle()->surLaCommune($this->_getParam("commune"))->trierPar($this->_getParam("tri"))
            );
            if ($this->_getParam("format") != "json") {
            $this->render("extraction");
            }
        }
        
         // Extraction 4 : Liste ERP liées au commission de visite periodique dans une année
        public function listeErpAvecVisitePeriodiqueSurUnAnAction()
        {  
            $model_stat = new Model_DbTable_Statistiques;
            $dateDebut = date("01/01/".date("Y"), time());
            $dateFin = date("31/12/".date("Y"), time());
                      
            if ($this->_getParam("format") != "json") {
                $date = new Zend_Date($this->_getParam("date"), Zend_Date::DATES);
                $this->view->resume = "Liste ERP avec des visites periodiques à partir du " . $date->get( Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR );
            }

            $this->extractionProcess(
                array (
                    "date" => array (
                        "label" => "visite périodique du", "type" => "date", "data" => $dateDebut
                    ),
                    "datefin" => array (
                        "label" => "au", "type" => "date", "data" => $dateFin
                    ),
                    "tri" => array (
                        "label" => "Tri sur une colonne",
                        "type" => "select",
                        "data" => array (
                            "Commune" => "LIBELLE_COMMUNE",
                            "Commission" => "ID_COMMISSION",
                            "Etablissement" => "LIBELLE_ETABLISSEMENTINFORMATIONS",
                            "Type" => "ID_TYPE",
                            "Catégorie" => "ID_CATEGORIE",
                            "Date de prochaine visite" => "DATEVISITE_DOSSIER",
                        )
                    ),
                    "iderp" => array ( 
                        "label" => "", "type" => "id","data" => "ID_ETABLISSEMENT"
                    )
                ),
                array(
                    "Commune" => "LIBELLE_COMMUNE",
                    "Etablissement" => "LIBELLE_ETABLISSEMENTINFORMATIONS",
                    "Prevenstionniste" => "NOM_UTILISATEURINFORMATIONS",
                    "Type" => "LIBELLE_TYPE",
                    "Catégorie" => "LIBELLE_CATEGORIE",
                    "Date de prochaine visite" => "DATEVISITE_DOSSIER",
                    "Date limite de prochaine visite" => "PERIODICITE_ETABLISSEMENTINFORMATIONS",
                    "Commission" => "LIBELLE_COMMISSION"
                ), 
                $model_stat->listeDesERPVisitePeriodique($this->_getParam("date"),$this->_getParam("datefin"))->trierPar($this->_getParam("tri"))
                    
            ); 
            
            if ($this->_getParam("format") != "json") 
            {
                $results = $this->view->results;
                foreach($results as $key => $row) 
                {
                    if($row['DATEVISITE_DOSSIER'] == null)
                    {
                        $results[$key]['DATEVISITE_DOSSIER'] = "<a href='/dossier/add/id_etablissement/".$row['ID_ETABLISSEMENT']."'>Programmer une visite</a>";
                    }
                    if($row['NOM_UTILISATEURINFORMATIONS'] == null)
                    {
                        $results[$key]['NOM_UTILISATEURINFORMATIONS'] =  "<a href='/etablissement/edit/id/".$row['ID_ETABLISSEMENT']."'>Ajouter un préventionniste</a>"; 
                    }

                    if($row['PERIODICITE_ETABLISSEMENTINFORMATIONS']!=0)
                    {

                        if($row['DATEVISITE_DOSSIER'] != null) 
                        {
                            $date = $row['DATEVISITE_DOSSIER'];
                            $d = new DateTime($date);
                            $i=new DateInterval('P'.$row['PERIODICITE_ETABLISSEMENTINFORMATIONS'].'M');
                            $d->add($i);
                            $results[$key]['PERIODICITE_ETABLISSEMENTINFORMATIONS'] = $d->format('Y-m-d');                      
                        } 
                        else
                        {
                            $results[$key]['PERIODICITE_ETABLISSEMENTINFORMATIONS'] = "";    
                        }    

                    }
                    else 
                    {
                       $results[$key]['PERIODICITE_ETABLISSEMENTINFORMATIONS'] = "<a href='/etablissement/edit/id/".$row['ID_ETABLISSEMENT']."'>Modifier la periodicité</a>"; 
                    }
                }
                $this->view->results = $results;
                $this->render("extraction");
            }
        }
 
    }
