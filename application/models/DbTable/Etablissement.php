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

        // NOTE : à faire après enregistrement d'un établissement
        public function getIDWinprev($id)
        {
            $model_adresse = new Model_DbTable_EtablissementAdresse;

            // Variables
            $genre = $codecommune = $nbetscommune = $rangcell = $commission = null;

            // Récupération des infos de l'établissement
            $infos = $this->getInformations($id);
            $adresses = $model_adresse->get($id);
            $parent = $this->getParent($id);

            // Vérifications
            if ($infos == null) {
                return false;
            }

            if ($infos->ID_GENRE == null || count($adresses) == 0) {
                return false;
            }

            // Etape 1 : genre
            switch ($infos->ID_GENRE) {
                case 1: $genre = "S"; break;
                case 2: $genre = "E"; break;
                case 3: $genre = "B"; break;
                case 4: $genre = "H"; break;
                case 5: $genre = "G"; break;
                case 6: $genre = "I"; break;
            }

            // Etape 2 : Code commune
            if($genre != "S" || $genre != "C" || count($adresses) > 0)
            {
                $codecommune = str_pad($adresses[0]["NUMINSEE_COMMUNE"], 6, "0", STR_PAD_LEFT);
            }
            else
            {
                $codecommune = "000000";
            }

            // Etape 3 : Ordre sur la commune
            if($genre != "S" || $genre != "C" || count($adresses) > 0)
            {
                $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->distinct()
                    ->from("adressecommune", null)
                    ->join("etablissementadresse", "etablissementadresse.NUMINSEE_COMMUNE =adressecommune.NUMINSEE_COMMUNE", "etablissementadresse.ID_ETABLISSEMENT")
                    ->join("etablissement", "etablissementadresse.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT", null)
                    ->where("adressecommune.NUMINSEE_COMMUNE = ?", $adresses[0]["NUMINSEE_COMMUNE"])
                    ->where("etablissement.DATEENREGISTREMENT_ETABLISSEMENT  <= ( SELECT etablissement.DATEENREGISTREMENT_ETABLISSEMENT FROM etablissement WHERE etablissement.ID_ETABLISSEMENT = '".($genre == "B" ? $parent["ID_ETABLISSEMENT"] : $id)."')");
                $nbetscommune = str_pad(count($this->fetchAll($select)), 5, "0", STR_PAD_LEFT);
            }
            else
            {
                $nbetscommune = "00000";
            }

            // Etape 4 : Rang de la cellule
            if ($genre == "B") {
                $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from("etablissementlie")
                    ->join("etablissement", "etablissement.ID_ETABLISSEMENT = etablissementlie.ID_FILS_ETABLISSEMENT", null)
                    ->where("etablissementlie.ID_ETABLISSEMENT = ?", $parent["ID_ETABLISSEMENT"])
                    ->where("etablissement.DATEENREGISTREMENT_ETABLISSEMENT  <= ( SELECT etablissement.DATEENREGISTREMENT_ETABLISSEMENT FROM etablissement WHERE etablissement.ID_ETABLISSEMENT = ?)", $id);
                $result = $this->fetchAll($select);
                $rangcell = str_pad($result == null ? 0 : count($result), 3, "0", STR_PAD_LEFT);
            } else {
                $rangcell = "000";
            }

            // Etape 5 : ID de la commission
            $commission = $infos->ID_COMMISSION == null ? "0" : $infos->ID_COMMISSION;

            return $genre . $codecommune . $nbetscommune . "-" . $rangcell  . "-" . $commission;
        }

        public function getByUser($id_user)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from(array("e" => "etablissement"), "ID_ETABLISSEMENT")
                ->joinLeft("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT", array("LIBELLE_ETABLISSEMENTINFORMATIONS", "PERIODICITE_ETABLISSEMENTINFORMATIONS"))
                ->joinLeft("etablissementinformationspreventionniste", "etablissementinformationspreventionniste.ID_ETABLISSEMENTINFORMATIONS = etablissementinformations.ID_ETABLISSEMENTINFORMATIONS", null)
                ->where("DATE_ETABLISSEMENTINFORMATIONS = (select max(DATE_ETABLISSEMENTINFORMATIONS) from etablissementinformations where ID_ETABLISSEMENT = e.ID_ETABLISSEMENT ) ")
                ->where("etablissementinformationspreventionniste.ID_UTILISATEUR = " . $id_user)
                ->where("etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT ) OR etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS IS NULL");

            return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select )->toArray() : null;
        }

        public function getInformations( $id_etablissement )
        {
            $DB_information = new Model_DbTable_EtablissementInformations;

            $select = $DB_information->select()
                ->setIntegrityCheck(false)
                ->from("etablissementinformations")
                ->where("ID_ETABLISSEMENT = '$id_etablissement'")
                ->where("DATE_ETABLISSEMENTINFORMATIONS = (select max(DATE_ETABLISSEMENTINFORMATIONS) from etablissementinformations where ID_ETABLISSEMENT = '$id_etablissement' ) ");

                //echo $select->__toString();
				//Zend_Debug::dump($DB_information->fetchRow($select));
            if ( $DB_information->fetchRow($select) != null ) {
                $result = $DB_information->fetchRow($select)->toArray();

                return $DB_information->find( $result["ID_ETABLISSEMENTINFORMATIONS"] )->current();
            } else

                return null;
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

        public function getPeriodicite( $id_etablissement )
        {
            $select = $this->select()->setIntegrityCheck(false);

            $select	->from(array("e" => "etablissement"), null)
                    ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT", "PERIODICITE_ETABLISSEMENTINFORMATIONS")
                    ->where("etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )")
                    ->order("etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS ASC")
                    ->where("e.ID_ETABLISSEMENT = ?", $id_etablissement);

            if(null != ($row = $this->getAdapter()->fetchRow($select)))

                return $row["PERIODICITE_ETABLISSEMENTINFORMATIONS"];
            else
                return null;
        }

        public function getGenre( $id_etablissement )
        {
            $select = $this->select()->setIntegrityCheck(false);

            $select	->from(array("e" => "etablissement"), null)
                    ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT", "ID_GENRE")
                    ->join("genre", "etablissementinformations.ID_GENRE = genre.ID_GENRE", "LIBELLE_GENRE")
                    ->where("etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )")
                    ->where("e.ID_ETABLISSEMENT = ?", $id_etablissement);

            return ( $this->fetchRow( $select ) != null ) ? $this->fetchRow( $select )->toArray() : null;
        }

        public function getParent( $id_etablissement )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("etablissementlie", null)
                ->joinLeft("etablissementinformations", "etablissementinformations.ID_ETABLISSEMENT = etablissementlie.ID_ETABLISSEMENT")
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

        // Recalcule les périod et cat des enfants d'un ets
        public function recalcEnfants($id_ets, $id_info, $historique)
        {
            $search = new Model_DbTable_Search;
            $etablissement_enfants = $search->setItem("etablissement")->setCriteria("etablissementlie.ID_ETABLISSEMENT", $id_ets)->run();
            $model_etablissementInformations = new Model_DbTable_EtablissementInformations;
            $etablissement = $model_etablissementInformations->find($id_info)->current();

            foreach ($etablissement_enfants as $ets) {

                // On récupère la fiche de l'établissement enfant
                $row_etablissement = $this->getInformations($ets["ID_ETABLISSEMENT"]);

                // Periodicité
                if ($etablissement->PERIODICITE_ETABLISSEMENTINFORMATIONS != $row_etablissement->PERIODICITE_ETABLISSEMENTINFORMATIONS) {

                    $row_etablissement->PERIODICITE_ETABLISSEMENTINFORMATIONS = $etablissement->PERIODICITE_ETABLISSEMENTINFORMATIONS;
                }

                // Catégorie
                if ($etablissement->ID_CATEGORIE != $row_etablissement->ID_CATEGORIE) {

                    $row_etablissement->ID_CATEGORIE = $etablissement->ID_CATEGORIE;
                }

                if ($historique) {

                    $row_etablissement->ID_ETABLISSEMENTINFORMATIONS = null;
                    $row_etablissement->DATE_ETABLISSEMENTINFORMATIONS = $etablissement->DATE_ETABLISSEMENTINFORMATIONS;

                    $new_row = $model_etablissementInformations->createRow();
                    $new_row->setFromArray($row_etablissement->toArray());
                    $new_row->save();
                } else {

                    $row_etablissement->save();
                }

            }
        }

    }
