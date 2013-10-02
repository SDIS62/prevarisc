<?php
    class Model_DbTable_Search extends Zend_Db_Table_Abstract
    {
        protected $_name = "type";
        private $select = null;
        private $item = null;
        public $numpage = null;
        private $nb_items = 100;

        // On demare la recherche
        public function run( $id_etablissement_parent = false, $numero_de_page = null )
        {
            // Recherche par niveaux
            if ($id_etablissement_parent !== false)
            {
                $this->select->where($id_etablissement_parent === true || $id_etablissement_parent == 0 ? "etablissementlie.ID_ETABLISSEMENT IS NULL" : "etablissementlie.ID_ETABLISSEMENT = " . $id_etablissement_parent);
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
                         ->from(array("e" => "etablissement"), "NUMEROID_ETABLISSEMENT")
                         ->columns(array(
                            "DATEVISITE_DOSSIER" => "( SELECT MAX( dossier.DATEVISITE_DOSSIER ) FROM etablissementdossier, dossier, dossiernature, etablissement
                                WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                                AND dossiernature.ID_DOSSIER = dossier.ID_DOSSIER
                                AND etablissementdossier.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT
                                AND etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT
                                AND dossiernature.ID_NATURE = '21'
                                AND ( dossier.TYPE_DOSSIER = '2' || dossier.TYPE_DOSSIER = '3')
                                GROUP BY etablissement.ID_ETABLISSEMENT)",
                            "NB_ENFANTS" => "( SELECT COUNT(etablissementlie.ID_FILS_ETABLISSEMENT)
                                FROM etablissement
                                INNER JOIN etablissementlie ON etablissement.ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT
                                WHERE etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT)"
                         ))
                         ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )")
                         ->joinLeft("avis", "etablissementinformations.ID_AVIS = avis.ID_AVIS", "LIBELLE_AVIS")
                         ->joinLeft("type", "etablissementinformations.ID_TYPE = type.ID_TYPE", "LIBELLE_TYPE")
                         ->join("genre", "etablissementinformations.ID_GENRE = genre.ID_GENRE", "LIBELLE_GENRE")
                         ->joinLeft("etablissementlie", "e.ID_ETABLISSEMENT = etablissementlie.ID_FILS_ETABLISSEMENT", array("pere" => "ID_ETABLISSEMENT", "ID_FILS_ETABLISSEMENT"))
                         ->joinLeft("etablissementinformationspreventionniste", "etablissementinformationspreventionniste.ID_ETABLISSEMENTINFORMATIONS = etablissementinformations.ID_ETABLISSEMENTINFORMATIONS", null)
                         ->joinLeft("utilisateur", "utilisateur.ID_UTILISATEUR = etablissementinformationspreventionniste.ID_UTILISATEUR", "ID_UTILISATEUR")
                         ->joinLeft("etablissementadresse", "e.ID_ETABLISSEMENT = etablissementadresse.ID_ETABLISSEMENT", array("NUMINSEE_COMMUNE", "LON_ETABLISSEMENTADRESSE", "LAT_ETABLISSEMENTADRESSE", "ID_ADRESSE"))
                         ->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_DEFAULT")
                         ->joinLeft(array("etablissementadressesite" => "etablissementadresse"), "etablissementadressesite.ID_ETABLISSEMENT = (SELECT ID_FILS_ETABLISSEMENT FROM etablissementlie WHERE ID_ETABLISSEMENT = e.ID_ETABLISSEMENT LIMIT 1)", null)
                         ->joinLeft(array("adressecommunesite" => "adressecommune"), "etablissementadressesite.NUMINSEE_COMMUNE = adressecommunesite.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_SITE")
                         ->joinLeft(array("etablissementadressecell" => "etablissementadresse"), "etablissementadressecell.ID_ETABLISSEMENT = (SELECT ID_ETABLISSEMENT FROM etablissementlie WHERE ID_FILS_ETABLISSEMENT = e.ID_ETABLISSEMENT LIMIT 1)", null)
                         ->joinLeft(array("adressecommunecell" => "adressecommune"), "etablissementadressecell.NUMINSEE_COMMUNE = adressecommunecell.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE AS LIBELLE_COMMUNE_ADRESSE_CELLULE")
                         ->order("etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS ASC")
                         ->group("e.ID_ETABLISSEMENT");

                    break;

                // Pour les dossiers
                case "dossier":

                    $this->select
                         ->from(array("d" => "dossier"))
                         ->columns(array(
                            "NOMS_ETABLISSEMENTS" => "( SELECT group_concat(DISTINCT etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS separator ', ')
                                FROM etablissementinformations
                                INNER JOIN etablissementdossier AS ets_d ON etablissementinformations.ID_ETABLISSEMENT = ets_d.ID_ETABLISSEMENT
                                WHERE ets_d.ID_DOSSIER = d.ID_DOSSIER AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS >= ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = ets_d.ID_ETABLISSEMENT )
                                )"
                         ))
                         ->join("dossiernature", "dossiernature.ID_DOSSIER = d.ID_DOSSIER", null)
                         ->join("dossiernatureliste", "dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE", array("LIBELLE_DOSSIERNATURE", "ID_DOSSIERNATURE"))
                         ->join("dossiertype", "dossiertype.ID_DOSSIERTYPE = dossiernatureliste.ID_DOSSIERTYPE", "LIBELLE_DOSSIERTYPE")
                         ->joinLeft("dossierdocurba", "d.ID_DOSSIER = dossierdocurba.ID_DOSSIER", "NUM_DOCURBA")
                         ->joinLeft(array("e" => "etablissementdossier"), "d.ID_DOSSIER = e.ID_DOSSIER", null)
                         ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )", "LIBELLE_ETABLISSEMENTINFORMATIONS")
                         ->joinLeft("avis", "d.AVIS_DOSSIER = avis.ID_AVIS")
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
                         ->group("u.ID_UTILISATEUR");

                    break;
            }

            return $this;
        }

        // Filtre
        public function setCriteria( $key, $value, $exact = true, $clause = "where")
        {
            $string = null;

            if ( is_array($value) ) {

                for ($i=0; $i<count($value); $i++) {

                    $string .= $key . (( $exact ) ? "=" : " LIKE ") . $this->getAdapter()->quote((( $exact ) ? "" : "%") . $value[$i] . (( $exact ) ? "" : "%"));

                    if ( $i < count($value) - 1 ) {

                        $string .= " OR ";
                    }
                }
            } else {

                $string = $key . (( $exact ) ? "=" : " LIKE ") . $this->getAdapter()->quote((( $exact ) ? "" : "%") . $value . (( $exact ) ? "" : "%"));
            }

            $this->select->$clause( $string );

            return $this;
        }

        // Limiter les résultats
        public function limit( $value )
        {
            $this->select->limit( $value );

            return $this;
        }
    }
