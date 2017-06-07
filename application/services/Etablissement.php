<?php

class Service_Etablissement implements Service_Interface_Etablissement
{
    const STATUT_CHANGE = 1;
    const CLASSEMENT_CHANGE = 3;
    /**
     * Récupération d'un établissement
     *
     * @param int $id_etablissement
     * @return array
     * @throws Exception si l'établissement n'existe pas
     * @throws Exception si la dernière fiche d'informations n'existe pas
     */
    public function get($id_etablissement)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        if(($etablissement = unserialize($cache->load('etablissement_id_' . $id_etablissement))) === false) {

            $model_etablissement = new Model_DbTable_Etablissement;

            $search = new Model_DbTable_Search;

            $DB_rubriques = new Model_DbTable_EtablissementInformationsRubrique;
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            $DB_genre = new Model_DbTable_Genre;
            $DB_categorie = new Model_DbTable_Categorie;
            $DB_famille = new Model_DbTable_Famille;
            $DB_classe = new Model_DbTable_Classe;
            $DB_classement = new Model_DbTable_Classement;
            $DB_type = new Model_DbTable_Type;
            $DB_typeactivite = new Model_DbTable_TypeActivite;
            $DB_commission = new Model_DbTable_Commission;
            $DB_commission_type = new Model_DbTable_CommissionType;
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

            // Si l'établissement n'existe pas, on généère une erreur
            if($informations === null) {
                throw new Exception("La fiche d'informations de l'établissement n'existe pas.");
            }

            // Récupération des parents de l'établissement
            $results = array();
            $id_enfant = $id_etablissement;
            $parent_direct = null;
            do {
                $parent = $model_etablissement->getParent($id_enfant);
                if ($parent != null) {
                    $results[] = $parent;
                    $id_enfant = $parent["ID_ETABLISSEMENT"];
                    if ($parent_direct == null) {
                        $parent_direct = $parent;
                    }
                }
            } while($parent != null);
            $etablissement_parents = count($results) == 0 ? array() : array_reverse($results);

            // Récupération de l'avis de l'établissement + dates de VP +  Récupération du facteur de dangerosité
            $avis = $facteur_dangerosite = null;
            if ($general->ID_DOSSIER_DONNANT_AVIS != null) {
                $dossier_donnant_avis = $DB_dossier->find($general->ID_DOSSIER_DONNANT_AVIS)->current();
                $avis = $dossier_donnant_avis->AVIS_DOSSIER_COMMISSION;
                $facteur_dangerosite = $dossier_donnant_avis->FACTDANGE_DOSSIER;
            }

            $last_visite = $search->setItem("dossier")
                    // Dossier correspondant à l'établissement dont l'ID est donné
                ->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)
                    // Dossier type "Visite de commission" et "Groupe de visite"
                ->setCriteria("d.TYPE_DOSSIER", array(2,3))
                        // Dossier ayant un avis de commission rendu
                ->setCriteria("d.AVIS_DOSSIER_COMMISSION > 0")
                    // Dossier nature "périodique" et autres types donnant avis de type "Visite de commission" et "Groupe de visite"
                ->setCriteria("ID_NATURE", array(21,26,47,48))
                ->order('DATEVISITE_DOSSIER DESC')
                ->limit(1)
                ->run(false, null, false)->toArray();

            $next_visite = null;

            if ($last_visite !== null && count($last_visite) > 0){
                if ($last_visite[0]['DATEVISITE_DOSSIER'] !== null) {
                    $tmp_date = new Zend_Date($last_visite[0]['DATEVISITE_DOSSIER'], Zend_Date::DATES);
                    $last_visite =  $tmp_date->get( Zend_date::DAY." ".Zend_Date::MONTH_NAME." ".Zend_Date::YEAR );

                    if($informations->PERIODICITE_ETABLISSEMENTINFORMATIONS != 0) {
                        $tmp_date = new Zend_Date($tmp_date->get( Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR ), Zend_Date::DATES);
                        $tmp_date->add($informations->PERIODICITE_ETABLISSEMENTINFORMATIONS, Zend_Date::MONTH);
                        $next_visite =  $tmp_date->get(Zend_Date::MONTH_NAME." ".Zend_Date::YEAR );
                    }    
                } else {
                    $last_visite = null;
                }
            }


            // Récupération de la date de PC initial
            $pc_inital = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("d.TYPE_DOSSIER", 1)->setCriteria("ID_NATURE", 1)->order('DATEINSERT_DOSSIER ASC')->run();
            $pc_inital = $pc_inital->getAdapter()->getItems(0, 1)->toArray();
            if(count($pc_inital) == 1) {
                $tmp_date = new Zend_Date($pc_inital[0]['DATEINSERT_DOSSIER'], Zend_Date::DATES);
                $pc_inital =  $tmp_date->get( Zend_Date::DAY . " " . Zend_Date::MONTH_NAME." ".Zend_Date::YEAR );
            }
            else {
                $pc_inital = null;
            }

            // Y'a t'il un dossier avec avis différé sur l'établissement ?
            $dossiers_avis_diff = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("DIFFEREAVIS_DOSSIER", 1)->run();
            $presence_avis_differe = count($dossiers_avis_diff) > 0;

            // Y'a t'il un dossier avec avis différé sur l'établissement ?
            $dossiers_echeancier = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("dossiernature.ID_NATURE", 46)->run();
            $presence_echeancier = count($dossiers_echeancier) > 0;

            // Récupération des établissements liés
            $etablissement_lies = $search->setItem("etablissement")->setCriteria("etablissementlie.ID_ETABLISSEMENT", $id_etablissement)->order("LIBELLE_ETABLISSEMENTINFORMATIONS")->run()->getAdapter()->getItems(0, 500)->toArray();

            // Récupération de l'indicateur de présence d'un DUS
            $contacts_dus = array();
            $contacts = array_merge(array(), $this->getAllContacts($id_etablissement));
            foreach($etablissement_parents as $etablissement_parent) {
                $contacts = array_merge($contacts, $this->getAllContacts($etablissement_parent['ID_ETABLISSEMENT']));
            }
            foreach($contacts as $contact) {
                if($contact['ID_FONCTION'] == 8) {
                    $contacts_dus[] = $contact;
                }
            }

            // Chargement des données pratiques
            if($informations->ID_GENRE == 1) {
                $duree_totale = 0;
                // Calcul de la durée totale de visite
                foreach($etablissement_lies as $etablissement) {
                    if($etablissement['DUREEVISITE_ETABLISSEMENT'] != null) {
                        $date_zero = new Datetime('00:00:00');
                        $duree_etablissement = new \Datetime($etablissement['DUREEVISITE_ETABLISSEMENT']);
                        $duree_etablissement_en_heure = $duree_etablissement->format('U') - $date_zero->format('U');
                        $duree_totale += (int) $etablissement['NBPREV_ETABLISSEMENT'] * $duree_etablissement_en_heure;
                    }
                }

                $duree_totale = gmdate("H:i:s", $duree_totale);

                $donnees_pratiques = array(
                    'NBPREV_ETABLISSEMENT' => null,
                    'DUREEVISITE_ETABLISSEMENT' => $duree_totale
                );
            }
            else if ($informations->ID_GENRE == 3 && $parent_direct) {
                // la catégorie d'une cellule est celle de l'établissement parent
                $informations->ID_CATEGORIE = $parent_direct['ID_CATEGORIE'];
                
                $donnees_pratiques = array(
                    'NBPREV_ETABLISSEMENT' => $general->NBPREV_ETABLISSEMENT,
                    'DUREEVISITE_ETABLISSEMENT' => $general->DUREEVISITE_ETABLISSEMENT
                );
            }
            else {
                $donnees_pratiques = array(
                    'NBPREV_ETABLISSEMENT' => $general->NBPREV_ETABLISSEMENT,
                    'DUREEVISITE_ETABLISSEMENT' => $general->DUREEVISITE_ETABLISSEMENT
                );
            }

            // Periodicite
            if($informations->ID_GENRE == 1) {
                foreach($etablissement_lies as $etablissement) {
                    if($etablissement['ID_GENRE'] != 2) {
                        continue;
                    } else if ($etablissement['PERIODICITE_ETABLISSEMENTINFORMATIONS'] === null
                            || $etablissement['PERIODICITE_ETABLISSEMENTINFORMATIONS'] === 0) {
                        continue;
                    } else if($etablissement['ID_STATUT'] != 2) {
                        continue;
                    }
                    
                    if ($informations['PERIODICITE_ETABLISSEMENTINFORMATIONS'] === null) {
                        $informations['PERIODICITE_ETABLISSEMENTINFORMATIONS'] = $etablissement['PERIODICITE_ETABLISSEMENTINFORMATIONS'];
                    } else if ($informations['PERIODICITE_ETABLISSEMENTINFORMATIONS'] > $etablissement['PERIODICITE_ETABLISSEMENTINFORMATIONS']) {
                        $informations['PERIODICITE_ETABLISSEMENTINFORMATIONS'] = $etablissement['PERIODICITE_ETABLISSEMENTINFORMATIONS'];
                    }
                    
                }
            } else if ($informations->ID_GENRE == 3 && $etablissement_parents) {
                $informations['PERIODICITE_ETABLISSEMENTINFORMATIONS'] = end($etablissement_parents)['PERIODICITE_ETABLISSEMENTINFORMATIONS'];
            }
            
            
            $commission = @$DB_commission->find($informations->ID_COMMISSION)->current();
            $etablissement = array(
                'general' => $general->toArray(),
                'informations' => array_merge($informations->toArray(), array(
                    "LIBELLE_GENRE" => $DB_genre->find($informations->ID_GENRE)->current()->LIBELLE_GENRE,
                    "LIBELLE_CATEGORIE" => @$DB_categorie->find($informations->ID_CATEGORIE)->current()->LIBELLE_CATEGORIE,
                    "LIBELLE_FAMILLE" => @$DB_famille->find($informations->ID_FAMILLE)->current()->LIBELLE_FAMILLE,
                    "LIBELLE_CLASSE" => @$DB_classe->find($informations->ID_CLASSE)->current()->LIBELLE_CLASSE,
                    "LIBELLE_CLASSEMENT" => @$DB_classement->find($informations->ID_CLASSEMENT)->current()->LIBELLE_CLASSEMENT,
                    "LIBELLE_TYPE_PRINCIPAL" => @$DB_type->find($informations->ID_TYPE)->current()->LIBELLE_TYPE,
                    "LIBELLE_TYPEACTIVITE_PRINCIPAL" => @$DB_typeactivite->find($informations->ID_TYPEACTIVITE)->current()->LIBELLE_ACTIVITE,
                    "LIBELLE_COMMISSION" => $commission == null ? null : $commission->LIBELLE_COMMISSION,
                    "LIBELLE_COMMISSION_TYPE" => @$DB_commission_type->find($commission->ID_COMMISSIONTYPE)->current()->LIBELLE_COMMISSIONTYPE,
                    "LIBELLE_STATUT" => @$DB_statut->find($informations->ID_STATUT)->current()->LIBELLE_STATUT,
                )),
                'presence_avis_differe' => $presence_avis_differe,
                'presence_echeancier' => $presence_echeancier,
                'facteur_dangerosite' => $facteur_dangerosite,
                'donnees_pratiques' => $donnees_pratiques,
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
                'etablissement_lies' => $etablissement_lies,
                'preventionnistes' => $search->setItem("utilisateur")->setCriteria("etablissementinformations.ID_ETABLISSEMENT", $id_etablissement)->run()->getAdapter()->getItems(0, 50)->toArray(),
                'adresses' => $DB_adresse->get($id_etablissement),
                'presence_dus' => count($contacts_dus) > 0
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
                $author = null;
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
                    if($fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"] > 0) {
                        $row = $DB_utilisateursInfo->fetchRow("ID_UTILISATEURINFORMATIONS = " . $DB_utilisateurs->find($fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"])->current()->ID_UTILISATEURINFORMATIONS)->toArray();
                        $author = array(
                            'id' => $fiche["UTILISATEUR_ETABLISSEMENTINFORMATIONS"],
                            'name' => $row['NOM_UTILISATEURINFORMATIONS'] . ' ' . $row['PRENOM_UTILISATEURINFORMATIONS']
                        );
                    }
                    else {
                        $author = null;
                    }
                    $historique[$key][] = array(
                        "valeur" => $value,
                        "debut" =>  $date->get( Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR ),
                        "author" => $author
                    );
                }
            }
        }

        // On traite le cas particulier des dossiers
        $dossiers = $this->getDossiers($id_etablissement);
        $dossiers_merged = $dossiers['etudes'];
        $dossiers_merged = array_merge($dossiers_merged, $dossiers['visites']);
        $dossiers_merged = array_merge($dossiers_merged, $dossiers['autres']);
        
        // on filtre les dossiers ne donnant pas avis
        foreach($dossiers_merged as $key => $dossier) {
            if(!in_array($dossier['AVIS_DOSSIER_COMMISSION'], array(1, 2))) {
                unset($dossiers_merged[$key]);
            }
            else if(!in_array($dossier['ID_DOSSIERNATURE'], array(7, 16, 17, 19, 21, 23, 24, 47, 26, 28, 29, 48)) ) {
               unset($dossiers_merged[$key]);
            }
            else if(!$dossier['DATECOMM_DOSSIER'] && !$dossier['DATEVISITE_DOSSIER']) {
               unset($dossiers_merged[$key]);
            }
        }
        
        @usort($dossiers_merged, function($a, $b) {
          
          $date_a = @new Zend_Date($a['DATECOMM_DOSSIER'] != null ? $a['DATECOMM_DOSSIER'] : $a['DATEVISITE_DOSSIER'], Zend_Date::DATES);
          $date_b = @new Zend_Date($b['DATECOMM_DOSSIER'] != null ? $b['DATECOMM_DOSSIER'] : $b['DATEVISITE_DOSSIER'], Zend_Date::DATES);

          if ($date_a == $date_b || $a === null || $b === null) {
            return 0;
          }
          else if ($date_a < $date_b) {
            return -1;
          }
          else {
            return 1;
          }
        });

        $key = "avis";
        foreach($dossiers_merged as $dossier) {
          $dossier = (object) $dossier;
          $tmp = ( array_key_exists($key, $historique) ) ? $historique[$key][ count($historique[$key])-1 ] : null;
          $value = $dossier->AVIS_DOSSIER_COMMISSION == 1 ? "Favorable" : "Défavorable";
          $author = null;
          
          if ( $value != null && (!isset( $historique[$key] ) || $tmp["valeur"] != $value )) {

            $date = new Zend_Date($dossier->DATECOMM_DOSSIER != null ? $dossier->DATECOMM_DOSSIER : $dossier->DATEVISITE_DOSSIER, Zend_Date::DATES);
            if ($tmp != null) {
              $historique[$key][ count($historique[$key])-1 ]["fin"] = $date->get( Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR );
            }
            if($dossier->CREATEUR_DOSSIER != null) {
              $author = $DB_utilisateursInfo->fetchRow("ID_UTILISATEURINFORMATIONS = " . $DB_utilisateurs->find($dossier->CREATEUR_DOSSIER)->current()->ID_UTILISATEURINFORMATIONS)->toArray();
            }
            $historique[$key][] = array(
              "valeur" => $value,
              "debut" =>  $date->get( Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR ),
              "author" => $dossier->CREATEUR_DOSSIER == 0 ? null : array('id' => $dossier->CREATEUR_DOSSIER, 'name' => $author['NOM_UTILISATEURINFORMATIONS'] . ' ' . $author['PRENOM_UTILISATEURINFORMATIONS'])
            );
          }
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
        $results['visites'] = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("d.TYPE_DOSSIER", array(2, 3))->order("COALESCE(DATEVISITE_DOSSIER, DATECOMM_DOSSIER,DATEINSERT_DOSSIER) DESC")->run()->getAdapter()->getItems(0, 999999)->toArray();
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
            "DESCTECH_IMPLANTATION_SURFACETOTALE_ETABLISSEMENT" => "Surface totale (m²)",
            "DESCTECH_IMPLANTATION_SURFACEACCPUBLIC_ETABLISSEMENT" => "Surface accessible au public (m²)",
            "DESCTECH_IMPLANTATION_SHON_ETABLISSEMENT" => "Surface de plancher (m²)",
            "DESCTECH_IMPLANTATION_NBNIVEAUX_ETABLISSEMENT" => "Nombre de niveaux",
            "DESCTECH_IMPLANTATION_PBDN_ETABLISSEMENT" => "Plancher bas du dernier niveau (m)",
            "DESCTECH_DESSERTE_NBFACADELIBRE_ETABLISSEMENT" => "Nombre de façades accessibles",
            "DESCTECH_DESSERTE_VOIEENGIN_ETABLISSEMENT" => "Voie engin",
            "DESCTECH_DESSERTE_VOIEECHELLE_ETABLISSEMENT" => "Voie echelle",
            "DESCTECH_DESSERTE_ESPACELIBRE_ETABLISSEMENT" => "Espace libre",
            "DESCTECH_ISOLEMENT_LATERALCF_ETABLISSEMENT" => "Latéral CF (minute)",
            "DESCTECH_ISOLEMENT_SUPERPOSECF_ETABLISSEMENT" => "Superposé CF (minute)",
            "DESCTECH_ISOLEMENT_VISAVIS_ETABLISSEMENT" => "Vis-à-vis (m)",
            "DESCTECH_STABILITE_STRUCTURESF_ETABLISSEMENT" => "Structure SF (minute)",
            "DESCTECH_STABILITE_PLANCHERSF_ETABLISSEMENT" => "Plancher SF (minute)",
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
            "DESCTECH_DEFENSE_DEBITSIMULTANE_ETABLISSEMENT" => "Débit simultané (m3/h)",
            "DESCTECH_RISQUES_NATURELS_ETABLISSEMENT" => "Risques naturels",
            "DESCTECH_RISQUES_TECHNOLOGIQUES_ETABLISSEMENT" => "Risques technologiques"
        );

        foreach ($etablissement->toArray() as $key => $value) {
            if (preg_match('/DESCTECH/', $key) && strcmp('DESCTECH_IMPLANTATION_SHOB_ETABLISSEMENT', $key) != 0) {
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
                    case "RISQUES": $title = "Risques"; break;
                }
                
                $champs_descriptif_technique[$title][array_key_exists($key, $translation_champs_des_tech) ? $translation_champs_des_tech[$key] : $key] = array(
                    'value' => $value,
                    'type' => $dbtable_info_etablissement['metadata'][$key]['DATA_TYPE'],
                    'length' => $dbtable_info_etablissement['metadata'][$key]['LENGTH'],
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
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $cache->remove('etablissement_id_' . $id_etablissement);

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


    public function checkAlerte($ets, $postData)
    {
        $alerte = false;

        if ($ets && $postData) {

            if ($ets["informations"]["ID_STATUT"] != $postData["ID_STATUT"]) {
                $alerte = self::STATUT_CHANGE;
            }


            if ($ets["informations"]["ID_CATEGORIE"] != $postData["ID_CATEGORIE"]
                || $ets["informations"]["ID_TYPE"] != $postData["ID_TYPE"]
                || $ets["informations"]["ID_TYPEACTIVITE"] != $postData["ID_TYPEACTIVITE"]
                || $this->compareActivitesSecondaires($ets, $postData)) {
                $alerte = self::CLASSEMENT_CHANGE;
            }

        }

        return $alerte;
    }

    private function compareActivitesSecondaires($ets, $postData)
    {
        $result = false;

        foreach($ets['types_activites_secondaires'] as $typesASecondaires) {
            if ( ! array_key_exists($typesASecondaires[
                'ID_ETABLISSEMENTINFORMATIONSTYPESACTIVITESSECONDAIRES'],
                $postData['TYPES_ACTIVITES_SECONDAIRES'])) {
                $result = true;
                break;
            }
        }

        return $result;
    } 


    /**
     * Sauvegarde d'un établissement
     *
     * @param int $id_genre
     * @param array $data
     * @param int $id_etablissement Optionnel
     * @param int $date Optionnel format : Y-m-d
     * @return int $id_etablissement Optionnel
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

            $data['LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS'] = isset($data['LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS']) && $data['LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS'] !== null ? $data['LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS'] : 0;

            $etablissement = $id_etablissement == null ? $DB_etablissement->createRow() : $DB_etablissement->find($id_etablissement)->current();

            if($date == '') {
                $informations = $DB_informations->createRow(array('DATE_ETABLISSEMENTINFORMATIONS' => date('Y-m-d')));
            }
            else {
                $information_a_la_date_donnee = $DB_informations->fetchRow("ID_ETABLISSEMENT = '" .  $id_etablissement . "' AND DATE_ETABLISSEMENTINFORMATIONS = '" . $date . "'");

                $DB_etablissements_lies->delete("ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);
                $DB_adresse->delete("ID_ETABLISSEMENT = " . $etablissement->ID_ETABLISSEMENT);

                if($information_a_la_date_donnee != null) {
                    $informations = $information_a_la_date_donnee;
                    $DB_plans->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    $DB_rubrique->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    $DB_types_activites_secondaires->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                    $DB_preventionniste->delete("ID_ETABLISSEMENTINFORMATIONS = " . $informations->ID_ETABLISSEMENTINFORMATIONS);
                }
                else {
                    $informations = $DB_informations->createRow(array('DATE_ETABLISSEMENTINFORMATIONS' => $date));
                }
            }

            // Sauvegarde des champs d'établissement (non historisés) en fonction
            $etablissement->TELEPHONE_ETABLISSEMENT = $data['TELEPHONE_ETABLISSEMENT'];
            $etablissement->FAX_ETABLISSEMENT = $data['FAX_ETABLISSEMENT'];
            $etablissement->COURRIEL_ETABLISSEMENT = $data['COURRIEL_ETABLISSEMENT'];

            // Sauvegarde des champs de la fiche d'informations en fonction du genre
            $informations->ICPE_ETABLISSEMENTINFORMATIONS = $informations->PERIODICITE_ETABLISSEMENTINFORMATIONS =
            $informations->DROITPUBLIC_ETABLISSEMENTINFORMATIONS =
            $informations->R12320_ETABLISSEMENTINFORMATIONS = $informations->LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS =
            $informations->ID_CLASSE = $informations->ID_FAMILLE =  $informations->ID_CATEGORIE = $informations->ID_TYPE =
            $informations->ID_TYPEACTIVITE = $informations->ID_COMMISSION = $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS =
            $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = $informations->EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS =
            $informations->EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS = $etablissement->NBPREV_ETABLISSEMENT =
            $etablissement->DUREEVISITE_ETABLISSEMENT = null;

            switch($id_genre) {
                // Établissement
                case 2:
                    $informations->ID_CATEGORIE = $data['ID_CATEGORIE'];
                    $informations->PERIODICITE_ETABLISSEMENTINFORMATIONS = (int) $data['PERIODICITE_ETABLISSEMENTINFORMATIONS'];
                    $informations->ID_TYPE = $data['ID_TYPE'];
                    $informations->ID_TYPEACTIVITE = $data['ID_TYPEACTIVITE'];
                    $informations->R12320_ETABLISSEMENTINFORMATIONS = (int) $data['R12320_ETABLISSEMENTINFORMATIONS'];
                    $informations->DROITPUBLIC_ETABLISSEMENTINFORMATIONS = (int) $data['DROITPUBLIC_ETABLISSEMENTINFORMATIONS'];
                    $informations->LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS = (int) $data['LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS = $informations->LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS ? (int) $data['EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS'] : null;
                    $informations->EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS = $data['ID_CATEGORIE'] == 5 ? (int) $data['EFFECTIFJUSTIFIANTCLASSEMENT_ETABLISSEMENTINFORMATIONS'] : null;
                    $informations->ID_COMMISSION = $data['ID_COMMISSION'];
                    $etablissement->NBPREV_ETABLISSEMENT = (int) $data['NBPREV_ETABLISSEMENT'];
                    $etablissement->DUREEVISITE_ETABLISSEMENT = empty($data['DUREEVISITE_ETABLISSEMENT']) ? null : $data['DUREEVISITE_ETABLISSEMENT'];
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
                    $etablissement->NBPREV_ETABLISSEMENT = (int) $data['NBPREV_ETABLISSEMENT'];
                    $etablissement->DUREEVISITE_ETABLISSEMENT = empty($data['DUREEVISITE_ETABLISSEMENT']) ? null : $data['DUREEVISITE_ETABLISSEMENT'];
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
                    $etablissement->NBPREV_ETABLISSEMENT = (int) $data['NBPREV_ETABLISSEMENT'];
                    $etablissement->DUREEVISITE_ETABLISSEMENT = empty($data['DUREEVISITE_ETABLISSEMENT']) ? null : $data['DUREEVISITE_ETABLISSEMENT'];
                    break;

                // EIC
                case 6:
                    $informations->ICPE_ETABLISSEMENTINFORMATIONS = (int) $data['ICPE_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    break;

                // Camping
                case 7:
                    $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    break;

                // Manifestation temporaire
                case 8:
                    $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    break;

                // IOP
                case 9:
                    $informations->EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'];
                    $informations->EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS = (int) $data['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'];
                    break;

                // Zone
                case 10:
                    $informations->ID_CLASSEMENT = $data['ID_CLASSEMENT'];
                    break;

            }

            $etablissement->save();

            $etablissement->NUMEROID_ETABLISSEMENT = $data['NUMEROID_ETABLISSEMENT'] != null ? $data['NUMEROID_ETABLISSEMENT'] : $etablissement->ID_ETABLISSEMENT;

            $etablissement->save();

            $informations->LIBELLE_ETABLISSEMENTINFORMATIONS = $data['LIBELLE_ETABLISSEMENTINFORMATIONS'];
            $informations->ID_GENRE = $id_genre;
            $informations->ID_STATUT = isset($data['ID_STATUT']) ? $data['ID_STATUT'] : 1;
            $informations->UTILISATEUR_ETABLISSEMENTINFORMATIONS = Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'];
            $informations->ID_ETABLISSEMENT = $etablissement->ID_ETABLISSEMENT;

            $informations->save();

            // Sauvegarde des préventionnistes
            if(array_key_exists('ID_UTILISATEUR', $data) && count($data['ID_UTILISATEUR']) > 0) {
                foreach($data['ID_UTILISATEUR'] as $id_preventionniste) {
                    if($id_preventionniste > 0) {
                        $DB_preventionniste->createRow(array(
                            "ID_ETABLISSEMENTINFORMATIONS" => $informations->ID_ETABLISSEMENTINFORMATIONS,
                            "ID_UTILISATEUR" => $id_preventionniste
                        ))->save();
                    }
                }
            }

            // Sauvegarde des rubriques pour les EIC
            if($id_genre == 6 && array_key_exists('RUBRIQUES', $data) && count($data['RUBRIQUES']) > 0) {
                foreach($data['RUBRIQUES'] as $key => $rubrique) {
                    if($key > 0) {
                        $DB_rubrique->createRow(array(
                            "ID_RUBRIQUE" => $rubrique["ID_RUBRIQUE"],
                            "NUMERO_ETABLISSEMENTINFORMATIONSRUBRIQUE" => !array_key_exists('NUMERO_ETABLISSEMENTINFORMATIONSRUBRIQUE', $rubrique) ? null : (int) $rubrique["NUMERO_ETABLISSEMENTINFORMATIONSRUBRIQUE"],
                            "VALEUR_ETABLISSEMENTINFORMATIONSRUBRIQUE" => !array_key_exists('VALEUR_ETABLISSEMENTINFORMATIONSRUBRIQUE', $rubrique) ? null : (double) $rubrique["VALEUR_ETABLISSEMENTINFORMATIONSRUBRIQUE"],
                            "NOM_ETABLISSEMENTINFORMATIONSRUBRIQUE" => !array_key_exists('NOM_ETABLISSEMENTINFORMATIONSRUBRIQUE', $rubrique) ? null : $rubrique["NOM_ETABLISSEMENTINFORMATIONSRUBRIQUE"],
                            "CLASSEMENT_ETABLISSEMENTINFORMATIONSRUBRIQUE" => !array_key_exists('CLASSEMENT_ETABLISSEMENTINFORMATIONSRUBRIQUE', $rubrique) ? null : $rubrique["CLASSEMENT_ETABLISSEMENTINFORMATIONSRUBRIQUE"],
                            "ID_ETABLISSEMENTINFORMATIONS" => $informations->ID_ETABLISSEMENTINFORMATIONS
                        ))->save();
                    }
                }
            }

            // Sauvegarde des plans en fonction du genre
            if(in_array($id_genre, array(2, 3, 5, 6, 7, 8, 9)) && array_key_exists('PLANS', $data) && count($data['PLANS']) > 0) {
                foreach($data['PLANS'] as $key => $plan) {
                    if($key > 0) {
                        $DB_plans->createRow(array(
                            "ID_ETABLISSEMENTINFORMATIONS" => $informations->ID_ETABLISSEMENTINFORMATIONS,
                            "NUMERO_ETABLISSEMENTPLAN" => !array_key_exists('NUMERO_ETABLISSEMENTPLAN', $plan) ? null : $plan["NUMERO_ETABLISSEMENTPLAN"],
                            "DATE_ETABLISSEMENTPLAN" => !array_key_exists('DATE_ETABLISSEMENTPLAN', $plan) ? null : $plan["DATE_ETABLISSEMENTPLAN"],
                            "MISEAJOUR_ETABLISSEMENTPLAN" => !array_key_exists('MISEAJOUR_ETABLISSEMENTPLAN', $plan) ? null : $plan["MISEAJOUR_ETABLISSEMENTPLAN"],
                            "ID_TYPEPLAN" => $plan["ID_TYPEPLAN"]
                        ))->save();
                    }
                }
            }

            // Sauvegarde des types et activités secondaires en fonction du genre
            if(in_array($id_genre, array(2, 3)) && array_key_exists('TYPES_ACTIVITES_SECONDAIRES', $data) && count($data['TYPES_ACTIVITES_SECONDAIRES']) > 0) {
                foreach($data['TYPES_ACTIVITES_SECONDAIRES'] as $key => $type_activite_secondaire) {
                    if($key > 0) {
                        $DB_types_activites_secondaires->createRow(array(
                            "ID_ETABLISSEMENTINFORMATIONS" => $informations->ID_ETABLISSEMENTINFORMATIONS,
                            "ID_TYPE_SECONDAIRE" => $type_activite_secondaire["ID_TYPE_SECONDAIRE"],
                            "ID_TYPEACTIVITE_SECONDAIRE" => $type_activite_secondaire["ID_TYPEACTIVITE_SECONDAIRE"]
                        ))->save();
                    }
                }
            }

            // Sauvegarde des adresses en fonction du genre
            if(in_array($id_genre, array(2, 4, 5, 6, 7, 8, 9, 10)) && array_key_exists('ADRESSES', $data) && count($data['ADRESSES']) > 0) {
                foreach($data['ADRESSES'] as $key => $adresse) {
                    if($key > 0) {
                    	if(array_key_exists('ID_RUE', $adresse) && (int) $adresse["ID_RUE"] > 0) {
                    		$DB_adresse->createRow(array(
	                            "NUMERO_ADRESSE" => $adresse["NUMERO_ADRESSE"],
	                            "COMPLEMENT_ADRESSE" => $adresse["COMPLEMENT_ADRESSE"],
	                            "LON_ETABLISSEMENTADRESSE" => empty($adresse["LON_ETABLISSEMENTADRESSE"]) ? null : $adresse["LON_ETABLISSEMENTADRESSE"],
	                            "LAT_ETABLISSEMENTADRESSE" => empty($adresse["LAT_ETABLISSEMENTADRESSE"]) ? null : $adresse["LAT_ETABLISSEMENTADRESSE"],
	                            "ID_ETABLISSEMENT" => $etablissement->ID_ETABLISSEMENT,
	                            "ID_RUE" => $adresse["ID_RUE"],
	                            "NUMINSEE_COMMUNE" => $adresse["NUMINSEE_COMMUNE"]
	                        ))->save();
                    	}
                    }
                }
            }

            // Sauvegarde des établissements liés
            if(array_key_exists('ID_FILS_ETABLISSEMENT', $data) && count($data['ID_FILS_ETABLISSEMENT']) > 0) {
                foreach($data['ID_FILS_ETABLISSEMENT'] as $id_etablissement_enfant) {
                    if($id_etablissement_enfant > 0) {
                        $genre_enfant = $DB_etablissement->getInformations($id_etablissement_enfant)->ID_GENRE;
                        if($id_genre == 1 && !in_array($genre_enfant, array(2,4,5,6,7,8,9))) {
                            throw new Exception('L\'établissement enfant n\'est pas compatible (Un site ne ne peut contenir que des établissements, habitations, EIC, camping, manifestation, IOP)', 500);
                        }
                        elseif(in_array($id_genre, array(2,5)) && $genre_enfant != 3) {
                            throw new Exception('L\'établissement enfant n\'est pas compatible (Un établissement ou IGH ne ne peut contenir que des cellules)', 500);
                        }
                        elseif($genre_enfant == 1){
                            throw new Exception('L\'établissement enfant n\'est pas compatible (Un site ne peut être enfant)', 500);
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
            }

            // Sauvegarde du père de l'établissement
            if(array_key_exists("ID_PERE", $data) && !empty($data['ID_PERE'])) {
                $genre_pere = $DB_etablissement->getInformations($data['ID_PERE'])->ID_GENRE;

                if(in_array($id_genre, array(2,4,5,6,7,8,9)) && $genre_pere != 1) {
                    throw new Exception('Le père n\'est pas compatible (Un établissement a comme père un site)', 500);
                }
                elseif($id_genre == 3 && !in_array($genre_pere, array(2, 5))) {
                    throw new Exception('Le père n\'est pas compatible (Les cellules ont comme père un établissement ou IGH)', 500);
                }
                elseif($id_genre == 1) {
                    throw new Exception('Le type n\'est pas compatible (Un site ne peut pas avoir de père)', 500);
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

            Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch')->clean(Zend_Cache::CLEANING_MODE_ALL);
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
     * @param int $genre
     * @param int $numinsee
     * @param int $type
     * @param int $categorie
     * @param bool $local_sommeil
     * @param int $classe
     * @param int $id_etablissement_pere
     * @param array $ids_etablissements_enfants
     * @return array
     */
    public function getDefaultValues($genre, $numinsee = null, $type = null, $categorie = null, $local_sommeil = null, $classe = null, $id_etablissement_pere = null, $ids_etablissements_enfants = null)
    {
        $model_etablissement = new Model_DbTable_Etablissement;
        $model_prev = new Model_DbTable_Preventionniste;
        $DB_periodicite = new Model_DbTable_Periodicite;
        $model_commission = new Model_DbTable_Commission;

        $results = array();

        switch($genre) {
            // Site
            case 1:
                // Preventionnistes des groupements de communes
                if($numinsee !== null) {
                    $results['preventionnistes'] = $model_prev->getPrev($numinsee, '');
                }
                break;

            // Établissement
            case 2:
                // Périodicité en fonction de la catégorie/type/local à sommeil
                if($categorie !== null && $type !== null && $local_sommeil !== null) {
                    $results['periodicite'] = $DB_periodicite->gn4($categorie, $type, $local_sommeil ? 1 : 0);
                }

                // Si pas de valeur de local à sommeil renseignée, on demande par défaut de saisir non
                if (is_null($local_sommeil)) {
                    $results['local_sommeil'] = false;
                }
                // Local à sommeil en fonction du type
                if($type !== null) {
                    if (getenv('PREVARISC_LOCAL_SOMMEIL_TYPES') != false){
                        $concerned_types = explode(';',getenv('PREVARISC_LOCAL_SOMMEIL_TYPES'));
                    } else $concerned_types = array(7,11);
                    if(in_array($type, $concerned_types)) {
                        $results['local_sommeil'] = true;
                    }
                }

                // Commission en fonction des compétences des commissions
                if($id_etablissement_pere !== null && $id_etablissement_pere != '') {
                    $etablissement = $this->get($id_etablissement_pere);
                    if($etablissement['informations']['ID_COMMISSION'] != null || $etablissement['informations']['ID_COMMISSION'] != 0) {
                        $results['commission'] = $model_commission->find($etablissement['informations']['ID_COMMISSION'])->current()->toArray();
                    }
                }

                if(!array_key_exists('commission', $results) && ($numinsee !== null && $categorie !== null && $type !== null && $local_sommeil !== null)) {
                    $commission = $model_commission->getCommission($numinsee, $categorie, $type, $local_sommeil ? 1 : 0);
                    if($commission !== null) {
                        $results['commission'] = $commission[0];
                    }
                }

                // Préventionnistes du site ou des groupements de communes
                if($numinsee !== null || $id_etablissement_pere !== null) {
                    $results['preventionnistes'] = $model_prev->getPrev($numinsee === null ? '' : $numinsee, $id_etablissement_pere === null ? '' : $id_etablissement_pere);
                }
                break;

            // Cellule
            case 3:
                // Préventionnistes de l'établissement parent
                if($id_etablissement_pere !== null) {
                    $results['preventionnistes'] = $model_prev->getPrev('', $id_etablissement_pere);
                }
                break;

            // IGH
            case 5:
                // Périodicité en fonction de la classe
                if($classe !== null) {
                    $results['periodicite'] = $DB_periodicite->gn4(0, $classe, false);
                }

                // Commission en fonction des compétences des commissions
                if($id_etablissement_pere !== null  && $id_etablissement_pere != '') {
                    $etablissement = $this->get($id_etablissement_pere);
                    if($etablissement['informations']['ID_COMMISSION'] != null || $etablissement['informations']['ID_COMMISSION'] != 0) {
                        $results['commission'] = $model_commission->find($etablissement['informations']['ID_COMMISSION'])->current()->toArray();
                    }
                }

                if(!array_key_exists('commission', $results) && ($numinsee !== null && $classe !== null)) {
                    $commission = $model_commission->getCommissionIGH($numinsee, $classe, 0);
                    if($commission !== null) {
                        $results['commission'] = $commission[0];
                    }
                }

                // Préventionnistes du site ou des groupements de communes
                if($numinsee !== null || ($id_etablissement_pere !== null  && $id_etablissement_pere != '')) {
                    $results['preventionnistes'] = $model_prev->getPrev($numinsee === null ? '' : $numinsee, $id_etablissement_pere === null ? '' : $id_etablissement_pere);
                }
                break;

            // Autres genres
            default:
                // Préventionnistes du site ou des groupements de communes
                if($numinsee !== null || $id_etablissement_pere !== null) {
                    $results['preventionnistes'] = $model_prev->getPrev($numinsee === null ? '' : $numinsee, $id_etablissement_pere === null ? '' : $id_etablissement_pere);
                }
                break;
        }

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
     * @param array $file
     * @param string $name
     * @param string $description
     * @param int $mise_en_avant 0 = aucune mise en avant, 1 = diaporama, 2 = plans
     */
    public function addPJ($id_etablissement, $file, $name = '', $description = '', $mise_en_avant = 0)
    {

        $extension = strtolower(strrchr($file['name'], "."));
        
        // Extension du fichier
        if (in_array($extension, array('.php', '.php4', '.php5', '.sh', '.ksh', '.csh'))) {
            throw new Exception("Ce type de fichier n'est pas autorisé en upload");
        }
            
        $DBpieceJointe = new Model_DbTable_PieceJointe;

        $piece_jointe = array(
            'EXTENSION_PIECEJOINTE' => $extension,
            'NOM_PIECEJOINTE' => $name == '' ? substr($file['name'], 0, -4) : $name,
            'DESCRIPTION_PIECEJOINTE' => $description,
            'DATE_PIECEJOINTE' => date('Y-m-d')
        );

        $piece_jointe['ID_PIECEJOINTE'] = $DBpieceJointe->createRow($piece_jointe)->save();

        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        $file_path = $store->getFilePath($piece_jointe, 'etablissement', $id_etablissement, true);

        if(!move_uploaded_file($file['tmp_name'], $file_path)) {
            $msg = 'Ne peut pas déplacer le fichier ' . $file['tmp_name']. ' vers '.$file_path;

            // log some debug information
            error_log($msg);
            error_log("is_dir ".dirname($file_path).": ".is_dir(dirname($file_path)));
            error_log("is_writable ".dirname($file_path).":".is_writable(dirname($file_path)));
            $cmd = 'ls -all '.dirname($file_path);
            error_log($cmd);
            $rslt = explode("\n", shell_exec($cmd));
            foreach($rslt as $file) {
                error_log($file);
            }

            throw new Exception($msg);
        }
        else {
            $DBsave = new Model_DbTable_EtablissementPj;

            $linkPj = $DBsave->createRow(array(
                'ID_ETABLISSEMENT' => $id_etablissement,
                'ID_PIECEJOINTE' => $piece_jointe['ID_PIECEJOINTE'],
                'PLACEMENT_ETABLISSEMENTPJ' => (int) $mise_en_avant != 0 && in_array($extension, array(".jpg", ".jpeg", ".png", ".gif")) ? $mise_en_avant : 0,
            ))->save();

            if(in_array($extension, array(".jpg", ".jpeg", ".png", ".gif"))) {
                $miniature = $piece_jointe;
                $miniature['EXTENSION_PIECEJOINTE'] = '.jpg';
                $miniature_path = $store->getFilePath($miniature, 'etablissement_miniature', $id_etablissement, true);
                GD_Resize::run($file_path, $miniature_path, 450);
                if (!is_file($miniature_path)) {
                    throw new Exception("Cannot create miniature file: $miniature_path");
                }
            }
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

        $pj = $DBpieceJointe->find($id_pj)->current();
        if (!$pj) {
            return ;
        }

        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        $file_path = $store->getFilePath($pj, 'etablissement', $id_etablissement);
        $miniature_pj = $pj;
        $miniature_pj['EXTENSION_PIECEJOINTE'] = '.jpg';
        $miniature_path = $store->getFilePath($miniature_pj, 'etablissement_miniature', $id_etablissement);

        if ($DBitem != null) {
            if( file_exists($file_path) )         unlink($file_path);
            if( file_exists($miniature_path))     unlink($miniature_path);
            $DBitem->delete("ID_PIECEJOINTE = " . (int) $id_pj);
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

        $this->addContactExistant($id_etablissement, $id_contact);
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

        $textes_applicables = array();
        $textes_applicables_non_organises = $etsTexteApplicable->recupTextes($id_etablissement);

        $old_titre = null;

        foreach($textes_applicables_non_organises as $texte_applicable)
        {
            if(true) {
                $new_titre = $texte_applicable['ID_TYPETEXTEAPPL'];

                if($old_titre != $new_titre && !array_key_exists($texte_applicable['LIBELLE_TYPETEXTEAPPL'], $textes_applicables)) {
                  $textes_applicables[$texte_applicable['LIBELLE_TYPETEXTEAPPL']] = array();
                }

                $textes_applicables[ $texte_applicable['LIBELLE_TYPETEXTEAPPL' ]][$texte_applicable['ID_TEXTESAPPL']] = array(
                  'ID_TEXTESAPPL' => $texte_applicable['ID_TEXTESAPPL'],
                  'LIBELLE_TEXTESAPPL' => $texte_applicable['LIBELLE_TEXTESAPPL'],
                );

                $old_titre = $new_titre;
            }
        }

        return $textes_applicables;
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

    public function getAvisEtablissement($id_etablissement, $id_dossier_donnant_avis)
    {
        $idDossierDonnantAvis = $id_dossier_donnant_avis;
        $DBdossier = new Model_DbTable_Dossier();
        $infosDossierDonnantAvis = $DBdossier->find($idDossierDonnantAvis)->current();
        $dateInsertDossierDonnantAvis = $infosDossierDonnantAvis['DATEINSERT_DOSSIER'];
        $dateInsertDossierDonnantAvis = new Zend_Date($dateInsertDossierDonnantAvis, Zend_Date::DATES);
        
        $search = new Model_DbTable_Search;
        $dossierDiff = $search->setItem("dossier")->setCriteria("e.ID_ETABLISSEMENT", $id_etablissement)->setCriteria("d.DIFFEREAVIS_DOSSIER", 1)->order("DATEINSERT_DOSSIER DESC")->run()->getAdapter()->getItems(0, 1)->toArray();
        
        //Si l'etablissement ne comporte pas d'avis différé on prend l'avis correspondant à ID_DOSSIERDONNANTAVIS
        if(count($dossierDiff) > 0){
            $dateInsertDossierDiffere = $dossierDiff[0]['DATEINSERT_DOSSIER'];
            $dateInsertDossierDiffere = new Zend_Date($dateInsertDossierDiffere, Zend_Date::DATES);
        }else{
            return "avisDoss";
        }

        //On compare la date de l'avis différé avec la date de l'avis d'exploitation le plus récent
        if($dateInsertDossierDonnantAvis->compare($dateInsertDossierDiffere) == 1){
            return "avisDoss";
        }else{
            return "avisDiff";
        }
    }
    
    public function getDossierDonnantAvis($idEtablissement) {
        $DBEtab = new Model_DbTable_Etablissement;
        return $DBEtab->getDossierDonnantAvis($idEtablissement);
    }
}
