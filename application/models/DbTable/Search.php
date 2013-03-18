<?php
    class Model_DbTable_Search extends Zend_Db_Table_Abstract
    {
        // Constructeur de la requete
        protected $_name="type"; // Nom de la base
        private $select = null;
        private $item = null;
        public $numpage = null;

        private $NB_ITEMS = 25;

        // On demare la recherche
        public function run( $id_etablissement_parent = false, $numero_de_page = null )
        {
            // Recherche par niveaux
            if ($id_etablissement_parent !== false) {

                if ($id_etablissement_parent === true || $id_etablissement_parent == 0) {

                    $this->select->where("etablissementlie.ID_ETABLISSEMENT IS NULL");
                } else {

                    $this->select->where("etablissementlie.ID_ETABLISSEMENT = " . $id_etablissement_parent);
                }
            }

            if ($numero_de_page != null && ($id_etablissement_parent === true || $id_etablissement_parent == 0)) {

                $this->numpage = $numero_de_page;

                $paginatorAdapt = new Zend_Paginator_Adapter_DbTableSelect($this->select);
                $items = $paginatorAdapt->getItems(($numero_de_page - 1) * $this->NB_ITEMS, $this->NB_ITEMS);

                $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($items->toArray()));
                $paginator->setItemCountPerPage($this->NB_ITEMS);
                $paginator->setCurrentPageNumber($numero_de_page);
                $liste = $paginator;

            } else {

                $liste = $this->fetchAll($this->select)->toArray();
            }
            // On execute la requete
            //$liste = $this->fetchAll($this->select)->toArray();
            // echo $this->select->__toString();

            // A FIX
            foreach ($liste as $key => $row) {
                if (($this->item == "etablissement" && Zend_Controller_Action_HelperBroker::getStaticHelper('Droits')->checkEtablissement($row["ID_ETABLISSEMENT"])) || ($this->item == "dossier" && Zend_Controller_Action_HelperBroker::getStaticHelper('Droits')->checkDossier($row["ID_DOSSIER"]))) {
                    unset($liste[$key]);
                }
            }

            return $liste;
        }

        // On set le type d'entité aque l'on recherche
        public function setItem( $item )
        {
            $this->item = $item;

            $this->select = $this->select()->setIntegrityCheck(false);

            switch ($item) {

                // Pour les établissements
                case "etablissement":

                    $this->select
                         ->from(array("e" => "etablissement"), "NUMEROID_ETABLISSEMENT")
                         ->columns(array("DATEVISITE_DOSSIER" => "( SELECT MAX( dossier.DATEVISITE_DOSSIER ) FROM etablissementdossier, dossier, dossiernature, etablissement
                                WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                                AND dossiernature.ID_DOSSIER = dossier.ID_DOSSIER
                                AND etablissementdossier.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT
                                AND etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT
                                AND dossiernature.ID_NATURE = '21'
                                AND ( dossier.TYPE_DOSSIER = '2' || dossier.TYPE_DOSSIER = '3')
                                GROUP BY etablissement.ID_ETABLISSEMENT)"))
                         ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )")
                         ->joinLeft("avis", "etablissementinformations.ID_AVIS = avis.ID_AVIS", "LIBELLE_AVIS")
                         ->join("genre", "etablissementinformations.ID_GENRE = genre.ID_GENRE", "LIBELLE_GENRE")
                         ->joinLeft("etablissementlie", "e.ID_ETABLISSEMENT = etablissementlie.ID_FILS_ETABLISSEMENT", array("pere" => "ID_ETABLISSEMENT", "ID_FILS_ETABLISSEMENT"))
                         ->joinLeft("etablissementinformationspreventionniste", "etablissementinformationspreventionniste.ID_ETABLISSEMENTINFORMATIONS = etablissementinformations.ID_ETABLISSEMENTINFORMATIONS", null)
                         ->joinLeft("utilisateur", "utilisateur.ID_UTILISATEUR = etablissementinformationspreventionniste.ID_UTILISATEUR", "ID_UTILISATEUR")
                         ->joinLeft("etablissementadresse", "e.ID_ETABLISSEMENT = etablissementadresse.ID_ETABLISSEMENT", array("NUMINSEE_COMMUNE", "LON_ETABLISSEMENTADRESSE", "LAT_ETABLISSEMENTADRESSE"))
                         ->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE")
                         ->order("etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS ASC")
                         ->group("ID_ETABLISSEMENT");

                    $this->select->columns("@enfants:= ( SELECT COUNT(etablissementlie.ID_FILS_ETABLISSEMENT)
                            FROM etablissement
                            INNER JOIN etablissementlie ON etablissement.ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT
                            WHERE etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT) AS NB_ENFANTS");

                        // $this->select->joinLeft(array("enfantstable" => "etablissement"), "enfantstable.ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT", array("NB_ENFANTS" => "COUNT(enfantstable.ID_ETABLISSEMENT)"));

                    break;

                // Pour les dossiers
                case "dossier":

                    $this->select
                         ->from(array("d" => "dossier"))
                         ->join("dossiernature", "dossiernature.ID_DOSSIER = d.ID_DOSSIER", null)
                         ->join("dossiernatureliste", "dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE", array("LIBELLE_DOSSIERNATURE", "ID_DOSSIERNATURE"))
                         ->join("dossiertype", "dossiertype.ID_DOSSIERTYPE = dossiernatureliste.ID_DOSSIERTYPE", "LIBELLE_DOSSIERTYPE")
                         ->joinLeft("dossierdocurba", "d.ID_DOSSIER = dossierdocurba.ID_DOSSIER", "NUM_DOCURBA")
                         ->joinLeft("etablissementdossier", "d.ID_DOSSIER = etablissementdossier.ID_DOSSIER", null)
                         ->joinLeft(array("e" => "etablissement"), "e.ID_ETABLISSEMENT = etablissementdossier.ID_ETABLISSEMENT", "ID_ETABLISSEMENT")
                         ->joinLeft("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )", "LIBELLE_ETABLISSEMENTINFORMATIONS")
                         ->joinLeft("avis", "d.AVIS_DOSSIER = avis.ID_AVIS");
                    break;

                // Pour les utilisateurs
                case "utilisateur":

                    $this->select
                         ->from(array("u" => "utilisateur"), array("uid" => "ID_UTILISATEUR"))
                         ->join("utilisateurinformations", "u.ID_UTILISATEURINFORMATIONS = utilisateurinformations.ID_UTILISATEURINFORMATIONS")
                         ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION", "LIBELLE_FONCTION")
                         ->joinLeft("utilisateurgrade", "utilisateurgrade.ID_UTILISATEUR = u.ID_UTILISATEUR", null)
                         ->joinLeft("grade", "utilisateurgrade.ID_GRADE = grade.ID_GRADE AND utilisateurgrade.DATE_UTILISATEURGRADE = ( SELECT MAX(utilisateurgrade.DATE_UTILISATEURGRADE) FROM utilisateurgrade WHERE utilisateurgrade.ID_UTILISATEUR = u.ID_UTILISATEUR )")
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
