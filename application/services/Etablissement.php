<?php

class Service_Etablissement implements Service_Interface_Etablissement
{
    /**
     * Récupération d'un établissement
     *
     * @param int $id_etablissement
     * @return array
     * @throws Exception si l'établissement n'existe pas
     */
    public function get($id_etablissement)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        if(($etablissement = unserialize($cache->load('etablissement_id_' . $id_etablissement))) === false)
        {
            $model_etablissement = new Model_DbTable_Etablissement;

            $search = new Model_DbTable_Search;

            $DB_rubriques = new Model_DbTable_EtablissementInformationsRubrique;
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            $DB_plans = new Model_DbTable_EtablissementInformationsPlan;
            $DB_genre = new Model_DbTable_Genre;
            $DB_categorie = new Model_DbTable_Categorie;
            $DB_famille = new Model_DbTable_Famille;
            $DB_classe = new Model_DbTable_Classe;
            $DB_type = new Model_DbTable_Type;
            $DB_typeactivite = new Model_DbTable_TypeActivite;
            $DB_commission = new Model_DbTable_Commission;
            $DB_statut = new Model_DbTable_Statut;
            $DB_dossier = new Model_DbTable_Dossier;

            // Récupération de l'établissement
            $general = $model_etablissement->find($id_etablissement)->current();

            // Si l'établissement n'existe pas, on généère une erreur
            if($general === null) {
                throw new Exception("L'établissement n'existe pas.");
            }

            // On récupère la dernière fiche d'informations de l'établissement
            $informations = $model_etablissement->getInformations($id_etablissement);

            // Récupération des parents de l'établissement
            $results = array();
            $id_enfant = $id_etablissement;
            do {
                $parent = $model_etablissement->getParent($id_enfant);
                if ($parent != null) {
                    $results[] = $parent;
                    $id_enfant = $parent["ID_ETABLISSEMENT"];
                }
            } while($parent != null);
            $etablissement_parents = count($results) == 0 ? array() : array_reverse($results);

            // Récupération de l'avis de l'établissement + dates de VP
            $avis = null;
            $last_visite = null;
            $next_visite = null;
            if ($general->ID_DOSSIER_DONNANT_AVIS != null) {
                $dossier_donnant_avis = $DB_dossier->find($general->ID_DOSSIER_DONNANT_AVIS)->current();
                $avis = $dossier_donnant_avis->AVIS_DOSSIER_COMMISSION;
                $tmp_date = new Zend_Date($dossier_donnant_avis->DATEVISITE_DOSSIER, Zend_Date::DATES);
                $last_visite =  $tmp_date->get( Zend_Date::MONTH_NAME." ".Zend_Date::YEAR );

                if($informations->PERIODICITE_ETABLISSEMENTINFORMATIONS != 0) {
                    $tmp_date = new Zend_Date($tmp_date->get( Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR ), Zend_Date::DATES);
                    $tmp_date->add($informations->PERIODICITE_ETABLISSEMENTINFORMATIONS, Zend_Date::MONTH);
                    $next_visite =  $tmp_date->get(Zend_Date::MONTH_NAME." ".Zend_Date::YEAR );
                }
            }

            // récupération de la date de PC initial
            $pc_inital = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("d.TYPE_DOSSIER", 1)->setCriteria("ID_NATURE", 1)->order('DATEINSERT_DOSSIER ASC')->run();
            $pc_inital = $pc_inital->getAdapter()->getItems(0, 1)->toArray();
            if(count($pc_inital) == 1) {
                $tmp_date = new Zend_Date($pc_inital[0]['DATEINSERT_DOSSIER'], Zend_Date::DATES);
                $pc_inital =  $tmp_date->get( Zend_Date::DAY . " " . Zend_Date::MONTH_NAME." ".Zend_Date::YEAR );
            }
            else {
                $pc_inital = null;
            }

            $etablissement = array(
                'general' => $general->toArray(),
                'informations' => array_merge($informations->toArray(), array(
                    "LIBELLE_GENRE" => $DB_genre->find($informations->ID_GENRE)->current()->LIBELLE_GENRE,
                    "LIBELLE_CATEGORIE" => @$DB_categorie->find($informations->ID_CATEGORIE)->current()->LIBELLE_CATEGORIE,
                    "LIBELLE_FAMILLE" => @$DB_famille->find($informations->ID_FAMILLE)->current()->LIBELLE_FAMILLE,
                    "LIBELLE_CLASSE" => @$DB_classe->find($informations->ID_CLASSE)->current()->LIBELLE_CLASSE,
                    "LIBELLE_TYPE_PRINCIPAL" => @$DB_type->find($informations->ID_TYPE)->current()->LIBELLE_TYPE,
                    "LIBELLE_TYPEACTIVITE_PRINCIPAL" => @$DB_typeactivite->find($informations->ID_TYPEACTIVITE)->current()->LIBELLE_ACTIVITE,
                    "LIBELLE_COMMISSION" => @$DB_commission->find($informations->ID_COMMISSION)->current()->LIBELLE_COMMISSION,
                    "LIBELLE_STATUT" => @$DB_statut->find($informations->ID_STATUT)->current()->LIBELLE_STATUT,
                )),
                'parents' => $etablissement_parents,
                'avis' => $avis,
                'last_visite' => $last_visite,
                'next_visite' => $next_visite,
                'pc_initial' => $pc_inital,
                'plans' => $model_etablissement->getPlansInformations($informations->ID_ETABLISSEMENTINFORMATIONS),
                'diapo_plans' => $model_etablissement->getPlans($id_etablissement),
                'diapo' => $model_etablissement->getDiaporama($id_etablissement),
                'types_activites_secondaires' => $model_etablissement->getTypesActivitesSecondaires($informations->ID_ETABLISSEMENTINFORMATIONS),
                'rubriques' => $DB_rubriques->fetchAll("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS, "ID_ETABLISSEMENTINFORMATIONSRUBRIQUE")->toArray(),
                'etablissement_lies' => $search->setItem("etablissement")->setCriteria("etablissementlie.ID_ETABLISSEMENT", $id_etablissement)->order("LIBELLE_ETABLISSEMENTINFORMATIONS")->run()->getAdapter()->getItems(0, 99999999999)->toArray(),
                'preventionnistes' => $search->setItem("utilisateur")->setCriteria("etablissementinformations.ID_ETABLISSEMENT", $id_etablissement)->run()->getAdapter()->getItems(0, 99999999999)->toArray(),
                'adresses' => $DB_adresse->get($id_etablissement)
            );

            // On stocke en cache
            $cache->save(serialize($etablissement));
        }

        return $etablissement;
    }

    /**
     * Récupération de l'historique d'un établissement
     *
     * @param int $id_etablissement
     * @return array
     */
    public function getHistorique($id_etablissement)
    {
        $historique = array();

        $DB_information = new Model_DbTable_EtablissementInformations;
        $DB_categorie = new Model_DbTable_Categorie;					
        $DB_famille = new Model_DbTable_Famille;						
        $DB_type = new Model_DbTable_Type;						        
        $DB_classe = new Model_DbTable_Classe;							
        $DB_utilisateurs = new Model_DbTable_Utilisateur;
        $DB_utilisateursInfo = new Model_DbTable_UtilisateurInformations;
        $DB_statut = new Model_DbTable_Statut;

        $categories = $DB_categorie->fetchAll()->toArray();
        $familles = $DB_famille->fetchAll()->toArray();
        $types = $DB_type->fetchAll()->toArray();
        $classes = $DB_classe->fetchAll()->toArray();
        $statuts = $DB_statut->fetchAll()->toArray();

        // On récupère toutes les fiches de l'établissement
        $fiches = $DB_information->fetchAll("ID_ETABLISSEMENT = " . $id_etablissement, "DATE_ETABLISSEMENTINFORMATIONS")->toArray();

        // On traite le tout
        foreach ($fiches as $fiche) {
            foreach ($fiche as $key => $item) {
                $tmp = ( array_key_exists($key, $historique) ) ? $historique[$key][ count($historique[$key])-1 ] : null;
                $value = null;
                switch ($key) {
                    case "LIBELLE_ETABLISSEMENTINFORMATIONS":
                        $value = $item;
                        break;
                    case "ID_STATUT":
                        $value = $statuts[$item - 1]["LIBELLE_STATUT"];
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
                    $author = $DB_utilisateursInfo->fetchRow("ID_UTILISATEURINFORMATIONS = " . $DB_utilisateurs->find($fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"])->current()->ID_UTILISATEURINFORMATIONS)->toArray();
                    $historique[$key][] = array(
                        "valeur" => $value,
                        "debut" =>  $date->get( Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR ),
                        "author" => $fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"] == 0 ? null : array('id' => $fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"], 'name' => $author['NOM_UTILISATEURINFORMATIONS'] . ' ' . $author['PRENOM_UTILISATEURINFORMATIONS'])
                    );
                }
            }
        }

        // On met ds le sens aujourd'hui -> passé
        foreach ($historique as $key => $item) {
            $historique[$key] = array_reverse($item);
        }

        return $historique;
    }

    /**
     * Récupération des dossiers d'un établissement
     *
     * @param int $id_etablissement
     * @return array
     */
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
        $results = array();
        $results['etudes'] = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("d.TYPE_DOSSIER", 1)->order("COALESCE(DATECOMM_DOSSIER,DATEINSERT_DOSSIER) DESC")->run()->getAdapter()->getItems(0, 999999)->toArray();
        $results['visites'] = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("d.TYPE_DOSSIER", array(2, 3))->order("DATEVISITE_DOSSIER,COALESCE(DATECOMM_DOSSIER,DATEINSERT_DOSSIER) DESC")->run()->getAdapter()->getItems(0, 999999)->toArray();
        $results['autres'] = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("d.TYPE_DOSSIER", $types_autre)->order("DATEINSERT_DOSSIER DESC")->run()->getAdapter()->getItems(0, 999999)->toArray();
        return $results;
    }

    /**
     * Récupération des descriptifs d'un établissement
     *
     * @param int $id_etablissement
     * @return array
     */
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

    /**
     * Sauvegarde des descriptifs d'un établissement
     *
     * @param int $id_etablissement
     * @param string $historique
     * @param string $descriptif
     * @param string $derogations
     * @param array $descriptifs_techniques
     */
    public function saveDescriptifs($id_etablissement, $historique, $descriptif, $derogations, array $descriptifs_techniques)
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

    /**
     * On cherche l'ensemble des établissements correspondant au libellé donné qui satisfassent à la contrainte d'être enfant (ou parent) du genre donné
     *
     * @param string $libelle
     * @param int $id_genre
     * @param bool $enfants Optionnel
     * @return array
     */
    public function findAll($libelle, $id_genre, $enfants = true)
    {
        // Création de l'objet recherche
        $search = new Model_DbTable_Search;

        // On set le type de recherche
        $search->setItem("etablissement");
        $search->limit(5);

        // On recherche avec le libellé
        $search->setCriteria("LIBELLE_ETABLISSEMENTINFORMATIONS", $libelle, false);

        // On filtre par le genre
        if (!$enfants) {
            if($id_genre == 2)
                $search->setCriteria("etablissementinformations.ID_GENRE", 1);
            elseif($id_genre == 3)
                $search->setCriteria("etablissementinformations.ID_GENRE", 2);
        }

        if ($enfants) {
            if($id_genre == 1)
                $search->setCriteria("etablissementinformations.ID_GENRE", array(2,4,5,6));
            elseif($id_genre == 2)
                $search->setCriteria("etablissementinformations.ID_GENRE", 3);
        }

        return $search->run()->getAdapter()->getItems(0, 99999999999)->toArray();
    }

    /**
     * On vérifie si une fiche existe à la date donnée pour l'établissement
     *
     * @param int $id_etablissement
     * @param string $date format Y-m-d
     * @return array
     */
    public function ficheExiste($id_etablissement, $date)
    {
        $DB_information = new Model_DbTable_EtablissementInformations;

        return (null != ($row = $DB_information->fetchRow("ID_ETABLISSEMENT = '" .  $id_etablissement . "' AND DATE_ETABLISSEMENTINFORMATIONS = '" . $date . "'"))) ? true : false;
    }

    /**
     * Sauvegarde d'un établissement
     *
     * @param int $id_genre
     * @param array $data
     * @param int $id_etablissement Optionnel
     * @param int $date Optionnel format : Y-m-d
     * @return int $id_etablissement
     * @throws Exception Si une erreur apparait lors de la sauvegarde
     */
    public function save($id_genre, array $data, $id_etablissement = null, $date = '')
    {
        $DB_etablissement = new Model_DbTable_Etablissement;
        $DB_informations = new Model_DbTable_EtablissementInformations;
        $DB_plans = new Model_DbTable_EtablissementInformationsPlan;
        $DB_rubrique = new Model_DbTable_EtablissementInformationsRubrique;
        $DB_types_activites_secondaires = new Model_DbTable_EtablissementInformationsTypesActivitesSecondaires;
        $DB_etablissements_lies = new Model_DbTable_EtablissementLie;
        $DB_preventionniste = new Model_DbTable_EtablissementInformationsPreventionniste;
        $DB_adresse = new Model_DbTable_EtablissementAdresse;

        // On commence la transaction
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        try {
            $etablissement = $id_etablissement == null ? $DB_etablissement->createRow() : $DB_etablissement->find($id_etablissement)->current();

            if($date == '') {
                $informations = $DB_informations->createRow(array('DATE_ETABLISSEMENTINFORMATIONS' => date('Y-m-d')));
            }
            else {
                $information_a_la_date_donnee = $DB_informations->fetchRow("ID_ETABLISSEMENT = '" .  $id_etablissement . "' AND DATE_ETABLISSEMENTINFORMATIONS = '" . $date . "'");

                if($information_a_la_date_donnee != null) {
                    $informations = $information_a_la_date_donnee;
                    $DB_plans->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    $DB_rubrique->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    $DB_types_activites_secondaires->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    $DB_preventionniste->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    $DB_etablissements_lies->delete("ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                    $DB_adresse->delete("ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                }
                else {
                    $informations = $DB_informations->createRow(array('DATE_ETABLISSEMENTINFORMATIONS' => $date));
                }
            }

            // Sauvegarde des champs d'établissement (non historisés) en fonction
            $etablissement->NUMEROID_ETABLISSEMENT = $data['NUMEROID_ETABLISSEMENT'];
            $etablissement->TELEPHONE_ETABLISSEMENT = $data['TELEPHONE_ETABLISSEMENT'];
            $etablissement->FAX_ETABLISSEMENT = $data['FAX_ETABLISSEMENT'];
            $etablissement->COURRIEL_ETABLISSEMENT = $data['COURRIEL_ETABLISSEMENT'];
            $etablissement->NBPREV_ETABLISSEMENT = (int) $data['NBPREV_ETABLISSEMENT'];
            $etablissement->DUREEVISITE_ETABLISSEMENT = $data['DUREEVISITE_ETABLISSEMENT'];
            $etablissement->save();

            // Sauvegarde des champs de la fiche d'informations en fonction du genre
            $informations->ICPE_ETABLISSEMENTINFORMATIONS = $informations->PERIODICITE_ETABLISSEMENTINFORMATIONS = 
            $informations->R12320_ETABLISSEMENTINFORMATIONS = $informations->LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS = 
            $informations->ID_CLASSE = $informations->ID_FAMILLE =  $informations->ID_CATEGORIE = $informations->ID_TYPE =
            $informations->ID_TYPEACTIVITE = $informations->ID_COMMISSION = $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS = 
            $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = $informations->EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS = 
            $informations->EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS = null;

            switch($id_genre) {
                // Établissement
                case 2:
                    $informations->ID_CATEGORIE = $data['ID_CATEGORIE'];
                    $informations->PERIODICITE_ETABLISSEMENTINFORMATIONS = (int) $data['PERIODICITE_ETABLISSEMENTINFORMATIONS'];
                    $informations->ID_TYPE = $data['ID_TYPE'];
                    $informations->ID_TYPEACTIVITE = $data['ID_TYPEACTIVITE'];
                    $informations->R12320_ETABLISSEMENTINFORMATIONS = (int) $data['R12320_ETABLISSEMENTINFORMATIONS'];
                    $informations->LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS = (int) $data['LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS = $informations->LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS ? (int) $data['EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS'] : null;
                    $informations->EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS = $data['ID_CATEGORIE'] == 5 ? (int) $data['EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS'] : null;
                    $informations->ID_COMMISSION = $data['ID_COMMISSION'];
                    break;

                // Cellule
                case 3:
                    $informations->ID_CATEGORIE = $data['ID_CATEGORIE'];
                    $informations->PERIODICITE_ETABLISSEMENTINFORMATIONS = (int) $data['PERIODICITE_ETABLISSEMENTINFORMATIONS'];
                    $informations->ID_TYPE = $data['ID_TYPE'];
                    $informations->ID_TYPEACTIVITE = $data['ID_TYPEACTIVITE'];
                    $informations->R12320_ETABLISSEMENTINFORMATIONS = (int) $data['R12320_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS = $data['ID_CATEGORIE'] == 5 ? (int) $data['EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS'] : null;
                    break;

                // Habitation
                case 4:
                    $informations->ID_FAMILLE = $data['ID_FAMILLE'];
                    break;

                // IGH
                case 5:
                    $informations->ID_CLASSE = $data['ID_CLASSE'];
                    $informations->PERIODICITE_ETABLISSEMENTINFORMATIONS = (int) $data['PERIODICITE_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    $informations->ID_COMMISSION = $data['ID_COMMISSION'];
                    break;

                // EIC
                case 6:
                    $informations->ICPE_ETABLISSEMENTINFORMATIONS = (int) $data['ICPE_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    break;
            }

            $informations->LIBELLE_ETABLISSEMENTINFORMATIONS = $data['LIBELLE_ETABLISSEMENTINFORMATIONS'];
            $informations->ID_GENRE = $id_genre;
            $informations->ID_STATUT = $data['ID_STATUT'];
            $informations->UTILISATEUR_ETABLISSEMENTINFORMATIONS = Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR;
            $informations->ID_ETABLISSEMENT = $etablissement->ID_ETABLISSEMENT;

            $informations->save();

            // Sauvegarde des préventionnistes
            if(array_key_exists('ID_UTILISATEUR', $data) && count($data['ID_UTILISATEUR']) > 0) {
                foreach($data['ID_UTILISATEUR'] as $id_preventionniste) {
                    $DB_preventionniste->createRow(array(
                        "ID_ETABLISSEMENTINFORMATIONS" => $informations->ID_ETABLISSEMENTINFORMATIONS,
                        "ID_UTILISATEUR" => $id_preventionniste
                    ))->save();
                }
            }

            // Sauvegarde des rubriques pour les EIC
            if($id_genre == 6 && array_key_exists('RUBRIQUES', $data) && count($data['RUBRIQUES']) > 0) {
                foreach($data['RUBRIQUES'] as $rubrique) {
                    $DB_rubrique->createRow(array(
                        "ID_RUBRIQUE" => $rubrique["ID_RUBRIQUE"],
                        "NUMERO_ETABLISSEMENTINFORMATIONSRUBRIQUE" => (int) $rubrique["NUMERO_ETABLISSEMENTINFORMATIONSRUBRIQUE"],
                        "VALEUR_ETABLISSEMENTINFORMATIONSRUBRIQUE" => (double) $rubrique["VALEUR_ETABLISSEMENTINFORMATIONSRUBRIQUE"],
                        "NOM_ETABLISSEMENTINFORMATIONSRUBRIQUE" => $rubrique["NOM_ETABLISSEMENTINFORMATIONSRUBRIQUE"],
                        "ID_ETABLISSEMENTINFORMATIONS" => $informations->ID_ETABLISSEMENTINFORMATIONS,
                        "CLASSEMENT_ETABLISSEMENTINFORMATIONSRUBRIQUE" => $rubrique["CLASSEMENT_ETABLISSEMENTINFORMATIONSRUBRIQUE"]
                    ))->save();
                }
            }

            // Sauvegarde des plans en fonction du genre
            if(in_array($id_genre, array(2, 3, 5 ,6)) && array_key_exists('PLANS', $data) && count($data['PLANS']) > 0) {
                foreach($data['PLANS'] as $plan) {
                    $DB_plans->createRow(array(
                        "ID_ETABLISSEMENTINFORMATIONS" => $informations->ID_ETABLISSEMENTINFORMATIONS,
                        "NUMERO_ETABLISSEMENTPLAN" => $plan["NUMERO_ETABLISSEMENTPLAN"],
                        "DATE_ETABLISSEMENTPLAN" => $plan["DATE_ETABLISSEMENTPLAN"],
                        "MISEAJOUR_ETABLISSEMENTPLAN" => $plan["MISEAJOUR_ETABLISSEMENTPLAN"],
                        "ID_TYPEPLAN" => $plan["ID_TYPEPLAN"]
                    ))->save();
                }
            }

            // Sauvegarde des types et activités secondaires en fonction du genre
            if(in_array($id_genre, array(2, 3)) && array_key_exists('TYPES_ACTIVITES_SECONDAIRES', $data) && count($data['TYPES_ACTIVITES_SECONDAIRES']) > 0) {
                foreach($data['TYPES_ACTIVITES_SECONDAIRES'] as $type_activite_secondaire) {
                    $DB_types_activites_secondaires->createRow(array(
                        "ID_ETABLISSEMENTINFORMATIONS" => $informations->ID_ETABLISSEMENTINFORMATIONS,
                        "ID_TYPE_SECONDAIRE" => $type_activite_secondaire["ID_TYPE_SECONDAIRE"],
                        "ID_TYPEACTIVITE_SECONDAIRE" => $type_activite_secondaire["ID_TYPEACTIVITE_SECONDAIRE"]
                    ))->save();
                }
            }

            // Sauvegarde des adresses en fonction du genre
            if(in_array($id_genre, array(2, 4, 5, 6)) && array_key_exists('ADRESSES', $data) && count($data['ADRESSES']) > 0) {
                foreach($data['ADRESSES'] as $adresse) {
                    $DB_adresse->createRow(array(
                        "NUMERO_ADRESSE" => $adresse["NUMERO_ADRESSE"],
                        "COMPLEMENT_ADRESSE" => $adresse["COMPLEMENT_ADRESSE"],
                        "LON_ETABLISSEMENTADRESSE" => $adresse["LON_ETABLISSEMENTADRESSE"],
                        "LAT_ETABLISSEMENTADRESSE" => $adresse["LAT_ETABLISSEMENTADRESSE"],
                        "ID_ETABLISSEMENT" => $etablissement->ID_ETABLISSEMENT,
                        "ID_RUE" => $adresse["ID_RUE"],
                        "NUMINSEE_COMMUNE" => $adresse["NUMINSEE_COMMUNE"]
                    ))->save();
                }
            }

            // Sauvegarde des établissements liés
            if(array_key_exists('ID_FILS_ETABLISSEMENT', $data) && count($data['ID_FILS_ETABLISSEMENT']) > 0) {
                foreach($data['ID_FILS_ETABLISSEMENT'] as $id_etablissement_enfant) {
                    $genre_enfant = $DB_etablissement->getInformations($id_etablissement_enfant)->ID_GENRE;
                    if($id_genre == 1 && ($genre_enfant != 2 && $genre_enfant != 4 && $genre_enfant != 5 && $genre_enfant != 6)) {
                        throw new Exception('L\'établissement enfant n\'est pas compatible (Un site ne ne peut contenir que des établissements)', 500);
                    }
                    elseif($id_genre == 2 && ($genre_enfant != 3)) {
                        throw new Exception('L\'établissement enfant n\'est pas compatible (Un établissement ne ne peut contenir que des cellules)', 500);
                    }
                    elseif($genre_enfant == null) {
                        throw new Exception('L\'établissement enfant n\'est pas compatible', 500);
                    }
                    else {
                        $DB_etablissements_lies->createRow(array(
                            "ID_ETABLISSEMENT" => $etablissement->ID_ETABLISSEMENT,
                            "ID_FILS_ETABLISSEMENT" => $id_etablissement_enfant
                        ))->save();
                        $cache->remove('etablissement_id_' . $id_etablissement_enfant);
                    }
                }
            }

            // Sauvegarde du père de l'établissement
            if(array_key_exists("ID_PERE", $data) && !empty($data['ID_PERE'])) {
                $genre_pere = $DB_etablissement->getInformations($data['ID_PERE'])->ID_GENRE;
                
                if($id_genre == 2 && $genre_pere != 1) {
                    throw new Exception('Le père n\'est pas compatible (Un établissement a comme père un site)', 500);
                }
                elseif($id_genre == 3 && $genre_pere != 2) {
                    throw new Exception('Le père n\'est pas compatible (Les cellules ont comme père un établissement)', 500);
                }
                elseif($genre_pere == null) {
                    throw new Exception('Le père n\'est pas compatible (Les sites, habitation, IGH et EIC n\'ont pas de père)', 500);
                }
                else {
                    $DB_etablissements_lies->delete("ID_FILS_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                    $DB_etablissements_lies->createRow(array(
                        "ID_ETABLISSEMENT" => (int) $data['ID_PERE'],
                        "ID_FILS_ETABLISSEMENT" => $etablissement->ID_ETABLISSEMENT
                    ))->save();
                    $cache->remove('etablissement_id_' . (int) $data['ID_PERE']);
                }
            }

            $cache->remove('etablissement_id_' . $id_etablissement);
            $db->commit();
        }
        catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $etablissement->ID_ETABLISSEMENT;
    }

    /**
     * Récupération des valeurs par défauts en fonction du genre et d'autres critères donnés pour un établissement
     *
     * @param int $id_genre
     * @param array $data
     * @return array
     */
    public function getDefaultValues($id_genre, array $data)
    {
        $model_etablissement = new Model_DbTable_Etablissement;

        $results = array();

        $results["categorie"] = $model_etablissement->getDefaultCategorie($data);
        $results['periodicite'] = $model_etablissement->getDefaultPeriodicite($data);
        $results['commission'] = $model_etablissement->getDefaultCommission($data);
        $results['preventioniste'] = $model_etablissement->getDefaultPrev($data);

        return $results;
    }

    /**
     * Récupération des pièces jointes d'un établissement
     *
     * @param int $id_etablissement
     * @return array
     */
    public function getAllPJ($id_etablissement)
    {
        $DBused = new Model_DbTable_PieceJointe;

        return $DBused->affichagePieceJointe("etablissementpj", "etablissementpj.ID_ETABLISSEMENT", $id_etablissement);
    }

    /**
     * Ajout d'une pièce jointe pour un établissement
     *
     * @param int $id_etablissement
     * @param string $file
     * @param string $name
     * @param string $description
     * @param int $mise_en_avant 0 = aucune mise en avant, 1 = diaporama, 2 = plans
     */
    public function addPJ($id_etablissement, $file, $name = '', $description = '', $mise_en_avant = 0)
    {
        $path = APPLICATION_PATH . DS . '..' . DS . 'public' . DS . 'data' . DS . 'uploads' . DS . 'pieces-jointes' . DS;
        $extension = strrchr($file['name'], ".");

        $DBpieceJointe = new Model_DbTable_PieceJointe;

        $nouvellePJ = $DBpieceJointe->createRow(array(
            'EXTENSION_PIECEJOINTE' => $extension,
            'NOM_PIECEJOINTE' => $name == '' ? $file['name'] : $name,
            'DESCRIPTION_PIECEJOINTE' => $description,
            'DATE_PIECEJOINTE' => date('Y-m-d')
        ))->save();

        if(!move_uploaded_file($file['tmp_name'], $path . $nouvellePJ . $extension)) {
            throw new Exception('Ne peut pas déplacer le fichier ' . $file['tmp_name']);
        }
        else {
            $DBsave = new Model_DbTable_EtablissementPj;

            $linkPj = $DBsave->createRow(array(
                'ID_ETABLISSEMENT' => $id_etablissement,
                'ID_PIECEJOINTE' => $nouvellePJ,
                'PLACEMENT_ETABLISSEMENTPJ' => (int) $mise_en_avant != 0 && in_array($extension, array(".jpg", ".jpeg", ".png", ".gif")) ? $mise_en_avant : 0,
            ))->save();

            GD_Resize::run($path . $nouvellePJ . $extension, $path . "miniatures" . DIRECTORY_SEPARATOR . $nouvellePJ . ".jpg", 450);
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $cache->remove('etablissement_id_' . $id_etablissement);
    }

    /**
     * Suppression d'une pièce jointe d'un établissement
     *
     * @param int $id_etablissement
     * @param int $id_pj
     */
    public function deletePJ($id_etablissement, $id_pj)
    {
        $DBpieceJointe = new Model_DbTable_PieceJointe;
        $DBitem = new Model_DbTable_EtablissementPj;

        $path = APPLICATION_PATH . DS . '..' . DS . 'public' . DS . 'data' . DS . 'uploads' . DS . 'pieces-jointes' . DS;

        $pj = $DBpieceJointe->find($id_pj)->current();

        if ($DBitem != null) {
            if( file_exists($path . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE) )                         unlink($path . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE);
            if( file_exists($path . "miniatures" . DS . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE) )     unlink($path . "miniatures" . DS . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE);
            $DBitem->delete("ID_PIECEJOINTE = " . (int) $this->_request->id_pj);
            $pj->delete();
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $cache->remove('etablissement_id_' . $id_etablissement);
    }

    /**
     * Récupération des contacts d'un établissement
     *
     * @param int $id_etablissement
     * @return array
     */
    public function getAllContacts($id_etablissement)
    {
        $DB_contact = new Model_DbTable_UtilisateurInformations;

        return $DB_contact->getContact('etablissement', $id_etablissement);
    }

    /**
     * Ajout d'un contact à un établissement
     *
     * @param int $id_etablissement
     * @param string $nom
     * @param string $prenom
     * @param int $id_fonction
     * @param string $societe
     * @param string $fixe
     * @param string $mobile
     * @param string $fax
     * @param string $email
     * @param string $adresse
     * @param string $web
     */
    public function addContact($id_etablissement, $nom, $prenom, $id_fonction, $societe, $fixe, $mobile, $fax, $email, $adresse, $web)
    {
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $DB_contact = new Model_DbTable_EtablissementContact;

        $id_contact = $DB_informations->createRow(array(
            'NOM_UTILISATEURINFORMATIONS' => (string) $nom,
            'PRENOM_UTILISATEURINFORMATIONS' => (string) $prenom,
            'TELFIXE_UTILISATEURINFORMATIONS' => (string) $fixe,
            'TELPORTABLE_UTILISATEURINFORMATIONS' => (string) $mobile,
            'TELFAX_UTILISATEURINFORMATIONS' => (string) $fax,
            'MAIL_UTILISATEURINFORMATIONS' => (string) $email,
            'SOCIETE_UTILISATEURINFORMATIONS' => (string) $societe,
            'WEB_UTILISATEURINFORMATIONS' => (string) $web,
            'OBS_UTILISATEURINFORMATIONS' => (string) $adresse,
            'ID_FONCTION' => (string) $id_fonction
        ))->save();

        $DB_contact->createRow(array(
            'ID_ETABLISSEMENT' => $id_etablissement,
            'ID_UTILISATEURINFORMATIONS' => $id_contact
        ))->save();
    }

    /**
     * Ajout d'un contact existant à un établissement
     *
     * @param int $id_etablissement
     * @param int $id_contact
     */
    public function addContactExistant($id_etablissement, $id_contact)
    {
        $DB_contact = new Model_DbTable_EtablissementContact;

        $DB_contact->createRow(array(
            'ID_ETABLISSEMENT' => $id_etablissement,
            'ID_UTILISATEURINFORMATIONS' => $id_contact
        ))->save();
    }

    /**
     * Suppression d'un contact
     *
     * @param int $id_etablissement
     * @param int $id_contact
     */
    public function deleteContact($id_etablissement, $id_contact)
    {
        $DB_current = new Model_DbTable_EtablissementContact;
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $DB_contact = array(
            new Model_DbTable_EtablissementContact,
            new Model_DbTable_DossierContact,
            new Model_DbTable_GroupementContact,
            new Model_DbTable_CommissionContact
        );

        // Appartient à d'autre ets ?
        $exist = false;
        foreach ($DB_contact as $key => $model) {
            if (count($model->fetchAll("ID_UTILISATEURINFORMATIONS = " . $id_contact)->toArray()) > (($model == $DB_current) ? 1 : 0) ) {
                $exist = true;
            }
        }

        // Est ce que le contact n'appartient pas à d'autre etablissement ?
        if (!$exist) {
            $DB_current->delete("ID_UTILISATEURINFORMATIONS = " . $id_contact); // Porteuse
            $DB_informations->delete( "ID_UTILISATEURINFORMATIONS = " . $id_contact ); // Contact
        } else {
            $DB_current->delete("ID_UTILISATEURINFORMATIONS = " . $id_contact . " AND ID_ETABLISSEMENT = " . $id_etablissement); // Porteuse
        }
    }

    /**
     * Récupération des textes applicables sur l'établissement
     *
     * @param int $id_etablissement
     * @return array
     */
    public function getAllTextesApplicables($id_etablissement)
    {
        $etsTexteApplicable = new Model_DbTable_EtsTextesAppl;
        return $etsTexteApplicable->recupTextes($id_etablissement);
    }

    /**
     * Sauvegarde des textes applicables sur un établissement
     *
     * @param int $id_etablissement
     * @param array $textes_applicables
     * @return array
     */
    public function saveTextesApplicables($id_etablissement, array $textes_applicables)
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
}
