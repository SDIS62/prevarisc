<?php
    class Model_DbTable_Search extends Zend_Db_Table_Abstract
    {
        protected $_name = "type";
        private $select = null;
        private $item = null;
        public $numpage = null;
        private $nb_items = 100;

        // On demare la recherche
        public function run( $id_etablissement_parent = false, $numero_de_page = null, $paginator = true )
        {
            // Recherche par niveaux
            if ($id_etablissement_parent !== false)
            {
                if($this->item == 'etablissement')
                    $this->select->where($id_etablissement_parent === true || $id_etablissement_parent == 0 ? "etablissementlie.ID_ETABLISSEMENT IS NULL" : "etablissementlie.ID_ETABLISSEMENT = " . $id_etablissement_parent);
                elseif($this->item == 'dossier')
                    $this->select->where($id_etablissement_parent === true || $id_etablissement_parent == 0 ? "dossierlie.ID_DOSSIER1 IS NULL" : "dossierlie.ID_DOSSIER1 = " . $id_etablissement_parent);
            }

            if(!$paginator)
            {
                return $this->fetchAll($this->select);
            }

            // On construit l'objet de pagination
            $paginator = Zend_Paginator::factory($this->select);

            // On set le nombre d'item par page
            $paginator->setItemCountPerPage($numero_de_page == null ? 999999999999999999 : $this->nb_items);

            // On set le numéro de la page demandée
            $paginator->setCurrentPageNumber($numero_de_page == null ? 1 : $numero_de_page);

            // On définit le style & la vue par défaut du choix de page
            $paginator->setDefaultScrollingStyle('Elastic');

            Zend_View_Helper_PaginationControl::setDefaultViewPartial(
                'search' . DIRECTORY_SEPARATOR . 'pagination_control.phtml'
            );

            return $paginator;
        }

        // On set le type d'entité avec ce que l'on recherche
        public function setItem( $item )
        {
            $this->item = $item;
            $this->select = $this->select()->setIntegrityCheck(false);

            switch ($item) {

                // Pour les établissements
                case "etablissement":

                    $this->select
                         ->from(array("e" => "etablissement"), array("NUMEROID_ETABLISSEMENT", "DUREEVISITE_ETABLISSEMENT", "NBPREV_ETABLISSEMENT"))
                         ->columns(array(
                            "NB_ENFANTS" => new Zend_Db_Expr("( SELECT COUNT(etablissementlie.ID_FILS_ETABLISSEMENT)
                                FROM etablissement
                                INNER JOIN etablissementlie ON etablissement.ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT
                                WHERE etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT)")
                         ))
                         ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )")
                         ->joinLeft("dossier", "e.ID_DOSSIER_DONNANT_AVIS = dossier.ID_DOSSIER", array("DATEVISITE_DOSSIER", "DATECOMM_DOSSIER", "DATEINSERT_DOSSIER"))
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
                         ->order("CAST(etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS AS UNSIGNED)")
                         ->order("etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS")
                         ->group("e.ID_ETABLISSEMENT");

                    break;

                // Pour les dossiers
                case "dossier":
                    $this->select
                         ->from(array("d" => "dossier"))
                         ->columns(array(
                             "NB_PJ" => new Zend_Db_Expr("(SELECT COUNT(dossierpj.ID_DOSSIER)
                                 FROM dossierpj
                                 WHERE dossierpj.ID_DOSSIER = d.ID_DOSSIER)"),
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
                                WHERE dossiernature.ID_NATURE = 46 AND dossier.ID_DOSSIER = d.ID_DOSSIER)")
                         ))
                         ->joinLeft("dossierlie", "d.ID_DOSSIER = dossierlie.ID_DOSSIER2")
                         ->join("dossiernature", "dossiernature.ID_DOSSIER = d.ID_DOSSIER", null)
                         ->join("dossiernatureliste", "dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE", array("LIBELLE_DOSSIERNATURE", "ID_DOSSIERNATURE"))
                         ->join("dossiertype", "dossiertype.ID_DOSSIERTYPE = dossiernatureliste.ID_DOSSIERTYPE", "LIBELLE_DOSSIERTYPE")
                         ->joinLeft("dossierdocurba", "d.ID_DOSSIER = dossierdocurba.ID_DOSSIER", "NUM_DOCURBA")
                         ->joinLeft(array("e" => "etablissementdossier"), "d.ID_DOSSIER = e.ID_DOSSIER", null)
                         ->joinLeft("avis", "d.AVIS_DOSSIER_COMMISSION = avis.ID_AVIS")
                         ->joinLeft("dossierpreventionniste", "dossierpreventionniste.ID_DOSSIER = d.ID_DOSSIER", null)
                         ->joinLeft("utilisateur", "utilisateur.ID_UTILISATEUR = dossierpreventionniste.ID_PREVENTIONNISTE", "ID_UTILISATEUR")
                         ->joinLeft("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS =
                            IFNULL(
                                (CASE
                                WHEN d.DATECOMM_DOSSIER IS NOT NULL THEN (SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS <= d.DATEINSERT_DOSSIER)
                                WHEN d.DATEVISITE_DOSSIER IS NOT NULL THEN (SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS <= d.DATEVISITE_DOSSIER)
                                WHEN d.DATEINSERT_DOSSIER IS NOT NULL THEN (SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS <= d.DATEINSERT_DOSSIER)
                                ELSE (SELECT MIN(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT)
                                END),
                                (SELECT MIN(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT)
                            )",
                            "LIBELLE_ETABLISSEMENTINFORMATIONS"
                         )
                         ->group("d.ID_DOSSIER");
                    break;

                // Pour les utilisateurs
                case "utilisateur":

                    $this->select
                         ->from(array("u" => "utilisateur"), array("uid" => "ID_UTILISATEUR"))
                         ->join("utilisateurinformations", "u.ID_UTILISATEURINFORMATIONS = utilisateurinformations.ID_UTILISATEURINFORMATIONS")
                         ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION", "LIBELLE_FONCTION")
                         ->joinLeft("etablissementinformationspreventionniste", "etablissementinformationspreventionniste.ID_UTILISATEUR = u.ID_UTILISATEUR")
                         ->joinLeft("etablissementinformations", "etablissementinformations.ID_ETABLISSEMENTINFORMATIONS = etablissementinformationspreventionniste.ID_ETABLISSEMENTINFORMATIONS")
                         ->where("etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(infos.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations as infos WHERE etablissementinformations.ID_ETABLISSEMENT = infos.ID_ETABLISSEMENT ) OR etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS IS NULL")
                         ->where("u.ACTIF_UTILISATEUR = 1")
                         ->order(array("utilisateurinformations.NOM_UTILISATEURINFORMATIONS", "utilisateurinformations.PRENOM_UTILISATEURINFORMATIONS"))
                         ->group("u.ID_UTILISATEUR");

                    break;
            }

            return $this;
        }

        public function joinEtablissementDossier() {
            $this->select
                    ->joinLeft("etablissementdossier", "etablissementdossier.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT")
                    ->joinLeft(array('dossiers' => "dossier"), "dossiers.ID_DOSSIER = etablissementdossier.ID_DOSSIER")
                    ->joinLeft("dossiernature", "dossiernature.ID_DOSSIER = dossiers.ID_DOSSIER");
        }

        // Filtre
        public function setCriteria( $key, $value = null, $exact = true, $clause = "where")
        {
            $string = null;

            if ( is_array($value) ) {

                for ($i=0; $i<count($value); $i++) {

                    $string .= $key . (( $exact ) ? "=" : " LIKE ") . $this->getAdapter()->quote((( $exact ) ? "" : "%") . $value[$i] . (( $exact ) ? "" : "%"));

                    if ( $i < count($value) - 1 ) {

                        $string .= " OR ";
                    }
                }

            } else if(is_null($value)) {
                $string = $key;

            } else {

                $string = $key . (( $exact ) ? "=" : " LIKE ") . $this->getAdapter()->quote((( $exact ) ? "" : "%") . $value . (( $exact ) ? "" : "%"));
            }

            $this->select->$clause( $string );

            return $this;
        }

        public function sup($key, $value)
        {
            $this->select->where($key . '>' . $this->getAdapter()->quote($value));

            return $this;
        }

        // Limiter les résultats
        public function limit( $value )
        {
            $this->select->limit( $value );

            return $this;
        }

        public function order( $value )
        {
            $this->select->order( $value );

            return $this;
        }

        public function columns( $array )
        {
            $this->select->columns( $array );

            return $this;
        }

        public function having( $value )
        {
            $this->select->having( $value );

            return $this;
        }
    }
