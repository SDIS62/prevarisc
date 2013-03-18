<?php
    class EtablissementController extends Zend_Controller_Action
    {
        private $informations;
        private $DB_etablissement;
        private $droits;

        // Initialisation de la classe reprÃ©sentant les genres des Ã©tablissements
        public function init()
        {
            // Actions Ã  effectuÃ©es en AJAX
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
            $ajaxContext->addActionContext('save', 'json')
                        ->addActionContext('get', 'json')
                        ->addActionContext('get-default-values', 'json')
                        ->addActionContext('carte', 'html')
                        ->addActionContext('fiche-existe', 'json')
                        ->initContext();

            $this->DB_etablissement = new Model_DbTable_Etablissement;

            $this->droits = $this->_helper->Droits()->get();

            if ($this->_request->format != "json") {

                $this->view->droits = $this->droits;

                $this->view->genre = "null";

                // DÃ©finition du layout
                $this->_helper->layout->setLayout('etablissement');

                // Nom de l'action appellÃ©e
                if( !isset($this->view->action) )
                    $this->view->action = $this->_request->getActionName();

                // ModÃ¨les commun aux actions
                $DB_avis = new Model_DbTable_Avis;								$this->view->DB_avis = $DB_avis->fetchAll()->toArray();
                $DB_statut = new Model_DbTable_Statut;							$this->view->DB_statut = $DB_statut->fetchAll()->toArray();
                $DB_genre = new Model_DbTable_Genre;							$this->view->DB_genre = $DB_genre->fetchAll()->toArray();

                // Liste des champs à afficher en fonction du genre
                $liste_champs = $this->DB_etablissement->getListeChamps();

                // DonnÃ©es de l'Ã©tablissement
                if ($this->_request->id) {

                    // Etablissement
                    $this->view->DB_etablissement = $this->DB_etablissement->find( $this->_request->id )->current();

                    // Informations de l'Ã©tablissement
                    $this->informations = $this->DB_etablissement->getInformations( $this->_request->id );
                    $this->view->DB_informations = $this->informations;

                    // Envoi le genre
                    $this->view->genre = Zend_Json::encode( $this->view->DB_genre[$this->informations["ID_GENRE"]-1]["LIBELLE_GENRE"] );

                    // L'Ã©tablissement fait-il partie d'un autre Ã©tablissement ?
                    $result = null;
                    $id_enfant = $this->_request->id;
                    do {

                        $parent = $this->DB_etablissement->getParent( $id_enfant );

                        if ($parent != null) {
                            $result[] = $parent;
                            $id_enfant = $parent["ID_ETABLISSEMENT"];
                        }

                    } while ( $parent != null );

                    if( $result != null )
                        $result = array_reverse($result);

                    $this->view->etablissement_parents = $result;
                }

                // Envoi des champs des genres
                $this->view->liste_champs = Zend_Json::encode( $liste_champs );

                // DonnÃ©e communes Ã  tout les genres
                foreach ($liste_champs as $key => $champs) {
                    $liste_champs[$key] = array_merge($liste_champs[$key], array("libelle", "statut", "genre", "telephone", "fax", "courriel", "type_plans", "numero_plans", "date_plan[]", "statut_plans"));
                }

                // DÃ©finition du titre
                $this->view->title = ( $this->_request->id ) ? ucfirst($this->view->action) . " | " . $this->informations["LIBELLE_ETABLISSEMENTINFORMATIONS"] : "Création d'un établissement";

                // Mode de lecture (read si il y'a un id spÃ©cifiÃ©, edit si on est en crÃ©ation)
                $this->view->mode_de_lecture = Zend_Json::encode( ( $this->_request->id ) ? "read" : "edit" );
            }

            // Check des droits
            if ($this->_request->getActionName() != "get-default-values") {
                if ($this->_request->id) {

                    if($this->_helper->Droits()->checkEtablissement($this->_request->id))
                        $this->_helper->Droits()->redirect();
                } else {

                    if ($this->droits->DROITETSCREATION_GROUPE != 1) {

                        $this->_helper->Droits()->redirect();
                    }
                }
            }
        }

        // Accueil de la fiche Ã©tablissement
        public function indexAction()
        {
            $this->view->id_etablissement = $this->_request->id;

            // Diaporama
            $this->view->inlineScript()->appendFile("/js/jquery.popeye-2.0.4.min.js");
            $this->view->headLink()->appendStylesheet('/css/jquery/jquery.popeye.css');

            // On rÃ©cupÃ¨re l'ensemble des donnÃ©es dont on a besoin
            $DB_categorie = new Model_DbTable_Categorie;					$this->view->DB_categorie = $DB_categorie->fetchAllPK();
            $DB_type = new Model_DbTable_Type;								$this->view->DB_type = $DB_type->fetchAll()->toArray();
            $DB_activite = new Model_DbTable_TypeActivite;					$this->view->DB_activite = $DB_activite->fetchAll()->toArray();
            $DB_commission = new Model_DbTable_Commission;					$this->view->DB_commission = $DB_commission->fetchAllPK();
            $DB_preventionnistes = new Model_DbTable_Commission;			$this->view->DB_preventionnistes = $DB_preventionnistes->fetchAll()->toArray();
            $DB_typesplan = new Model_DbTable_TypePlan;						$this->view->DB_typesplan = $DB_typesplan->fetchAllPK();
            $DB_famille = new Model_DbTable_Famille;						$this->view->DB_famille = $DB_famille->fetchAllPK();
            $DB_classe = new Model_DbTable_Classe;							$this->view->DB_classe = $DB_classe->fetchAllPK();
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            $DB_plans = new Model_DbTable_EtablissementInformationsPlan;
            $DB_typeactsecondaires = new Model_DbTable_EtablissementInformationsTypesActivitesSecondaires;
            $DB_rubriques = new Model_DbTable_EtablissementInformationsRubrique;
            $search = new Model_DbTable_Search;
            $model_groupement = new Model_DbTable_Groupement;
            $model_admin = new Model_DbTable_Admin;

            // Gestion des prototypes pour l'ajout en ajax & des modÃ¨les
            $plans = ( isset($this->informations) ) ? $DB_plans->fetchAll("ID_ETABLISSEMENTINFORMATIONS = " . $this->informations->ID_ETABLISSEMENTINFORMATIONS)->toArray() : null;
            $plans[-1] = array_fill_keys ( array( "ID_TYPEPLAN", "NUMERO_ETABLISSEMENTPLAN", "DATE_ETABLISSEMENTPLAN", "ID_STATUTPLAN" ) , null );
            $this->view->plans = $plans;

            $types_activites_secondaires = ( isset($this->informations) ) ? $DB_typeactsecondaires->fetchAll("ID_ETABLISSEMENTINFORMATIONS = " . $this->informations->ID_ETABLISSEMENTINFORMATIONS)->toArray() : null;
            $types_activites_secondaires[-1] = array_fill_keys ( array( "ID_TYPE_SECONDAIRE", "ID_TYPEACTIVITE_SECONDAIRE" ) , null );
            $this->view->types_activites_secondaires = $types_activites_secondaires;

            $rubriques = ( isset($this->informations) ) ? $DB_rubriques->fetchAll("ID_ETABLISSEMENTINFORMATIONS = " . $this->informations->ID_ETABLISSEMENTINFORMATIONS, "ID_ETABLISSEMENTINFORMATIONSRUBRIQUE")->toArray() : null;
            $rubriques[-1] = array_fill_keys ( array( "NUMERO_ETABLISSEMENTINFORMATIONSRUBRIQUE", "NOM_ETABLISSEMENTINFORMATIONSRUBRIQUE", "VALEUR_ETABLISSEMENTINFORMATIONSRUBRIQUE", "CLASSEMENT_ETABLISSEMENTINFORMATIONSRUBRIQUE" ) , null );
            $this->view->rubriques = $rubriques;

            $etablissement_lies = $search->setItem("etablissement")->setCriteria("etablissementlie.ID_ETABLISSEMENT", $this->_request->id)->run();
            $etablissement_lies[-1] = array_fill_keys ( array( "ID_FILS_ETABLISSEMENT", "LIBELLE_ETABLISSEMENTINFORMATIONS" ) , null );
            $this->view->etablissement_lies = $etablissement_lies;

            $preventionnistes = ( $this->_request->id ) ? $search->setItem("utilisateur")->setCriteria("etablissementinformations.ID_ETABLISSEMENT", $this->_request->id)->run() : null;
            $preventionnistes[-1] = array_fill_keys ( array( "LIBELLE_GRADE", "NOM_UTILISATEURINFORMATIONS", "PRENOM_UTILISATEURINFORMATIONS" ) , null );
            $this->view->preventionnistes = $preventionnistes;

            $adresses = $DB_adresse->get($this->_request->id);
            $adresses[-1] = array_fill_keys ( array( "LON_ETABLISSEMENTADRESSE", "LAT_ETABLISSEMENTADRESSE", "NUMERO_ADRESSE", "ID_RUE", "NUMINSEE_COMMUNE", "COMPLEMENT_ADRESSE", "LIBELLE_COMMUNE", "LIBELLE_RUE", "CODEPOSTAL_COMMUNE" ) , null );
            $this->view->adresses = $adresses;

            $this->view->idwinprev = $this->DB_etablissement->getIDWinprev($this->_request->id);

            // Si c'est un Ã©tablissement existant
            if ($this->_request->id) {

                // Images pour le diapo
                $this->view->diapo_plans = $this->DB_etablissement->getPlans($this->_request->id);
                $this->view->diapo = $this->DB_etablissement->getDiaporama($this->_request->id);

                // Adresse du pÃ¨re
                if ($this->view->etablissement_parents) {

                    $pere = end($this->view->etablissement_parents);

                    if ($pere) {

                        $this->view->pere = $pere;
                        $adresses_pere = $DB_adresse->get($pere["ID_ETABLISSEMENT"]);
                        $this->view->adresses_pere = $adresses_pere;

                        // Catégorie pour la cellule
                        $this->view->cell_categorie = $this->DB_etablissement->getDefaultCategorie(array("ID_GENRE" => 3, "ID_PERE" => $pere["ID_ETABLISSEMENT"]));
                    }
                }

                // Les dernières et prochaines visites
                $this->view->last_visite =  $this->DB_etablissement->getVisiteLastPeriodique( $this->_request->id ) != null ? $this->DB_etablissement->getVisiteLastPeriodique( $this->_request->id )->get( Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR ) : null;
                $this->view->next_visite =  $this->DB_etablissement->getVisiteNextPeriodique( $this->_request->id ) != null ? $this->DB_etablissement->getVisiteNextPeriodique( $this->_request->id )->get( Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR ) : null;

                //Zend_Debug::dump($this->view->last_visite);
            }
            // Pour un nouveel Ã©tablissement
            elseif (isset($_GET["pere"])) {

                $this->view->adresses_pere = $DB_adresse->get($_GET["pere"]);
            }

            // Si un pÃ¨re est donnÃ©, on diminue la liste des genres possible
            if (isset($_GET["pere"]) || count($this->view->etablissement_parents) > 0) {

                if(is_array($this->view->etablissement_parents))
                    $pere = end($this->view->etablissement_parents);

                $id_pere = isset($_GET["pere"]) ? $_GET["pere"] : $pere["ID_ETABLISSEMENT"];
                $id_genre = $this->DB_etablissement->getGenre($id_pere);

                switch ($id_genre["ID_GENRE"] - 1) { // -1 pour correspondre au tableau
                    case 0 :
                        // Si le parent = site, on enlÃ¨ve = cellule, site
                        unset($this->view->DB_genre[0]);
                        unset($this->view->DB_genre[2]);
                        break;

                    case 1 : // Si le parent = etablissement, on enlÃ¨ve = site, hab, eic, igh, etablissement
                        unset($this->view->DB_genre[0]);
                        unset($this->view->DB_genre[1]);
                        unset($this->view->DB_genre[3]);
                        unset($this->view->DB_genre[4]);
                        unset($this->view->DB_genre[5]);
                        break;
                }
            }

            if (count($adresses) > 1) {
                // Envoi des groupements de l'ets
                $this->view->array_groupements = $model_groupement->getGroupementParVille($adresses[0]["NUMINSEE_COMMUNE"]);
            }

        }

        public function carteAction()
        {
            if ($this->_request->id) {

                $DB_adresse = new Model_DbTable_EtablissementAdresse;
                $search = new Model_DbTable_Search;

                $etablissement_lies = $search->setItem("etablissement")->setCriteria("etablissementlie.ID_ETABLISSEMENT", $this->_request->id)->run();
                $this->view->etablissement_lies = $etablissement_lies;

                $adresses = $DB_adresse->get($this->_request->id);
                $adresses[-1] = array_fill_keys ( array( "LON_ETABLISSEMENTADRESSE", "LAT_ETABLISSEMENTADRESSE", "NUMERO_ADRESSE", "ID_RUE", "NUMINSEE_COMMUNE", "COMPLEMENT_ADRESSE", "LIBELLE_COMMUNE", "LIBELLE_RUE", "CODEPOSTAL_COMMUNE" ) , null );
                $this->view->adresses = $adresses;
            }
        }

        public function descriptifAction()
        {
            if ($this->_request->DESCRIPTIF_ETABLISSEMENT) {

                $etablissement = $this->DB_etablissement->find( $this->_request->id )->current();
                $etablissement->DESCRIPTIF_ETABLISSEMENT = $this->_request->DESCRIPTIF_ETABLISSEMENT;
                $etablissement->save();

                $this->_helper->_redirector("descriptif", $this->_request->getControllerName(), null, array("id" => $this->_request->id));
            }
        }

        public function piecesJointesAction()
        {
            $this->_forward("index", "piece-jointe", null, array(
                "type" => "etablissement",
                "id" => $this->_request->id
            ));
        }

        public function contactsAction()
        {
        }

        public function dossiersAction()
        {
            // CrÃ©ation de l'objet recherche
            $search = new Model_DbTable_Search;

            // On set le type de recherche
            $search->setItem("dossier");

            // On recherche avec l'id de l'Ã©tablissement
            $search->setCriteria("e.ID_ETABLISSEMENT", $this->_request->id);

            // On balance le rÃ©sultat sur la vue
            $this->view->dossiers = $search->run();
        }

        public function historiqueAction()
        {
            $historique = array();

            // ModÃ¨les additionnels
            $DB_categorie = new Model_DbTable_Categorie;					$categories = $DB_categorie->fetchAll()->toArray();
            $DB_famille = new Model_DbTable_Famille;						$familles = $DB_famille->fetchAll()->toArray();
            $DB_classe = new Model_DbTable_Classe;							$classes = $DB_classe->fetchAll()->toArray();
            $DB_utilisateurs = new Model_DbTable_Utilisateur;
            $DB_utilisateursInfo = new Model_DbTable_UtilisateurInformations;

            // Instances des modÃ¨les
            $DB_information = new Model_DbTable_EtablissementInformations;

            // On rÃ©cupÃ¨re toutes les fiches de l'Ã©tablissement
            $fiches = $DB_information->fetchAll("ID_ETABLISSEMENT = " . $this->_request->id, "DATE_ETABLISSEMENTINFORMATIONS")->toArray();

            // On traite le tout
            foreach ($fiches as $id => $fiche) {

                foreach ($fiche as $key => $item) {

                    $tmp = ( array_key_exists($key, $historique) ) ? $historique[$key][ count($historique[$key])-1 ] : null;

                    $value = null;

                    switch ($key) {

                        case "LIBELLE_ETABLISSEMENTINFORMATIONS":
                            $value = $item;
                            break;

                        case "ID_STATUT":
                            $value = $this->view->DB_statut[$item - 1]["LIBELLE_STATUT"];
                            break;

                        case "ID_AVIS":
                            if (isset($this->view->DB_avis[$item - 1])) {
                                $value = "<span class='avis " . $this->view->DB_avis[$item - 1]["LIBELLE_AVIS"][0] . "' >".$this->view->DB_avis[$item - 1]["LIBELLE_AVIS"]."</span>";
                            }
                            break;

                        case "ID_CATEGORIE":
                            if (isset($categories[$item - 1])) {
                                $value = $categories[$item - 1]["LIBELLE_CATEGORIE"];
                            }
                            break;

                        case "ID_TYPE":
                            $value = $item;
                            break;
                    }

                    if ( !isset( $historique[$key] ) || $tmp["valeur"] != $value ) {

                        $date = new Zend_Date($fiche["DATE_ETABLISSEMENTINFORMATIONS"], Zend_Date::DATES);

                        if ($tmp != null) {
                            $historique[$key][ count($historique[$key])-1 ]["fin"] = $date->get( Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR );
                        }

                        $historique[$key][] = array(
                            "valeur" => $value,
                            "debut" =>  $date->get( Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR ),
                            "author" => $fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"] == 0 ? null : array("id" => $fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"], "object" => $DB_utilisateursInfo->fetchRow("ID_UTILISATEURINFORMATIONS = " . $DB_utilisateurs->find($fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"])->current()->ID_UTILISATEUR))
                        );
                    }
                }
            }

            // On met ds le sens aujourd'hui -> passÃ©
            foreach ($historique as $key => $item) {
                $historique[$key] = array_reverse($item);
            }

            $liste_champs = Zend_Json::decode($this->view->liste_champs);
            $genre = Zend_Json::decode($this->view->genre);

            // Envoi des variables Ã  la vue
            $this->view->historique = $historique;
            $this->view->fiches = $fiches;
            $this->view->classement = $liste_champs[$genre];
        }

        public function addAction()
        {
            $this->view->action = "add";
            $this->_forward('index');
        }

        public function ficheExisteAction()
        {
            $DB_information = new Model_DbTable_EtablissementInformations;

            if ($this->_request->date != "undefined") {

                $array_date = $this->getDate($this->_request->date);
                $this->view->bool_fiche = (null != ($row = $DB_information->fetchRow("ID_ETABLISSEMENT = '" .  $this->_request->id . "' AND DATE_ETABLISSEMENTINFORMATIONS = '" . $array_date . "'"))) ? true : false;
            } else {

                $this->view->bool_fiche = false;
            }
        }

        public function saveAction()
        {
            // Instances des modÃ¨les
            $DB_information = new Model_DbTable_EtablissementInformations;
            $DB_tab["ID_TYPEPLAN"] = new Model_DbTable_EtablissementInformationsPlan;
            $DB_tab["ID_RUBRIQUE"] = new Model_DbTable_EtablissementInformationsRubrique;
            $DB_tab["ID_TYPE_SECONDAIRE"] = new Model_DbTable_EtablissementInformationsTypesActivitesSecondaires;
            $DB_tab["ID_FILS_ETABLISSEMENT"] = new Model_DbTable_EtablissementLie;
            $DB_tab["ID_UTILISATEUR"] = new Model_DbTable_EtablissementInformationsPreventionniste;
            $DB_tab["NUMERO_ADRESSE"] = new Model_DbTable_EtablissementAdresse;

            // On commence la transaction
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            // Variable pour savoir si on créé ou Update
            $historique = false;

            try {

                // Si il n'y a pas d'id
                if (!$this->_request->id) {
                    // On créé un nouvel Ã©tablissement
                    $etablissement = $this->DB_etablissement->createRow();
                } else {
                    // On récupère l'instance d'un établissement
                    $etablissement = $this->DB_etablissement->find( $this->_request->id )->current();
                }

                // Est ce que l'on créé un nouveau bloc d'informations
                if (!$this->_request->id || $this->_request->historique == 1) {

                    $historique = true;

                    if ($this->_request->historique && $this->_request->id) {

                        $rowtmp = null;

                        $array_date = $this->getDate($this->_request->date);
                        if (null != ($row = $DB_information->fetchRow("ID_ETABLISSEMENT = '" .  $this->_request->id . "' AND DATE_ETABLISSEMENTINFORMATIONS = '" . $array_date . "'"))) {

                            $informations = $row;

                            // On vide les tables des plans / rubrique / secondaire
                            if ($this->droits->DROITETABLISSEMENT_GROUPE != 2) {
                                foreach ($DB_tab as $key => $tab) {
                                    if ( !in_array($key, array("ID_FILS_ETABLISSEMENT", "NUMERO_ADRESSE")) ) {

                                        $tab->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                                    } else {

                                        $tab->delete( "ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                                    }
                                }
                            } else {

                                // On vide que les champs prévision
                                $DB_tab["ID_TYPEPLAN"]->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                            }
                            // throw new Exception('Vous ne pouvez pas créer deux fiches etablissement datant du même jour', 500);
                        }

                        if ($this->droits->DROITETABLISSEMENT_GROUPE != 2) {
                            $DB_tab["NUMERO_ADRESSE"]->delete("ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                            $DB_tab["ID_FILS_ETABLISSEMENT"]->delete("ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                        }
                    }

                    if (!isset($informations)) {

                        $update = false;
                        $informations = $DB_information->createRow();
                        $informations->DATE_ETABLISSEMENTINFORMATIONS = $this->getDate(isset($this->_request->date) ? $this->_request->date : date("d/m/Y", time()));
                    }

                    unset($_GET["historique"]);
                } else {

                    // On recupÃ¨re le dernier bloc d'informations de l'Ã©tablissement
                    $informations = $this->DB_etablissement->getInformations( $this->_request->id );

                    // On vide les tables des plans / rubrique / secondaire
                    if ($this->droits->DROITETABLISSEMENT_GROUPE != 2) {
                        foreach ($DB_tab as $key => $tab) {
                            if ( !in_array($key, array("ID_FILS_ETABLISSEMENT", "NUMERO_ADRESSE")) ) {

                                $tab->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                            } else {

                                $tab->delete( "ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                            }
                        }
                    } else {

                        // On vide que les champs prévision
                        $DB_tab["ID_TYPEPLAN"]->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    }
                }

                // On met Ã  0 les checkbox, elles seront checkÃ©es si dans le formulaire elles le sont
                $informations->LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS = 0;
                $informations->ICPE_ETABLISSEMENTINFORMATIONS = 0;
                $informations->R12320_ETABLISSEMENTINFORMATIONS = 0;
                $informations->SCHEMAMISESECURITE_ETABLISSEMENTINFORMATIONS = 0;
                $informations->ID_AVIS = null;

                // Sauvegarde de la base pour rÃ©cupÃ©rer les id
                $etablissement->save();
                $informations->ID_ETABLISSEMENT = $etablissement->ID_ETABLISSEMENT;
                $informations->save();

                // On populate les informations
                foreach ($_GET as $key => $data) {

                    // DonnÃ©es non historisÃ©es
                    if ( in_array($key, array("NUMEROID_ETABLISSEMENT", "TELEPHONE_ETABLISSEMENT", "FAX_ETABLISSEMENT", "COURRIEL_ETABLISSEMENT")) ) {

                        $etablissement->$key = $data;
                    }
                    // DonnÃ©es historisÃ©es
                    else if ( !is_array($data) ) {

                        if (in_array($key, $DB_information->info(Zend_Db_Table_Abstract::COLS))) {
                            if( in_array($key, array("DATEPCINITIAL_ETABLISSEMENTINFORMATIONS", "DATEPCMODIFICATIF_ETABLISSEMENTINFORMATIONS")) )
                                $informations->$key = $this->getDate($data);
                            else
                                $informations->$key = $data;
                        }
                    }
                    // Données dans une table a part
                    else {

                        if ( array_key_exists($key, $DB_tab) ) {

                            for ( $i=0; $i<count($data); $i++ ) {

                                // CrÃ©ation de la ligne
                                $item = $DB_tab[$key]->createRow();


                                foreach ( $DB_tab[$key]->info(Zend_Db_Table_Abstract::COLS) as $col ) {

                                    if ( isset($_GET[$col][$i]) ) {

                                        $item->$col = $col == "DATE_ETABLISSEMENTPLAN" ? $this->getDate($_GET[$col][$i]) : $_GET[$col][$i];
                                    }
                                }

                                // Gestion historique ou pas
                                if ( !in_array($key, array("ID_FILS_ETABLISSEMENT", "NUMERO_ADRESSE")) ) {

                                    $item->ID_ETABLISSEMENTINFORMATIONS = $informations->ID_ETABLISSEMENTINFORMATIONS;
                                } else {

                                    $item->ID_ETABLISSEMENT = $etablissement->ID_ETABLISSEMENT;
                                }

                                // Sauvegarde
                                $item->save();
                            }
                        }
                    }
                }

                // On sauvegarde l'utilisateur qui a sauvegardé la fiche
                $informations->UTILISATEUR_ETABLISSEMENTINFORMATIONS = Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR;

                // On sauvegarde les changements
                $etablissement->save();
                $informations->save();

                // On spÃ©cifi le pÃ¨re
                if ($this->_request->ID_PERE != "") {

                    $pere = $DB_tab["ID_FILS_ETABLISSEMENT"]->fetchRow("ID_ETABLISSEMENT = " . $this->_request->ID_PERE . " AND ID_FILS_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);

                    if (!$pere) {

                        $item = $DB_tab["ID_FILS_ETABLISSEMENT"]->createRow();
                        $item->ID_ETABLISSEMENT = (int) $this->_request->ID_PERE;
                        $item->ID_FILS_ETABLISSEMENT = $etablissement->ID_ETABLISSEMENT;
                        $item->save();
                    }
                } else {

                    if($this->droits->DROITETABLISSEMENT_GROUPE != 2)
                        $DB_tab["ID_FILS_ETABLISSEMENT"]->delete("ID_FILS_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                }

                $db->commit();

                // On ajoute la news dans la db
                // $model = new Model_DbTable_News;
                //$model->add("informations", "La fiche Ã©tablissement de <a href='/etablissement/index/id/".$etablissement->ID_ETABLISSEMENT."'>" . $informations->LIBELLE_ETABLISSEMENTINFORMATIONS . "</a> a Ã©tÃ© mise Ã  jour.", array(1) );

                // On donne le lien vers l'Ã©tablissement pour le rechargement
                if( $etablissement->ID_ETABLISSEMENT )
                    $this->view->url = "/etablissement/index/id/".$etablissement->ID_ETABLISSEMENT."?confirm=1";

                // On update les cat et la perio des celulles enfants si il y en a
                $this->DB_etablissement->recalcEnfants($etablissement->ID_ETABLISSEMENT, $informations->ID_ETABLISSEMENTINFORMATIONS, $historique);

            } catch (Exception $e) {

                $this->_helper->viewRenderer->setNoRender();
                $db->rollBack();
                $this->view->error = $e->getMessage();
                //throw $e;
            }
        }

        private function getDate($input)
        {
            $array_date = explode("/", $input);
            if (!is_array($array_date) || count($array_date) != 3) {

                throw new Exception('Erreur dans la date', 500);
            }

            return $array_date[2]."-".$array_date[1]."-".$array_date[0]." 00:00:00";
        }

        public function getAction()
        {
            // CrÃ©ation de l'objet recherche
            $search = new Model_DbTable_Search;

            // On set le type de recherche
            $search->setItem("etablissement");
            $search->limit(5);

            /*
            if( array_key_exists("ID_GENRE", $_GET) )
                $search->setCriteria("genre.ID_GENRE", $this->_request->ID_GENRE);
            */

            // On recherche avec le libellÃ©
            $search->setCriteria("LIBELLE_ETABLISSEMENTINFORMATIONS", $this->_request->q, false);

            // On balance le rÃ©sultat sur la vue
            $this->view->resultats = $search->run();
        }

        public function getDefaultValuesAction()
        {
            // Variables
            $this->view->categorie = $this->DB_etablissement->getDefaultCategorie($_GET);
            if ($this->view->categorie != null) { $_GET["ID_CATEGORIE"] = $this->view->categorie; }
            $this->view->periodicite = $this->DB_etablissement->getDefaultPeriodicite($_GET);
            $this->view->commission = $this->DB_etablissement->getDefaultCommission($_GET);
            $this->view->preventioniste = $this->DB_etablissement->getDefaultPrev($_GET);
        }
    }
