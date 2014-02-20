<?php

class Service_Etablissement implements Service_Interface_Etablissement
{
    /*
    private $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    */

    public function get($id_etablissement)
    {

    }

    public function getHistorique($id_etablissement)
    {
        $historique = array();

        // Modèles additionnels
        $DB_categorie = new Model_DbTable_Categorie;					$categories = $DB_categorie->fetchAll()->toArray();
        $DB_famille = new Model_DbTable_Famille;						$familles = $DB_famille->fetchAll()->toArray();
        $DB_type = new Model_DbTable_Type;						        $types = $DB_type->fetchAll()->toArray();
        $DB_classe = new Model_DbTable_Classe;							$classes = $DB_classe->fetchAll()->toArray();
        $DB_utilisateurs = new Model_DbTable_Utilisateur;
        $DB_utilisateursInfo = new Model_DbTable_UtilisateurInformations;

        // Instances des modèles
        $DB_information = new Model_DbTable_EtablissementInformations;

        // On récupère toutes les fiches de l'établissement
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

                    case "ID_CATEGORIE":
                        if (isset($categories[$item - 1])) {
                            $value = $categories[$item - 1]["LIBELLE_CATEGORIE"];
                        }
                        break;

                    case "ID_TYPE":
                        if (isset($types[$item - 1])) {
                            $value = $types[$item - 1]["LIBELLE_TYPE"];
                        }
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

        // On met ds le sens aujourd'hui -> passé
        foreach ($historique as $key => $item) {
            $historique[$key] = array_reverse($item);
        }

        $liste_champs = Zend_Json::decode($this->view->liste_champs);
        $genre = Zend_Json::decode($this->view->genre);

        // Envoi des variables à la vue
        $this->view->historique = $historique;
        $this->view->fiches = $fiches;
        $this->view->classement = $liste_champs[$genre];
    }

    public function getDossiers($id_etablissement)
    {
        // Création de l'objet recherche
        $search = new Model_DbTable_Search;

        // récupération des types de dossier autre
        $dossier_types = new Model_DbTable_DossierType;
        $dossier_types = $dossier_types->fetchAll()->toArray();
        $i = 0; $types_autre= array();
        foreach ($dossier_types as $key => $type) {
            if ($type["ID_DOSSIERTYPE"] != 1 && $type["ID_DOSSIERTYPE"] != 2 && $type["ID_DOSSIERTYPE"] != 3) {
                $types_autre[$i] = (int) $type["ID_DOSSIERTYPE"];
                $i++;
            }
        }

        // On balance le résultat sur la vue
        $this->view->etudes = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $this->_request->id)->setCriteria("d.TYPE_DOSSIER", 1)->order("COALESCE(DATECOMM_DOSSIER,DATEINSERT_DOSSIER) DESC")->run();
        $this->view->visites = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $this->_request->id)->setCriteria("d.TYPE_DOSSIER", array(2, 3))->order("DATEVISITE_DOSSIER,COALESCE(DATECOMM_DOSSIER,DATEINSERT_DOSSIER) DESC")->run();
        $this->view->autres = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $this->_request->id)->setCriteria("d.TYPE_DOSSIER", $types_autre)->order("DATEINSERT_DOSSIER DESC")->run();
    }

    public function getDescriptifs($id_etablissement)
    {
        $dbtable_etablissement = new Model_DbTable_Etablissement;
        $dbtable_info_etablissement = $dbtable_etablissement->info();
        $etablissement = $dbtable_etablissement->find($id_etablissement)->current();

        $champs_descriptif_technique = array();

        $translation_champs_des_tech = array(
            "DESCTECH_IMPLANTATION_SURFACE_ETABLISSEMENT" => "Surface emprise au sol (m²)",
            "DESCTECH_IMPLANTATION_SHON_ETABLISSEMENT" => "SHON (m²)",
            "DESCTECH_IMPLANTATION_SHOB_ETABLISSEMENT" => "SHOB (m²)",
            "DESCTECH_IMPLANTATION_NBNIVEAUX_ETABLISSEMENT" => "Nombre de niveaux",
            "DESCTECH_IMPLANTATION_PBDN_ETABLISSEMENT" => "PBDN (m)",
            "DESCTECH_DESSERTE_NBFACADELIBRE_ETABLISSEMENT" => "Nombre de façades accessibles",
            "DESCTECH_DESSERTE_VOIEENGIN_ETABLISSEMENT" => "Voie engin",
            "DESCTECH_DESSERTE_VOIEECHELLE_ETABLISSEMENT" => "Voie echelle",
            "DESCTECH_DESSERTE_ESPACELIBRE_ETABLISSEMENT" => "Espace libre",
            "DESCTECH_ISOLEMENT_LATERALCF_ETABLISSEMENT" => "Latéral CF (heure)",
            "DESCTECH_ISOLEMENT_SUPERPOSECF_ETABLISSEMENT" => "Superposé CF (heure)",
            "DESCTECH_ISOLEMENT_VISAVIS_ETABLISSEMENT" => "Vis-à-vis (m)",
            "DESCTECH_STABILITE_STRUCTURESF_ETABLISSEMENT" => "Structure SF (heure)",
            "DESCTECH_STABILITE_PLANCHERSF_ETABLISSEMENT" => "Plancher SF (heure)",
            "DESCTECH_DISTRIBUTION_CLOISONNEMENTTRAD_ETABLISSEMENT" => "Cloisonnement traditionnel",
            "DESCTECH_DISTRIBUTION_SECTEURS_ETABLISSEMENT" => "Secteurs",
            "DESCTECH_DISTRIBUTION_COMPARTIMENTS_ETABLISSEMENT" => "Compartiments",
            "DESCTECH_LOCAUXARISQUE_NBRISQUESMOYENS_ETABLISSEMENT" => "Nombre de locaux à risques moyens",
            "DESCTECH_LOCAUXARISQUE_NBRISQUESIMPORTANTS_ETABLISSEMENT" => "Nombre de locaux à risques importants",
            "DESCTECH_ESPACES_NOMBRE_ETABLISSEMENT" => "Nombre",
            "DESCTECH_ESPACES_NIVEAUCONCERNE_ETABLISSEMENT" => "Nombre de niveaux concernés",
            "DESCTECH_DESENFUMAGE_NATUREL_ETABLISSEMENT" => "Naturel",
            "DESCTECH_DESENFUMAGE_MECANIQUE_ETABLISSEMENT" => "Mecanique",
            "DESCTECH_DESENFUMAGE_COMMENTAIRE_ETABLISSEMENT" => "Commentaire",
            "DESCTECH_CHAUFFERIES_NB_ETABLISSEMENT" => "Nombre",
            "DESCTECH_CHAUFFERIES_PUISSMAX_ETABLISSEMENT" => "Puissance max (kw)",
            "DESCTECH_CHAUFFERIES_PUISSANCETOTALE" => "Puissance totale (kw)",
            "DESCTECH_CHAUFFERIES_NB30KW" => "Nombre de chaufferies 30kw",
            "DESCTECH_CHAUFFERIES_NB70KW" => "Nombre de chaufferies 70kw",
            "DESCTECH_CHAUFFERIES_NB2MW" => "Nombre de chaufferies 2mw",
            "DESCTECH_COUPURENRJ_GAZ_ETABLISSEMENT" => "Gaz",
            "DESCTECH_COUPURENRJ_ELEC_ETABLISSEMENT" => "Électricité",
            "DESCTECH_COUPURENRJ_PHOTOVOLTAIQUE_ETABLISSEMENT" => "Photovoltaïque",
            "DESCTECH_COUPURENRJ_AUTRE_ETABLISSEMENT" => "Autre",
            "DESCTECH_ASCENSEURS_NBTOTAL_ETABLISSEMENT" => "Nombre ascenseurs total",
            "DESCTECH_ASCENSEURS_NBAS4_ETABLISSEMENT" => "Nombre ascenseurs de type AS4",
            "DESCTECH_MOYENSSECOURS_COLONNESSECHES_ETABLISSEMENT" => "Colonnes sèches",
            "DESCTECH_MOYENSSECOURS_COLONNESHUMIDES_ETABLISSEMENT" => "Colonnes humides",
            "DESCTECH_MOYENSSECOURS_RIA_ETABLISSEMENT" => "RIA",
            "DESCTECH_MOYENSSECOURS_SPRINKLEUR_ETABLISSEMENT" => "Sprinkleur",
            "DESCTECH_MOYENSSECOURS_BROUILLARDEAU_ETABLISSEMENT" => "Brouillard d'eau",
            "DESCTECH_PCSECU_PRESENCE_ETABLISSEMENT" => "Présence",
            "DESCTECH_PCSECU_LOCALISATION_ETABLISSEMENT" => "Localisation",
            "DESCTECH_SSI_PRESENCE_ETABLISSEMENT" => "Présence",
            "DESCTECH_SSI_CATEGORIE_ETABLISSEMENT" => "Catégorie SSI",
            "DESCTECH_SSI_ALARME_TYPE_ETABLISSEMENT" => "Alarme type",
            "DESCTECH_SERVICESECU_CHEFDESERVICESECU_ETABLISSEMENT" => "Chef de service sécurité",
            "DESCTECH_SERVICESECU_CHEFEQUIPE_ETABLISSEMENT" => "Chef d'équipe sécurité",
            "DESCTECH_SERVICESECU_AGENTDESECU_ETABLISSEMENT" => "Agent de sécurité",
            "DESCTECH_SERVICESECU_PERSONNELSDESIGNES_ETABLISSEMENT" => "Personnels désignés",
            "DESCTECH_SERVICESECU_EL18_ETABLISSEMENT" => "Personne qualifiée désignée (EL18)",
            "DESCTECH_SERVICESECU_SP_ETABLISSEMENT" => "Sapeurs Pompiers",
            "DESCTECH_SERVICESECU_COMMENTAIRESP_ETABLISSEMENT" => "Commentaire sur les SP",
            "DESCTECH_DEFENSE_PTEAU_ETABLISSEMENT" => "Nombre de point d'eau",
            "DESCTECH_DEFENSE_VOLUMEPTEAU_ETABLISSEMENT" => "Volume des points d'eau (m3)",
            "DESCTECH_DEFENSE_PTEAUCOMMENTAIRE_ETABLISSEMENT" => "Commentaires sur le / les points d'eau naturel",
            "DESCTECH_DEFENSE_PI_ETABLISSEMENT" => "PI",
            "DESCTECH_DEFENSE_BI_ETABLISSEMENT" => "BI",
            "DESCTECH_DEFENSE_DEBITSIMULTANE_ETABLISSEMENT" => "Débit simultané (m3/h)"
        );

        foreach ($etablissement->toArray() as $key => $value) {
            if (preg_match('/DESCTECH/', $key)) {
                $key_to_str = str_replace('DESCTECH_', '', $key);
                $key_to_str = explode('_', $key_to_str);
                $key_to_str = $key_to_str[0];
                $title = null;

                switch ($key_to_str) {
                    case "IMPLANTATION": $title = "Implantation"; break;
                    case "DESSERTE": $title = "Desserte"; break;
                    case "ISOLEMENT": $title = "Isolement par rapport aux tiers"; break;
                    case "STABILITE": $title = "Stabilité au feu"; break;
                    case "DISTRIBUTION": $title = "Distribution intérieure"; break;
                    case "LOCAUXARISQUE": $title = "Locaux à risques"; break;
                    case "ESPACES": $title = "Espaces d'attentes sécurisés"; break;
                    case "DESENFUMAGE": $title = "Désenfumage"; break;
                    case "CHAUFFERIES": $title = "Chaufferies"; break;
                    case "COUPURENRJ": $title = "Localisation des coupures d'énergies"; break;
                    case "ASCENSEURS": $title = "Ascenseurs"; break;
                    case "MOYENSSECOURS": $title = "Moyens de secours"; break;
                    case "PCSECU": $title = "PC Sécurité"; break;
                    case "SSI": $title = "SSI"; break;
                    case "SERVICESECU": $title = "Service de sécurité"; break;
                    case "DEFENSE": $title = "Défense incendie"; break;
                }

                $champs_descriptif_technique[$title][array_key_exists($key, $translation_champs_des_tech) ? $translation_champs_des_tech[$key] : $key] = array(
                    'value' => $value,
                    'type' => $dbtable_info_etablissement['metadata'][$key]['DATA_TYPE'],
                    'key' => $key
                );
            }
        }

        return array(
            'historique' => $etablissement->DESCRIPTIF_HISTORIQUE_ETABLISSEMENT,
            'descriptif' => $etablissement->DESCRIPTIF_ETABLISSEMENT,
            'derogations' => $etablissement->DESCRIPTIF_DEROGATIONS_ETABLISSEMENT,
            'descriptifs_techniques' => $champs_descriptif_technique
        );
    }

    public function saveDescriptifs($id_etablissement, $historique, $descriptif, $derogations, $descriptifs_techniques)
    {

        $dbtable_etablissement = new Model_DbTable_Etablissement;
        $etablissement = $dbtable_etablissement->find($id_etablissement)->current();

        $etablissement->DESCRIPTIF_DEROGATIONS_ETABLISSEMENT = $derogations;
        $etablissement->DESCRIPTIF_HISTORIQUE_ETABLISSEMENT = $historique;
        $etablissement->DESCRIPTIF_ETABLISSEMENT = $descriptif;

        foreach($descriptifs_techniques as $key => $value) {
            $etablissement->$key = $value;
        }

        $etablissement->save();
    }

    public function getAdresses($id_etablissement)
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

    public function getPlans($id_etablissement)
    {
        $this->DB_etablissement->getDiaporama($this->_request->id);
    }

    public function getDiapo($id_etablissement)
    {
        $this->DB_etablissement->getPlans($this->_request->id);
    }

    public function findAll($libelle)
    {
        // CrÃ©ation de l'objet recherche
        $search = new Model_DbTable_Search;

        // On set le type de recherche
        $search->setItem("etablissement");
        $search->limit(5);

        // On recherche avec le libellé
        $search->setCriteria("LIBELLE_ETABLISSEMENTINFORMATIONS", $libelle, false);

        // On filtre par le genre
        if ($this->_request->g) {
            if ($this->_request->genre_pere == 1) {
                if($this->_request->g == 2)
                    $search->setCriteria("etablissementinformations.ID_GENRE", 1);
                elseif($this->_request->g == 3)
                    $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            }

            if ($this->_request->genre_enfant == 1) {
                if($this->_request->g == 1)
                    $search->setCriteria("etablissementinformations.ID_GENRE", array(2,4,5,6));
                elseif($this->_request->g == 2)
                    $search->setCriteria("etablissementinformations.ID_GENRE", 3);
            }
        }

        // On balance le rÃ©sultat sur la vue
        $this->view->resultats = $search->run()->getAdapter()->getItems(0, 99999999999)->toArray();
    }

    public function ficheExiste($id_etablissement)
    {
        $DB_information = new Model_DbTable_EtablissementInformations;

        if ($this->_request->date != "undefined") {
            $array_date = $this->getDate($this->_request->date);
            $this->view->bool_fiche = (null != ($row = $DB_information->fetchRow("ID_ETABLISSEMENT = '" .  $this->_request->id . "' AND DATE_ETABLISSEMENTINFORMATIONS = '" . $array_date . "'"))) ? true : false;
        } else {
            $this->view->bool_fiche = false;
        }
    }

    public function save($id_etablissement)
    {
        // Instances des modèles
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
        $new = false;

        try {
            if (!isset($_GET["ID_UTILISATEUR"]) || count($_GET["ID_UTILISATEUR"]) == 0 || $_GET["ID_UTILISATEUR"][0] == null) {
                throw new Exception("Pas de préventionnistes liés.");
            }

            // Si il n'y a pas d'id
            if (!$this->_request->id) {
                // On créé un nouvel établissement
                $etablissement = $this->DB_etablissement->createRow();
                $new = true;
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
                        foreach ($DB_tab as $key => $tab) {
                            if ( !in_array($key, array("ID_FILS_ETABLISSEMENT", "NUMERO_ADRESSE")) ) {
                                $tab->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                            } else {
                                $tab->delete( "ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                            }
                        }
                    }

                    $DB_tab["NUMERO_ADRESSE"]->delete("ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                    $DB_tab["ID_FILS_ETABLISSEMENT"]->delete("ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                }

                if (!isset($informations)) {
                    $update = false;
                    $informations = $DB_information->createRow();
                    $informations->DATE_ETABLISSEMENTINFORMATIONS = $this->getDate(isset($this->_request->date) ? $this->_request->date : date("d/m/Y", time()));
                }

                unset($_GET["historique"]);
            } else {
                // On recupère le dernier bloc d'informations de l'établissement
                $informations = $this->DB_etablissement->getInformations( $this->_request->id );

                // On vide les tables des plans / rubrique / secondaire
                foreach ($DB_tab as $key => $tab) {
                    if ( !in_array($key, array("ID_FILS_ETABLISSEMENT", "NUMERO_ADRESSE")) ) {
                        $tab->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    } else {
                        $tab->delete( "ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                    }
                }
            }

            // On met à  0 les checkbox, elles seront checkées si dans le formulaire elles le sont
            $informations->LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS = 0;
            $informations->ICPE_ETABLISSEMENTINFORMATIONS = 0;
            $informations->R12320_ETABLISSEMENTINFORMATIONS = 0;
            $informations->EXTINCTIONAUTO_ETABLISSEMENTINFORMATIONS = 0;
            $informations->SCHEMAMISESECURITE_ETABLISSEMENTINFORMATIONS = 0;
            $informations->ID_STATUT = null;
            $informations->ID_GENRE = 1;
            $informations->ID_CLASSE = null;

            $_GET['NBPREV_ETABLISSEMENT'] = isset($_GET['NBPREV_ETABLISSEMENT']) ? (int) $_GET['NBPREV_ETABLISSEMENT'] : 0;
            $_GET['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'] = isset($_GET['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS']) ? (int) $_GET['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'] : 0;
            $_GET['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'] = isset($_GET['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS']) ? (int) $_GET['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'] : 0;
            $_GET['EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS'] = isset($_GET['EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS']) ? (int) $_GET['EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS'] : 0;
            $_GET['EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS'] = isset($_GET['EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS']) ? (int) $_GET['EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS'] : 0;

            // Sauvegarde de la base pour récupérer les id
            $etablissement->save();
            $informations->ID_ETABLISSEMENT = $etablissement->ID_ETABLISSEMENT;
            $informations->save();

            // On populate les informations
            foreach ($_GET as $key => $data) {
                // Données non historisées
                if ( in_array($key, array("NUMEROID_ETABLISSEMENT", "TELEPHONE_ETABLISSEMENT", "FAX_ETABLISSEMENT", "COURRIEL_ETABLISSEMENT", "NUMEROID_ETABLISSEMENT", "NBPREV_ETABLISSEMENT", "DUREEVISITE_ETABLISSEMENT")) ) {
                    $etablissement->$key = $data;
                }
                // Données historisées
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
                    // est ce que l'on veut ajouter une ligne dans la bonne table ?
                    if ( array_key_exists($key, $DB_tab) ) {
                        // on parcourt les données du tableau envoyé
                        for ( $i=0; $i<count($data); $i++ ) {
                            // Création de la ligne
                            $item = $DB_tab[$key]->createRow();

                            foreach ( $DB_tab[$key]->info(Zend_Db_Table_Abstract::COLS) as $col ) {
                                if ( isset($_GET[$col][$i]) ) {
                                    // On check si lenfant correspondant bien a l'établissement actuel
                                    if ($key == "ID_FILS_ETABLISSEMENT") {
                                        $genre_actuel = $_GET["ID_GENRE"];
                                        $genre_enfant = $this->DB_etablissement->getInformations($_GET[$col][$i])->ID_GENRE;

                                        switch ($genre_actuel) {
                                            case 1:
                                                if($genre_enfant != 2 && $genre_enfant != 4 && $genre_enfant != 5 && $genre_enfant != 6)
                                                    throw new Exception('L\'établissement enfant n\'est pas compatible (Un Site ne ne peut contenir que des établissements)', 500);
                                                break;

                                            case 2:
                                                if($genre_enfant != 3)
                                                    throw new Exception('L\'établissement enfant n\'est pas compatible (Un établissement ne ne peut contenir que des cellules)', 500);
                                                break;

                                            default:
                                                if($genre_enfant != null)
                                                    throw new Exception('L\'établissement enfant n\'est pas compatible', 500);
                                        }
                                    }

                                    $item->$col = $col == "DATE_ETABLISSEMENTPLAN" ? $this->getDate($_GET[$col][$i]) : $_GET[$col][$i];
                                }
                            }

                            // Ajout de la clé étrangère
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

            // On spécifie le père
            if ($this->_request->ID_PERE != "") {

                $DB_tab["ID_FILS_ETABLISSEMENT"]->delete("ID_FILS_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                $pere_information = $this->DB_etablissement->getInformations($this->_request->ID_PERE);

                // on test si le père peut être enregistré (genre)
                // si l'établissement = site alors pas de pere
                // si l'établissement = cellule alos père = etablissement
                switch ($informations->ID_GENRE) {
                    case 2:
                        if($pere_information->ID_GENRE != 1)
                            throw new Exception('Le père n\'est pas compatible (Un établissement a comme père un site)', 500);
                        break;
                    case 3:
                        if($pere_information->ID_GENRE != 2)
                            throw new Exception('Le père n\'est pas compatible (Les cellules ont comme père un établissement)', 500);
                        break;
                    default:
                        if($this->_request->ID_PERE !=null)
                            throw new Exception('Le père n\'est pas compatible (Les sites, habitation, IGH et EIC n\'ont pas de père)', 500);
                        break;
                }

                $item = $DB_tab["ID_FILS_ETABLISSEMENT"]->createRow();
                $item->ID_ETABLISSEMENT = (int) $this->_request->ID_PERE;
                $item->ID_FILS_ETABLISSEMENT = $etablissement->ID_ETABLISSEMENT;
                $item->save();

            } else {
                $DB_tab["ID_FILS_ETABLISSEMENT"]->delete("ID_FILS_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
            }

            $db->commit();

            // On donne le lien vers l'Ã©tablissement pour le rechargement
            if ($etablissement->ID_ETABLISSEMENT) {
                $this->view->url = "/etablissement/index/id/".$etablissement->ID_ETABLISSEMENT."?confirm=1";
            }

            // On update les cat et la perio des celulles enfants si il y en a
            $this->DB_etablissement->recalcEnfants($etablissement->ID_ETABLISSEMENT, $informations->ID_ETABLISSEMENTINFORMATIONS, $historique);

        } catch (Exception $e) {
            $this->_helper->viewRenderer->setNoRender();
            $db->rollBack();
            $this->view->error = $e->getMessage();
            //throw $e;
        }
    }

    public function getDefaultValues($data)
    {
        $results = array();

        $results["ID_CATEGORIE"] = $this->repository->getDefaultCategorie($data);

        if ($results["ID_CATEGORIE"] != null) {
            $results["ID_CATEGORIE"] = $this->view->categorie;
        }

        $results['periodicite'] = $this->repository->getDefaultPeriodicite($data);
        $results['commission'] = $this->repository->getDefaultCommission($data);
        $results['preventioniste'] = $this->repository->getDefaultPrev($data);

        return $results;
    }


    public function getAllPJ($id)
    {

    }

    public function savePJ($data)
    {

    }

    public function deletePJ($data)
    {

    }


    public function getAllContacts($id)
    {

    }

    public function saveContact($data)
    {

    }

    public function deleteContact($data)
    {

    }

    // Récupération de tous les textes applicables qui ont été cochés
    public function getAllTextesApplicables($id_etablissement)
    {
        $etsTexteApplicable = new Model_DbTable_EtsTextesAppl;
        return $etsTexteApplicable->recupTextes($id_etablissement);
    }

    public function saveTextesApplicables($id_etablissement, $textes_applicables)
    {
        $etsTexteApplicable = new Model_DbTable_EtsTextesAppl;

        foreach($textes_applicables as $id_texte_applicable => $is_active) {
            if(!$is_active) {
                if($etsTexteApplicable->find($id_texte_applicable, $id_etablissement)->current() !== null) {
                    $etsTexteApplicable->find($id_texte_applicable, $id_etablissement)->current()->delete();
                }
            }
            else {
                if($etsTexteApplicable->find($id_texte_applicable, $id_etablissement)->current() === null) {
                    $row = $etsTexteApplicable->createRow();
                    $row->ID_TEXTESAPPL = $id_texte_applicable;
                    $row->ID_ETABLISSEMENT = $id_etablissement;
                    $row->save();
                }
            }

        }
    }

    private function getDate($input)
    {
        $array_date = explode("/", $input);
        if (!is_array($array_date) || count($array_date) != 3) {
            throw new Exception('Erreur dans la date', 500);
        }
        if($array_date[2] != '0000')

            return $array_date[2]."-".$array_date[1]."-".$array_date[0]." 00:00:01";
        else
            return "1970-01-02 00:00:02";
    }

}
