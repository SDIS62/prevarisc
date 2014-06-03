<?php

class Service_Search
{
    /**
     * Recherche des établissements
     *
     * @param string $label
     * @param string $identifiant
     * @param string|array $genre
     * @param string|array $categorie
     * @param string|array $classe
     * @param string|array $famille
     * @param string|array $types
     * @param bool $avis_favorable
     * @param string|array $statuts
     * @param bool $local_sommeil
     * @param float $lon
     * @param float $lat
     * @param int $parent
     * @param string $city
     * @param int $street_id
     * @param int $count Par défaut 10, max 100
     * @param int $page par défaut = 1
     * @return array
     */
    public function etablissements($label = null, $identifiant = null, $genres = null, $categories = null, $classes = null, $familles = null, $types = null, $avis_favorable = null, $statuts = null, $local_sommeil = null, $lon = null, $lat = null, $parent = null, $city = null, $street_id = null, $count = 10, $page = 1)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch');

        // Identifiant de la recherche
        $search_id = 'search_etablissements_' . md5(serialize(func_get_args()));

        if(($results = unserialize($cache->load($search_id))) === false) {

            // Création de l'objet recherche
            $select = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));

            // Requête principale
            $select->from(array("e" => "etablissement"), array("NUMEROID_ETABLISSEMENT", "DUREEVISITE_ETABLISSEMENT", "NBPREV_ETABLISSEMENT"))
                ->columns(array(
                    "NB_ENFANTS" => "( SELECT COUNT(etablissementlie.ID_FILS_ETABLISSEMENT)
                        FROM etablissement
                        INNER JOIN etablissementlie ON etablissement.ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT
                        WHERE etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT)",
                    "PRESENCE_ECHEANCIER_TRAVAUX" => "(SELECT COUNT(dossierlie.ID_DOSSIER1)
                        FROM dossier
                        INNER JOIN etablissementdossier ON dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER2
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE dossiernature.ID_NATURE = 46 AND etablissementdossier.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT)"))
                ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )")
                ->joinLeft("dossier", "e.ID_DOSSIER_DONNANT_AVIS = dossier.ID_DOSSIER", array("DATEVISITE_DOSSIER", "DATECOMM_DOSSIER", "DATEINSERT_DOSSIER", "DIFFEREAVIS_DOSSIER"))
                ->joinLeft("avis", "dossier.AVIS_DOSSIER_COMMISSION = avis.ID_AVIS")
                ->joinLeft("type", "etablissementinformations.ID_TYPE = type.ID_TYPE", "LIBELLE_TYPE")
                ->join("genre", "etablissementinformations.ID_GENRE = genre.ID_GENRE", "LIBELLE_GENRE")
                ->joinLeft("etablissementlie", "e.ID_ETABLISSEMENT = etablissementlie.ID_FILS_ETABLISSEMENT", array("pere" => "ID_ETABLISSEMENT", "ID_FILS_ETABLISSEMENT"))
                ->joinLeft("etablissementinformationspreventionniste", "etablissementinformationspreventionniste.ID_ETABLISSEMENTINFORMATIONS = etablissementinformations.ID_ETABLISSEMENTINFORMATIONS", null)
                ->joinLeft("utilisateur", "utilisateur.ID_UTILISATEUR = etablissementinformationspreventionniste.ID_UTILISATEUR", "ID_UTILISATEUR")
                ->joinLeft("etablissementadresse", "e.ID_ETABLISSEMENT = etablissementadresse.ID_ETABLISSEMENT", array("NUMINSEE_COMMUNE", "LON_ETABLISSEMENTADRESSE", "LAT_ETABLISSEMENTADRESSE", "ID_ADRESSE", "ID_RUE"))
                ->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_DEFAULT")
                ->joinLeft(array("etablissementadressesite" => "etablissementadresse"), "etablissementadressesite.ID_ETABLISSEMENT = (SELECT ID_FILS_ETABLISSEMENT FROM etablissementlie WHERE ID_ETABLISSEMENT = e.ID_ETABLISSEMENT LIMIT 1)", "ID_RUE AS ID_RUE_SITE")
                ->joinLeft(array("adressecommunesite" => "adressecommune"), "etablissementadressesite.NUMINSEE_COMMUNE = adressecommunesite.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_SITE")
                ->joinLeft(array("etablissementadressecell" => "etablissementadresse"), "etablissementadressecell.ID_ETABLISSEMENT = (SELECT ID_ETABLISSEMENT FROM etablissementlie WHERE ID_FILS_ETABLISSEMENT = e.ID_ETABLISSEMENT LIMIT 1)", "ID_RUE AS ID_RUE_CELL")
                ->joinLeft(array("adressecommunecell" => "adressecommune"), "etablissementadressecell.NUMINSEE_COMMUNE = adressecommunecell.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_CELLULE")
                ->order("etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS ASC")
                ->group("e.ID_ETABLISSEMENT");

            // Critères : nom de l'établissement
            if($label !== null) {
               $this->setCriteria($select, "LIBELLE_ETABLISSEMENTINFORMATIONS", $label, false);
            }

            // Critères : identifiant
            if($identifiant !== null) {
               $this->setCriteria($select, "NUMEROID_ETABLISSEMENT", $identifiant);
            }

            // Critères : genre
            if($genres !== null) {
               $this->setCriteria($select, "genre.ID_GENRE", $genres);
            }

            // Critères : catégorie
            if($categories !== null) {
               $this->setCriteria($select, "ID_CATEGORIE", $categories);
            }

            // Critères : classe
            if($classes !== null) {
               $this->setCriteria($select, "ID_CLASSE", $classes);
            }

            // Critères : famille
            if($familles !== null) {
               $this->setCriteria($select, "ID_FAMILLE", $familles);
            }

            // Critères : type
            if($types !== null) {
               $this->setCriteria($select, "type.ID_TYPE", $types);
            }

            // Critères : avis favorable
            if($avis_favorable !== null) {
               $this->setCriteria($select, "avis.ID_AVIS", $avis_favorable ? 1 : 2);
            }

            // Critères : statuts
            if($statuts !== null) {
               $this->setCriteria($select, "ID_STATUT", $statuts);
            }

            // Critères : statuts
            if($local_sommeil !== null) {
               $this->setCriteria($select, "LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS", $local_sommeil);
            }

            // Critère : commune et rue
            if($city !== null) {
                if($genres !== null && count($genres) > 0) {
                    foreach($genres as $genre) {
                        switch($genre) {
                            case "1":
                                $this->setCriteria($select, "adressecommunesite.LIBELLE_COMMUNE", $city);
                                if($street_id !== null) {
                                    $this->setCriteria($select, "etablissementadressesite.ID_RUE", $street_id);
                                }
                                break;
                            case "3":
                                $this->setCriteria($select, "adressecommunecell.LIBELLE_COMMUNE", $city);
                                if($street_id !== null) {
                                    $this->setCriteria($select, "etablissementadressecell.ID_RUE", $street_id);
                                }
                                break;

                            default:
                                $this->setCriteria($select, "adressecommune.LIBELLE_COMMUNE", $city);
                                if($street_id !== null) {
                                    $this->setCriteria($select, "etablissementadresse.ID_RUE", $street_id);
                                }
                        }
                    }
                }
                else {
                    $this->setCriteria($select, "LIBELLE_COMMUNE_ADRESSE_SITE", $city, true, "orHaving");
                    $this->setCriteria($select, "LIBELLE_COMMUNE_ADRESSE_CELLULE", $city, true, "orHaving");
                    $this->setCriteria($select, "LIBELLE_COMMUNE_ADRESSE_DEFAULT", $city, true, "orHaving");

                    if($street_id !== null) {
                        $this->setCriteria($select, "ID_RUE_SITE", $street_id, true, "orHaving");
                        $this->setCriteria($select, "ID_RUE_CELL", $street_id, true, "orHaving");
                        $this->setCriteria($select, "etablissementadresse.ID_RUE", $street_id, true, "orHaving");
                    }
                }
            }

            // Critères : géolocalisation
            if($lon !== null && $lat !== null) {
               $this->setCriteria($select, "LON_ETABLISSEMENTADRESSE", $lon);
               $this->setCriteria($select, "LAT_ETABLISSEMENTADRESSE", $lat);
            }

            // Critères : parent
            if($parent !== null) {
               $select->where($parent == 0 ? "etablissementlie.ID_ETABLISSEMENT IS NULL" : "etablissementlie.ID_ETABLISSEMENT = ?", $parent);
            }

            // Gestion des pages et du count
            $select->limitPage($page, $count > 100 ? 100 : $count);

            // Construction du résultat
            $rows_counter = new Zend_Paginator_Adapter_DbSelect($select);
            $results = array(
                'results' => $select->query()->fetchAll(),
                'search_metadata' => array(
                    'search_id' => $search_id,
                    'current_page' => $page,
                    'count' => count($rows_counter)
                )
            );

            $cache->save(serialize($results));
        }

        return $results;
    }

    /**
     * Recherche des dossiers
     *
     * @param array $types
     * @param string $objet
     * @param string $num_doc_urba
     * @param int $parent Id d'un dossier parent
     * @param int $count Par défaut 10, max 100
     * @param int $page par défaut = 1
     * @return array
     */
    public function dossiers($types = null, $objet = null, $num_doc_urba = null, $parent = null, $count = 10, $page = 1)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch');

        // Identifiant de la recherche
        $search_id = 'search_dossiers_' . md5(serialize(func_get_args()));

        if(($results = unserialize($cache->load($search_id))) === false) {

            // Création de l'objet recherche
            $select = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));

            // Requête principale
            $select->from(array("d" => "dossier"))
                ->columns(array(
                    "NB_DOSS_LIES" => "(SELECT COUNT(dossierlie.ID_DOSSIER2)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER1
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER)",
                    "NB_URBA" => "( SELECT group_concat(dossierdocurba.NUM_DOCURBA, ', ')
                        FROM dossier
                        INNER JOIN dossierdocurba ON dossierdocurba.ID_DOSSIER = dossier.ID_DOSSIER
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER
                        LIMIT 1)",
                    "ALERTE_RECEPTION_TRAVAUX" => "(SELECT COUNT(dossierlie.ID_DOSSIER2)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER1
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE (dossiernature.ID_NATURE = 2 OR dossiernature.ID_NATURE = 1 OR dossiernature.ID_NATURE = 13 OR dossiernature.ID_NATURE = 12) AND dossier.ID_DOSSIER = d.ID_DOSSIER)",
                    "ECHEANCIER_TRAVAUX" => "(SELECT COUNT(dossierlie.ID_DOSSIER1)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER2
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE dossiernature.ID_NATURE = 46 AND dossier.ID_DOSSIER = d.ID_DOSSIER)"))
                ->joinLeft("dossierlie", "d.ID_DOSSIER = dossierlie.ID_DOSSIER2")
                ->join("dossiernature", "dossiernature.ID_DOSSIER = d.ID_DOSSIER", null)
                ->join("dossiernatureliste", "dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE", array("LIBELLE_DOSSIERNATURE", "ID_DOSSIERNATURE"))
                ->join("dossiertype", "dossiertype.ID_DOSSIERTYPE = dossiernatureliste.ID_DOSSIERTYPE", "LIBELLE_DOSSIERTYPE")
                ->joinLeft(array("e" => "etablissementdossier"), "d.ID_DOSSIER = e.ID_DOSSIER", null)
                ->joinLeft("avis", "d.AVIS_DOSSIER_COMMISSION = avis.ID_AVIS")
                ->group("d.ID_DOSSIER");

            // Critères : numéro de doc urba
            if($num_doc_urba !== null) {
               $select->having("NB_URBA like ?", "%$num_doc_urba%");
            }

            // Critères : objet
            if($objet !== null) {
               $this->setCriteria($select, "OBJET_DOSSIER", $objet, false);
            }

            // Critères : parent
            if($parent !== null) {
               $select->where($parent == 0 ? "dossierlie.ID_DOSSIER1 IS NULL" : "dossierlie.ID_DOSSIER1 = ?", $parent);
            }

            // Critères : type
            if($types !== null) {
               $this->setCriteria($select, "dossiertype.ID_DOSSIERTYPE", $types);
            }

            // Gestion des pages et du count
            $select->limitPage($page, $count > 100 ? 100 : $count);

            // Construction du résultat
            $rows_counter = new Zend_Paginator_Adapter_DbSelect($select);
            $results = array(
                'results' => $select->query()->fetchAll(),
                'search_metadata' => array(
                    'search_id' => $search_id,
                    'current_page' => $page,
                    'count' => count($rows_counter)
                )
            );

            $cache->save(serialize($results));
        }

        return $results;
    }
    
    /**
     * Recherche des courriers
     *
     * @param array $types
     * @param string $objet
     * @param string $num_doc_urba
     * @param int $parent Id d'un dossier parent
     * @param int $count Par défaut 10, max 100
     * @param int $page par défaut = 1
     * @return array
     */
    public function courriers($objet = null, $num_doc_urba = null, $parent = null, $count = 10, $page = 1)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch');

        // Identifiant de la recherche
        $search_id = 'search_dossiers_' . md5(serialize(func_get_args()));

        if(($results = unserialize($cache->load($search_id))) === false) {

            // Création de l'objet recherche
            $select = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));

            // Requête principale
            $select->from(array("d" => "dossier"))
                ->columns(array(
                    "NB_DOSS_LIES" => "(SELECT COUNT(dossierlie.ID_DOSSIER2)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER1
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER)",
                    "NB_URBA" => "( SELECT group_concat(dossierdocurba.NUM_DOCURBA, ', ')
                        FROM dossier
                        INNER JOIN dossierdocurba ON dossierdocurba.ID_DOSSIER = dossier.ID_DOSSIER
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER
                        LIMIT 1)",
                    "ALERTE_RECEPTION_TRAVAUX" => "(SELECT COUNT(dossierlie.ID_DOSSIER2)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER1
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE (dossiernature.ID_NATURE = 2 OR dossiernature.ID_NATURE = 1 OR dossiernature.ID_NATURE = 13 OR dossiernature.ID_NATURE = 12) AND dossier.ID_DOSSIER = d.ID_DOSSIER)",
                    "ECHEANCIER_TRAVAUX" => "(SELECT COUNT(dossierlie.ID_DOSSIER1)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER2
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE dossiernature.ID_NATURE = 46 AND dossier.ID_DOSSIER = d.ID_DOSSIER)"))
                ->joinLeft("dossierlie", "d.ID_DOSSIER = dossierlie.ID_DOSSIER2")
                ->join("dossiernature", "dossiernature.ID_DOSSIER = d.ID_DOSSIER", null)
                ->join("dossiernatureliste", "dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE", array("LIBELLE_DOSSIERNATURE", "ID_DOSSIERNATURE"))
                ->join("dossiertype", "dossiertype.ID_DOSSIERTYPE = dossiernatureliste.ID_DOSSIERTYPE", "LIBELLE_DOSSIERTYPE")
                ->joinLeft(array("e" => "etablissementdossier"), "d.ID_DOSSIER = e.ID_DOSSIER", null)
                ->joinLeft("avis", "d.AVIS_DOSSIER_COMMISSION = avis.ID_AVIS")
                ->group("d.ID_DOSSIER");

            // Critères : numéro de doc urba
            if($num_doc_urba !== null) {
               $this->setCriteria($select, "NUM_DOCURBA", $num_doc_urba);
            }
            
            if (null !== $objet) {
                $select->where("DEMANDEUR_DOSSIER LIKE '%{$objet}%' OR OBJET_DOSSIER LIKE '%{$objet}%'");
            }

            // Critères : parent
            if($parent !== null) {
               $select->where($parent == 0 ? "dossierlie.ID_DOSSIER1 IS NULL" : "dossierlie.ID_DOSSIER1 = ?", $parent);
            }

            // Critères : type
            $this->setCriteria($select, "dossiertype.ID_DOSSIERTYPE", 5);

            // Gestion des pages et du count
            $select->limitPage($page, $count > 100 ? 100 : $count);
            // Construction du résultat
            $rows_counter = new Zend_Paginator_Adapter_DbSelect($select);
            $results = array(
                'results' => $select->query()->fetchAll(),
                'search_metadata' => array(
                    'search_id' => $search_id,
                    'current_page' => $page,
                    'count' => count($rows_counter)
                )
            );

            $cache->save(serialize($results));
        }

        return $results;
    }

    /**
     * Recherche des utilisateurs
     *
     * @param string|array $fonctions
     * @param string $name
     * @param int|array $groups
     * @param bool $actif Optionnel
     * @param int $count Par défaut 10, max 100
     * @param int $page par défaut = 1
     * @return array
     */
    public function users($fonctions = null, $name = null, $groups = null, $actif = true, $count = 10, $page = 1)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch');

        // Identifiant de la recherche
        $search_id = 'search_users_' . md5(serialize(func_get_args()));

        if(($results = unserialize($cache->load($search_id))) === false) {

            // Création de l'objet recherche
            $select = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));

            // Requête principale
            $select->from(array("u" => "utilisateur"), array("uid" => "ID_UTILISATEUR", "*"))
                ->join("utilisateurinformations", "u.ID_UTILISATEURINFORMATIONS = utilisateurinformations.ID_UTILISATEURINFORMATIONS")
                ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION", "LIBELLE_FONCTION")
                ->joinLeft("etablissementinformationspreventionniste", "etablissementinformationspreventionniste.ID_UTILISATEUR = u.ID_UTILISATEUR")
                ->joinLeft("etablissementinformations", "etablissementinformations.ID_ETABLISSEMENTINFORMATIONS = etablissementinformationspreventionniste.ID_ETABLISSEMENTINFORMATIONS")
                ->where("etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(infos.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations as infos WHERE etablissementinformations.ID_ETABLISSEMENT = infos.ID_ETABLISSEMENT ) OR etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS IS NULL")
                ->group("u.ID_UTILISATEUR")
                ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS");

            // Critères : activité
            if($actif === true) {
                $this->setCriteria($select, "u.ACTIF_UTILISATEUR", 1);
            }
            elseif($actif === false) {
              $this->setCriteria($select, "u.ACTIF_UTILISATEUR", 0);
            }

            // Critères : groupe
            if($groups !== null) {
               $this->setCriteria($select, "ID_GROUPE", $groups);
            }

            // Critères : nom
            if($name !== null) {
               $this->setCriteria($select, "NOM_UTILISATEURINFORMATIONS", $name, false);
                $this->setCriteria($select, "PRENOM_UTILISATEURINFORMATIONS", $name, false, "orWhere");
            }

            // Critères : fonctions
            if($fonctions !== null) {
               $this->setCriteria($select, "fonction.ID_FONCTION", $fonctions);
            }

            // Gestion des pages et du count
            $select->limitPage($page, $count > 100 ? 100 : $count);

            // Construction du résultat
            $rows_counter = new Zend_Paginator_Adapter_DbSelect($select);
            $results = array(
                'results' => $select->query()->fetchAll(),
                'search_metadata' => array(
                    'search_id' => $search_id,
                    'current_page' => $page,
                    'count' => count($rows_counter)
                )
            );

            $cache->save(serialize($results));
        }

        return $results;
    }

    /**
     * Méthode pour aider à placer des conditions sur la requête
     *
     * @param Zend_Db_Select $select
     * @param string $key
     * @param string|array $value
     * @param bool $exact
     * @param string $clause Par défaut where
     * @return Service_Search Interface fluide
     */
    private function setCriteria(Zend_Db_Select &$select, $key, $value, $exact = true, $clause = "where")
    {
        $string = null;

        if ( is_array($value) ) {
            for ($i=0; $i<count($value); $i++) {
                $string .= $key . (( $exact ) ? "=" : " LIKE ") . $select->getAdapter()->quote((( $exact ) ? "" : "%") . $value[$i] . (( $exact ) ? "" : "%"));
                if ( $i < count($value) - 1 ) {
                    $string .= " OR ";
                }
            }
        } else {
            $string = $key . (( $exact ) ? "=" : " LIKE ") . $select->getAdapter()->quote((( $exact ) ? "" : "%") . $value . (( $exact ) ? "" : "%"));
        }

        $select->$clause($string);

        return $this;
    }
}
