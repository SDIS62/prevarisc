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
     * @param string|array $types_activites
     * @param bool $avis_favorable
     * @param string|array $statuts
     * @param bool $local_sommeil
     * @param float $lon
     * @param float $lat
     * @param int $parent
     * @param string $city
     * @param int $street_id
     * @param int $count Par défaut 10, max 1000
     * @param int $page par défaut = 1
     * @return array
     */
    public function etablissements($label = null, $identifiant = null, $genres = null, $categories = null, $classes = null, $familles = null, $types_activites = null, $avis_favorable = null, $statuts = null, $local_sommeil = null, $lon = null, $lat = null, $parent = null, $city = null, $street_id = null, $count = 10, $page = 1)
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
                    "NB_ENFANTS" => new Zend_Db_Expr("( SELECT COUNT(etablissementlie.ID_FILS_ETABLISSEMENT)
                        FROM etablissement
                        INNER JOIN etablissementlie ON etablissement.ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT
                        WHERE etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT)"),
                    "PRESENCE_ECHEANCIER_TRAVAUX" => new Zend_Db_Expr("(SELECT COUNT(dossierlie.ID_DOSSIER1)
                        FROM dossier
                        INNER JOIN etablissementdossier ON dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER2
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE dossiernature.ID_NATURE = 46 AND etablissementdossier.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT)")))
                //->join(array("etablissementinformations" => new Zend_Db_Expr("(SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS), etablissementinformations.* FROM etablissementinformations group by ID_ETABLISSEMENT)")), "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT")
                ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )")
                ->joinLeft("dossier", "e.ID_DOSSIER_DONNANT_AVIS = dossier.ID_DOSSIER", array("DATEVISITE_DOSSIER", "DATECOMM_DOSSIER", "DATEINSERT_DOSSIER", "DIFFEREAVIS_DOSSIER"))
                ->joinLeft("avis", "dossier.AVIS_DOSSIER_COMMISSION = avis.ID_AVIS")
                ->joinLeft("type", "etablissementinformations.ID_TYPE = type.ID_TYPE", "LIBELLE_TYPE")
                ->joinLeft("typeactivite", "etablissementinformations.ID_TYPEACTIVITE = typeactivite.ID_TYPEACTIVITE", "LIBELLE_ACTIVITE")
                ->join("genre", "etablissementinformations.ID_GENRE = genre.ID_GENRE", "LIBELLE_GENRE")
                ->joinLeft("etablissementlie", "e.ID_ETABLISSEMENT = etablissementlie.ID_FILS_ETABLISSEMENT", array("pere" => "ID_ETABLISSEMENT", "ID_FILS_ETABLISSEMENT"))
                //->joinLeft("etablissementinformationspreventionniste", "etablissementinformationspreventionniste.ID_ETABLISSEMENTINFORMATIONS = etablissementinformations.ID_ETABLISSEMENTINFORMATIONS", null)
                //->joinLeft("utilisateur", "utilisateur.ID_UTILISATEUR = etablissementinformationspreventionniste.ID_UTILISATEUR", "ID_UTILISATEUR")
                ->joinLeft("etablissementadresse", "e.ID_ETABLISSEMENT = etablissementadresse.ID_ETABLISSEMENT", array("NUMINSEE_COMMUNE", "LON_ETABLISSEMENTADRESSE", "LAT_ETABLISSEMENTADRESSE", "ID_ADRESSE", "ID_RUE"))
                ->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_DEFAULT")
                ->joinLeft("adresserue", "adresserue.ID_RUE = etablissementadresse.ID_RUE", "LIBELLE_RUE")
                ->joinLeft(array("etablissementadressesite" => "etablissementadresse"), "etablissementadressesite.ID_ETABLISSEMENT = (SELECT ID_FILS_ETABLISSEMENT FROM etablissementlie WHERE ID_ETABLISSEMENT = e.ID_ETABLISSEMENT LIMIT 1)", "ID_RUE AS ID_RUE_SITE")
                ->joinLeft(array("adressecommunesite" => "adressecommune"), "etablissementadressesite.NUMINSEE_COMMUNE = adressecommunesite.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_SITE")
                ->joinLeft(array("etablissementadressecell" => "etablissementadresse"), "etablissementadressecell.ID_ETABLISSEMENT = (SELECT ID_ETABLISSEMENT FROM etablissementlie WHERE ID_FILS_ETABLISSEMENT = e.ID_ETABLISSEMENT LIMIT 1)", "ID_RUE AS ID_RUE_CELL")
                ->joinLeft(array("adressecommunecell" => "adressecommune"), "etablissementadressecell.NUMINSEE_COMMUNE = adressecommunecell.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_CELLULE")

                // Vincent MICHEL le 12/11/2014 : retrait de cette clause qui tue les performances
                // sur la recherche. Je n'ai pas vu d'impact sur le retrait du group by.
                // Cyprien DEMAEGDT le 03/08/2015 : rétablissement de la clause pour résoudre le
                // problème de duplicité d'établissements dans les résultats de recherche (#1300)
                ->group("e.ID_ETABLISSEMENT")
                ;

            // Critères : nom de l'établissement
            if($label !== null) {

                $cleanLabel = trim($label);

                // recherche par id
                if (substr($cleanLabel, 0, 1) == "#") {
                    $this->setCriteria($select, "NUMEROID_ETABLISSEMENT", substr($cleanLabel, 1), false);

                // on test si la chaine contient uniquement des caractères de type identifiant sans espace
                } else  if (preg_match('/^[E0-9\/\-\.]+([0-9A-Z]{1,2})?$/', $cleanLabel) === 1) {
                    $this->setCriteria($select, "NUMEROID_ETABLISSEMENT", $cleanLabel, false);

                // cas par défaut
                } else {
                  $this->setCriteria($select, "LIBELLE_ETABLISSEMENTINFORMATIONS", $cleanLabel, false);
                }
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
            if($types_activites !== null) {
               $this->setCriteria($select, "typeactivite.ID_TYPEACTIVITE", $types_activites);
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
            if($street_id !== null) {
                $clauses = array();
                $clauses[] = "etablissementadresse.ID_RUE = ".$select->getAdapter()->quote($street_id);
                if($genres == null || in_array('1', $genres)) {
                    $clauses[] = "etablissementadressesite.ID_RUE = ".$select->getAdapter()->quote($street_id);
                }
                if($genres == null || in_array('3', $genres)) {
                    $clauses[] = "etablissementadressecell.ID_RUE = ".$select->getAdapter()->quote($street_id);
                }
                $select->where('('.implode(' OR ', $clauses).')');
            }
            else if($city !== null) {
                $clauses = array();
                $clauses[] = "etablissementadresse.NUMINSEE_COMMUNE = ". $select->getAdapter()->quote($city);
                if($genres == null || in_array('1', $genres)) {
                    $clauses[] = "etablissementadressesite.NUMINSEE_COMMUNE = ". $select->getAdapter()->quote($city);
                }
                if($genres == null || in_array('3', $genres)) {
                    $clauses[] = "etablissementadressecell.NUMINSEE_COMMUNE = ". $select->getAdapter()->quote($city);
                }
                $select->where('('.implode(' OR ', $clauses).')');
            }

            // Critères : géolocalisation
            if($lon !== null && $lat !== null) {
               $this->setCriteria($select, "etablissementadresse.LON_ETABLISSEMENTADRESSE", $lon);
               $this->setCriteria($select, "etablissementadresse.LAT_ETABLISSEMENTADRESSE", $lat);
            }

            // Critères : parent
            if($parent !== null) {
               $select->where($parent == 0 ? "etablissementlie.ID_ETABLISSEMENT IS NULL" : "etablissementlie.ID_ETABLISSEMENT = ?", $parent);
            }

            // Performance optimisation : avoid sorting on big queries, and sort only if
            // there is at least one where part
            if (count($select->getPart(Zend_Db_Select::WHERE)) > 0) {
                $select->order("etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS ASC");
            }

            // Gestion des pages et du count
            $select->limitPage($page, $count > 1000 ? 1000 : $count);

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
     * @param bool $avis_differe Avis différé
     * @param int $count Par défaut 10, max 100
     * @param int $page par défaut = 1
     * @return array
     */
    public function dossiers($types = null, $objet = null, $num_doc_urba = null, $parent = null, $avis_differe = null, $count = 10, $page = 1, $criterias = null)
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
                    "NB_DOSS_LIES" => new Zend_Db_Expr("(SELECT COUNT(dossierlie.ID_DOSSIER2)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER1
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER)"),
                    "NB_URBA" => new Zend_Db_Expr("( SELECT group_concat(dossierdocurba.NUM_DOCURBA, ', ')
                        FROM dossier
                        INNER JOIN dossierdocurba ON dossierdocurba.ID_DOSSIER = dossier.ID_DOSSIER
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER
                        LIMIT 1)"),
                    "ALERTE_RECEPTION_TRAVAUX" => new Zend_Db_Expr("(SELECT COUNT(dossierlie.ID_DOSSIER2)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER1
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE (dossiernature.ID_NATURE = 2 OR dossiernature.ID_NATURE = 1 OR dossiernature.ID_NATURE = 13 OR dossiernature.ID_NATURE = 12) AND dossier.ID_DOSSIER = d.ID_DOSSIER)"),
                    "ECHEANCIER_TRAVAUX" => new Zend_Db_Expr("(SELECT COUNT(dossierlie.ID_DOSSIER1)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER2
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE dossiernature.ID_NATURE = 46 AND dossier.ID_DOSSIER = d.ID_DOSSIER)")))
                ->joinLeft("dossierlie", "d.ID_DOSSIER = dossierlie.ID_DOSSIER2")
                ->joinLeft("commission", "d.COMMISSION_DOSSIER = commission.ID_COMMISSION","LIBELLE_COMMISSION")
                ->join("dossiernature", "dossiernature.ID_DOSSIER = d.ID_DOSSIER", null)
                ->join("dossiernatureliste", "dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE", array("LIBELLE_DOSSIERNATURE", "ID_DOSSIERNATURE"))
                ->join("dossiertype", "dossiertype.ID_DOSSIERTYPE = dossiernatureliste.ID_DOSSIERTYPE", "LIBELLE_DOSSIERTYPE")
                ->joinLeft(array("e"=>"etablissementdossier"), "d.ID_DOSSIER = e.ID_DOSSIER", null)
                ->joinLeft(array("ei" => new Zend_Db_Expr("(SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS), etablissementinformations.* FROM etablissementinformations group by ID_ETABLISSEMENT)")), "e.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT", array("LIBELLE_ETABLISSEMENTINFORMATIONS","ID_ETABLISSEMENT"))
                ->joinLeft("type","type.ID_TYPE = ei.ID_TYPE",array("ID_TYPE","LIBELLE_TYPE"))
                ->joinLeft("genre","genre.ID_GENRE = ei.ID_GENRE","LIBELLE_GENRE")
                ->joinLeft("avis", "d.AVIS_DOSSIER_COMMISSION = avis.ID_AVIS")
                ->joinLeft("dossierdocurba","dossierdocurba.ID_DOSSIER = d.ID_DOSSIER",null)
                ->joinLeft("dossieraffectation","dossieraffectation.ID_DOSSIER_AFFECT = d.ID_DOSSIER",null)
                ->joinLeft("datecommission","datecommission.ID_DATECOMMISSION = dossieraffectation.ID_DATECOMMISSION_AFFECT",null)
                ->joinLeft("dossierpreventionniste","dossierpreventionniste.ID_DOSSIER = d.ID_DOSSIER",null)
                ->joinLeft(array("ea" => "etablissementadresse"),"ea.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT",null)
                ->group("d.ID_DOSSIER")
                ;

            // Critères : numéro de doc urba
            if($num_doc_urba !== null) {
               $select->having("NB_URBA like ?", "%$num_doc_urba%");
            }

            // Critères : objet
            if($objet !== null) {

                $cleanObjet = trim($objet);

                // recherche par id
                if (substr($cleanObjet, 0, 1) == "#") {
                    $select->having("NB_URBA like ?", "%".substr($cleanObjet, 1)."%");
                // on test si la chaine contient uniquement des caractères de type identifiant sans espace
                } else  if (preg_match('/^[0-9A-Z\.]+$/', $cleanObjet) === 1) {
                    $select->having("NB_URBA like ?", "%".$cleanObjet."%");
                // cas par défaut
                } else {
                  $this->setCriteria($select, "OBJET_DOSSIER", $cleanObjet, false);
                }
            }

            // Critères : parent
            if($parent !== null) {
               $select->where($parent == 0 ? "dossierlie.ID_DOSSIER1 IS NULL" : "dossierlie.ID_DOSSIER1 = ?", $parent);
            }

            // Critères : type
            if($types !== null) {
               $this->setCriteria($select, "dossiertype.ID_DOSSIERTYPE", $types);
            }

            // Critères : avis différé
            if($avis_differe !== null) {
               $this->setCriteria($select, "d.DIFFEREAVIS_DOSSIER", $avis_differe);
            }

            // Critères : commissions
            if (isset($criterias['commissions']) && $criterias['commissions'] !== null){
                $this->setCriteria($select, "datecommission.COMMISSION_CONCERNE", $criterias['commissions']);
            }

            // Critères : avis commission
            if (isset($criterias['avisCommission']) && $criterias['avisCommission'] !== null){
                $this->setCriteria($select, "d.AVIS_DOSSIER_COMMISSION", $criterias['avisCommission']);
            }

            // Critères : avis rapporteur
            if (isset($criterias['avisRapporteur']) && $criterias['avisRapporteur'] !== null){
                $this->setCriteria($select, "d.AVIS_DOSSIER", $criterias['avisRapporteur']);
            }

            // Critères : permis
            if (isset($criterias['permis']) && $criterias['permis'] !== null){
                $this->setCriteria($select, "dossierdocurba.NUM_DOCURBA", $criterias['permis']);
            }

            // Critères : permis
            if (isset($criterias['preventionniste']) && $criterias['preventionniste'] !== null){
                $this->setCriteria($select, "dossierpreventionniste.ID_PREVENTIONNISTE", $criterias['preventionniste']);
            }

            if (isset($criterias['commune']) && $criterias['commune'] !== null){
                    $this->setCriteria($select, "ea.NUMINSEE_COMMUNE", $criterias['commune']);
            }

            if (isset($criterias['voie']) && $criterias['voie'] !== null){
                $this->setCriteria($select, "ea.ID_RUE", $criterias['voie']);
            }

            if (isset($criterias['dateCreationStart']) && $criterias['dateCreationStart'] !== null){
                $select->where("d.DATEINSERT_DOSSIER >= STR_TO_DATE (? , '%d/%m/%Y')",$criterias['dateCreationStart']);
            }
            if (isset($criterias['dateCreationEnd']) && $criterias['dateCreationEnd'] !== null){
                $select->where("d.DATEINSERT_DOSSIER <= STR_TO_DATE (? , '%d/%m/%Y')",$criterias['dateCreationEnd']);
            }
            if (isset($criterias['dateReceptionStart']) && $criterias['dateReceptionStart'] !== null){
                $select->where("d.DATESDIS_DOSSIER >= STR_TO_DATE (? , '%d/%m/%Y')",$criterias['dateReceptionStart']);
            }
            if (isset($criterias['dateReceptionEnd']) && $criterias['dateReceptionEnd'] !== null){
                $select->where("d.DATESDIS_DOSSIER <= STR_TO_DATE (? , '%d/%m/%Y')",$criterias['dateReceptionEnd']);
            }
            if (isset($criterias['dateReponseStart']) && $criterias['dateReponseStart'] !== null){
                $select->where("d.DATEREP_DOSSIER >= STR_TO_DATE (? , '%d/%m/%Y')",$criterias['dateReponseStart']);
            }
            if (isset($criterias['dateReponseEnd']) && $criterias['dateReponseEnd'] !== null){
                $select->where("d.DATEREP_DOSSIER <= STR_TO_DATE (? , '%d/%m/%Y')",$criterias['dateReponseEnd']);
            }

            // Performance optimisation : avoid sorting on big queries, and sort only if
            // there is at least one where part
            if (count($select->getPart(Zend_Db_Select::WHERE)) > 0) {
                $select->order("d.DATEINSERT_DOSSIER DESC");
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

            $newResults = array();
            foreach ($results['results'] as $row){
                $newResults[$row['ID_DOSSIER']] = $row;
            }
            $results['results'] = $newResults;

            $sIDsTable = array();
            foreach ($results['results'] as $row) {
                array_push($sIDsTable, $row['ID_DOSSIER']);
            }

            // Si pas de dossier, pas de recherche
            if (!empty($sIDsTable)){

            // Recherche des préventionnistes associés aux dossiers
            $selectPrev = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));
            $selectPrev->from(array("u" => "utilisateur"),'ID_UTILISATEUR')
                    ->join(array("ui" =>"utilisateurinformations"),"u.ID_UTILISATEURINFORMATIONS = ui.ID_UTILISATEURINFORMATIONS",array("PRENOM_UTILISATEURINFORMATIONS","NOM_UTILISATEURINFORMATIONS"))
                    ->join("dossierpreventionniste","dossierpreventionniste.ID_PREVENTIONNISTE = u.ID_UTILISATEUR","ID_DOSSIER");


                $selectPrev->Where("dossierpreventionniste.ID_DOSSIER IN (?)",$sIDsTable);

                $preventionnistes = $selectPrev->query()->fetchAll();
                foreach ($preventionnistes as $prev) {
                    if ($prev['ID_DOSSIER'] != null){
                        if (!isset($results['results'][$prev['ID_DOSSIER']]['PREVENTIONNISTES'])) $results['results'][$prev['ID_DOSSIER']]['PREVENTIONNISTES'] = array();
                        array_push($results['results'][$prev['ID_DOSSIER']]['PREVENTIONNISTES'], $prev);
                    }
                }


                // Recherche des pièces jointes associés aux dossiers
                $selectPj = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));
                $selectPj->from(array("pj" => "piecejointe"),array('NOM_PIECEJOINTE','EXTENSION_PIECEJOINTE'))
                        ->join('dossierpj','dossierpj.ID_PIECEJOINTE = pj.ID_PIECEJOINTE','ID_DOSSIER');


                $selectPj->Where("dossierpj.ID_DOSSIER IN (?)",$sIDsTable);

                $piecesjointes = $selectPj->query()->fetchAll();
                foreach ($piecesjointes as $pj) {
                    if ($pj['ID_DOSSIER'] != null){
                        if (!isset($results['results'][$pj['ID_DOSSIER']]['PIECESJOINTES'])) $results['results'][$pj['ID_DOSSIER']]['PIECESJOINTES'] = array();
                        array_push($results['results'][$pj['ID_DOSSIER']]['PIECESJOINTES'], $pj);
                    }
                }
            }

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
                    "NB_DOSS_LIES" => new Zend_Db_Expr("(SELECT COUNT(dossierlie.ID_DOSSIER2)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER1
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER)"),
                    "NB_URBA" => new Zend_Db_Expr("( SELECT group_concat(dossierdocurba.NUM_DOCURBA, ', ')
                        FROM dossier
                        INNER JOIN dossierdocurba ON dossierdocurba.ID_DOSSIER = dossier.ID_DOSSIER
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER
                        LIMIT 1)"),
                    "ALERTE_RECEPTION_TRAVAUX" => new Zend_Db_Expr("(SELECT COUNT(dossierlie.ID_DOSSIER2)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER1
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE (dossiernature.ID_NATURE = 2 OR dossiernature.ID_NATURE = 1 OR dossiernature.ID_NATURE = 13 OR dossiernature.ID_NATURE = 12) AND dossier.ID_DOSSIER = d.ID_DOSSIER)"),
                    "ECHEANCIER_TRAVAUX" => new Zend_Db_Expr("(SELECT COUNT(dossierlie.ID_DOSSIER1)
                        FROM dossier
                        INNER JOIN dossierlie ON dossier.ID_DOSSIER = dossierlie.ID_DOSSIER2
                        INNER JOIN dossiernature ON dossierlie.ID_DOSSIER1 = dossiernature.ID_DOSSIER
                        WHERE dossiernature.ID_NATURE = 46 AND dossier.ID_DOSSIER = d.ID_DOSSIER)")))
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

            //$cache->save(serialize($results));
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
                ->join("groupe", "u.ID_GROUPE = groupe.ID_GROUPE", "LIBELLE_GROUPE")
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
               $this->setCriteria($select, "groupe.ID_GROUPE", $groups);
            }

            // Critères : nom
            if($name !== null) {
               $this->setCriteria($select, "(NOM_UTILISATEURINFORMATIONS", $name, false);
                $this->setCriteria($select, "PRENOM_UTILISATEURINFORMATIONS)", $name, false, "orWhere");
            }

            // Critères : fonctions
            if($fonctions !== null) {
               $this->setCriteria($select, "fonction.ID_FONCTION", $fonctions);
            }

            // Gestion des pages et du count
            $select->limitPage($page, $count);

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
     * Recherche des préventionnistes actifs sur au moins un dossier
     *
     * @return array
     */
    public function listePrevActifs()
    {
        // Liste des préventionnistes pour les critères de recherche
        $selectListePrev = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));
        $selectListePrev->from(array("ui" => "utilisateurinformations"),array('NOM_UTILISATEURINFORMATIONS','PRENOM_UTILISATEURINFORMATIONS'))
                ->join('utilisateur','ui.ID_UTILISATEURINFORMATIONS = utilisateur.ID_UTILISATEURINFORMATIONS',null)
                ->join('dossierpreventionniste','dossierpreventionniste.ID_PREVENTIONNISTE = utilisateur.ID_UTILISATEUR','ID_PREVENTIONNISTE')
                ->order('NOM_UTILISATEURINFORMATIONS', 'ASC')
                ->distinct();
        return $selectListePrev->query()->fetchAll();
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
