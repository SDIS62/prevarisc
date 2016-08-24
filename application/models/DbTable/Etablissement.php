<?php
    class Model_DbTable_Etablissement extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissement";
        protected $_primary = "ID_ETABLISSEMENT";

        public function getTypesActivitesSecondaires($id_ets_info)
        {
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from("etablissementinformationstypesactivitessecondaires")
                    ->join("type", "ID_TYPE_SECONDAIRE = ID_TYPE", "LIBELLE_TYPE")
                    ->join("typeactivite", "ID_TYPEACTIVITE_SECONDAIRE = ID_TYPEACTIVITE", "LIBELLE_ACTIVITE")
                    ->where("ID_ETABLISSEMENTINFORMATIONS = ?", $id_ets_info);

            $result = $this->fetchAll($select);
            return $result == null ? null : $result->toArray();
        }

        public function getInformations($id_etablissement)
        {
            $DB_information = new Model_DbTable_EtablissementInformations;

            $select = $DB_information->select()
                ->setIntegrityCheck(false)
                ->from("etablissementinformations")
                ->where("ID_ETABLISSEMENT = '$id_etablissement'")
                ->where("DATE_ETABLISSEMENTINFORMATIONS = (select max(DATE_ETABLISSEMENTINFORMATIONS) from etablissementinformations where ID_ETABLISSEMENT = '$id_etablissement' ) ");

            return $DB_information->fetchRow($select);
        }

        public function getLibelle( $id_etablissement )
        {
            $select = $this->select()->setIntegrityCheck(false);

            $select	->from(array("e" => "etablissement"), null)
                    ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT", "LIBELLE_ETABLISSEMENTINFORMATIONS")
                    ->where("etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )")
                    ->order("etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS ASC")
                    ->where("e.ID_ETABLISSEMENT = ?", $id_etablissement);

            return ( $this->fetchRow( $select ) != null ) ? $this->fetchRow( $select )->toArray() : null;
        }

        public function getParent( $id_etablissement )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("etablissementlie", null)
                ->joinLeft("etablissementinformations", "etablissementinformations.ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT")
                ->joinLeft("categorie", "categorie.ID_CATEGORIE = etablissementinformations.ID_CATEGORIE")
                ->where("etablissementlie.ID_FILS_ETABLISSEMENT = '$id_etablissement'")
                ->where("DATE_ETABLISSEMENTINFORMATIONS = (select max(DATE_ETABLISSEMENTINFORMATIONS) from etablissementinformations where ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT ) ");

            return ( $this->fetchRow( $select ) != null ) ? $this->fetchRow( $select )->toArray() : null;
        }

        public function getAllParents( $id_etablissement )
        {
            $result = $this->getParent($id_etablissement);

            if($result == null)

                return $result;
            else
                return array($result, $this->getParent($result["ID_ETABLISSEMENT"]));
        }

        public function getDiaporama($id_etablissement)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("etablissementpj", null)
                ->joinLeft("piecejointe", "piecejointe.ID_PIECEJOINTE = etablissementpj.ID_PIECEJOINTE")
                ->where("EXTENSION_PIECEJOINTE = '.jpg' OR EXTENSION_PIECEJOINTE = '.JPG' OR EXTENSION_PIECEJOINTE = '.jpeg' OR EXTENSION_PIECEJOINTE = '.png'")
                ->where("PLACEMENT_ETABLISSEMENTPJ = 1")
                ->where("etablissementpj.ID_ETABLISSEMENT = " . $id_etablissement);

            return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select )->toArray() : null;
        }

        public function getPlans($id_etablissement)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("etablissementpj", null)
                ->joinLeft("piecejointe", "piecejointe.ID_PIECEJOINTE = etablissementpj.ID_PIECEJOINTE")
                ->where("EXTENSION_PIECEJOINTE = '.jpg' OR EXTENSION_PIECEJOINTE = '.JPG' OR EXTENSION_PIECEJOINTE = '.png'")
                ->where("PLACEMENT_ETABLISSEMENTPJ = 2")
                ->where("etablissementpj.ID_ETABLISSEMENT = " . $id_etablissement);

            return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select )->toArray() : null;
        }

        public function getPlansInformations($id_etablissement_informations)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("etablissementinformationsplan")
                ->join("typeplan", "etablissementinformationsplan.ID_TYPEPLAN = typeplan.ID_TYPEPLAN")
                ->where("etablissementinformationsplan.ID_ETABLISSEMENTINFORMATIONS = ?", $id_etablissement_informations);

            return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select )->toArray() : null;
        }

        public function listeDesERPOuvertsSousAvisDefavorable($idsCommission = null, $numInseeCommune = null, $idUtilisateur = null)
        {
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("avis.ID_AVIS", 2);
            $search->setCriteria("etablissementinformations.ID_GENRE", array(2));
            $search->setCriteria("etablissementinformations.ID_STATUT", 2);
            if ($numInseeCommune) {
                $search->setCriteria("etablissementadresse.NUMINSEE_COMMUNE", $numInseeCommune);
            }
            if ($idsCommission) {
                $search->setCriteria("etablissementinformations.ID_COMMISSION", (array) $idsCommission);
            }
            if ($idUtilisateur) {
                $search->setCriteria("utilisateur.ID_UTILISATEUR", $idUtilisateur);
            }
            return $search->run(false, null, false)->toArray();
        }

        public function listeERPSansPreventionniste()
        {
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->setCriteria("etablissementinformations.ID_STATUT", 2);
            $search->setCriteria("utilisateur.ID_UTILISATEUR IS NULL");
            $search->sup("etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS", 0);
             //etablissementinformations.ID_ETABLISSEMENTINFORMATIONS not in (SELECT ID_ETABLISSEMENTINFORMATIONS FROM etablissementinformationspreventionniste)
            return $search->run(false, null, false)->toArray();
        }

        public function listeErpOuvertsSansProchainesVisitePeriodiques($idsCommission)
        {
            $search = new Model_DbTable_Search;
            $search->setItem("etablissement");
            $search->columns(array(
                "nextvisiteyear" => new Zend_Db_Expr("YEAR(DATE_ADD(dossiers.DATEVISITE_DOSSIER, INTERVAL etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS MONTH))"),
            ));
            $search->joinEtablissementDossier();
            $search->setCriteria("dossiers.DATEVISITE_DOSSIER = ( "
                    . "SELECT MAX(dos.DATEVISITE_DOSSIER) FROM dossier as dos "
                    . "LEFT JOIN etablissementdossier etabdoss ON etabdoss.ID_DOSSIER = dos.ID_DOSSIER "
                    . "LEFT JOIN dossiernature dn ON dn.ID_DOSSIER = dos.ID_DOSSIER "
                    . "WHERE etabdoss.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT "
                    . "AND dos.TYPE_DOSSIER IN(2,3) "
                    . "AND dn.ID_NATURE IN (21,23,24,26,28,29,47,48))");
            $search->setCriteria("etablissementinformations.ID_STATUT", 2);
            $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            $search->sup("etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS", 0);
            if ($idsCommission) {
                $search->setCriteria("etablissementinformations.ID_COMMISSION", (array) $idsCommission);
            }
            $search->having("nextvisiteyear <= YEAR(NOW())");
             //etablissementinformations.ID_ETABLISSEMENTINFORMATIONS not in (SELECT ID_ETABLISSEMENTINFORMATIONS FROM etablissementinformationspreventionniste)
            $etablissements_isoles = $search->run(false, null, false)->toArray();

            return $etablissements_isoles;

        }

        public function getDossierDonnantAvis($id_etablissement)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("dossier", array("ID_DOSSIER", "DATECOMM_DOSSIER", "DATEVISITE_DOSSIER", "AVIS_DOSSIER_COMMISSION"))
                ->join("etablissementdossier", "etablissementdossier.ID_DOSSIER = dossier.ID_DOSSIER")
                ->join("dossiernature", "dossiernature.ID_DOSSIER = etablissementdossier.ID_DOSSIER", null)
                ->where("etablissementdossier.ID_ETABLISSEMENT = ?", $id_etablissement)
                ->where("dossiernature.ID_NATURE in (?)", array(19, 7, 17, 16, 21, 23, 24, 47, 26, 28, 29, 48))
                ->where("dossier.AVIS_DOSSIER_COMMISSION IS NOT NULL")
                ->where("dossier.AVIS_DOSSIER_COMMISSION > 0")
                ->order("IFNULL(dossier.DATECOMM_DOSSIER, dossier.DATEVISITE_DOSSIER) DESC");

            return $this->fetchRow($select);
        }





    }
