<?php
    class Model_DbTable_Etablissement extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissement";
        protected $_primary = "ID_ETABLISSEMENT";

        public function getListeChamps()
        {
            return array (
                    "Site" => array( "periodicite", "etablissement_lies", "preventionnistes" ),
                    "Établissement" => array( "adresse", "categorie", "periodicite", "r123_20", "type_principal", "activite_principale", "types_secondaires", "activite_secondaires", "commission", "local_sommeil", "effectifs", "effectif_public", "effectif_personnel", "effectif_heberge", "effectif_total", "preventionnistes", "avis", "etablissement_lies", "datepc", "dangerosite", "mise_secu" ),
                    "Cellule" => array( "adresse_cellule", "r123_20", "type_principal", "activite_principale", "types_secondaires", "activite_secondaires", "local_sommeil", "effectifs", "effectif_public", "effectif_personnel", "effectif_heberge", "effectif_total", "preventionnistes", "avis", "periodicite", "categorie", "numerotation", "etablissement_lies", "datepc" ),
                    "Habitation" => array( "adresse", "famille", "preventionnistes", "datepc"),
                    "IGH" => array( "adresse", "classe", "periodicite", "commission", "local_sommeil", "effectifs", "effectif_public", "effectif_personnel", "effectif_heberge", "effectif_total", "preventionnistes", "avis", "datepc" ),
                    "EIC" => array( "icpe", "adresse", "effectifs", "effectif_personnel", "effectif_total", "preventionnistes", "datepc" )
                );
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

        // Valeurs par d�faut d'un �tablissement
        // P�riodicit�
        public function getDefaultPeriodicite($request)
        {
          if ((!isset($request["PERIODICITE_ETABLISSEMENTINFORMATIONS"]) || $request["PERIODICITE_ETABLISSEMENTINFORMATIONS"] != "") && $request["ID_GENRE"] != 3) {
              return null;
          }

            $DB_periodicite = new Model_DbTable_Periodicite;

            switch ($request["ID_GENRE"]) {

                // La plus contraignante
                case 1:
                    $periodicite = null;
                    foreach ($request["ID_FILS_ETABLISSEMENT"] as $item) {
                        if ($item > 0) {

                            $tmp = $this->getInformations( $item )->PERIODICITE_ETABLISSEMENTINFORMATIONS;
                            if($periodicite == null || $tmp < $periodicite)
                                $periodicite = $tmp;
                        }
                    }

                    return ($periodicite) ? $periodicite : "0";
                    break;

                // Utilise le GE4
                case 2:
                    return $DB_periodicite->gn4( $request["ID_CATEGORIE"], $request["ID_TYPE"], isset($request["LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS"]));
                    break;

                // Utilise celle du parent
                case 3:
                    return $this->getInformations( $request["ID_PERE"] )->PERIODICITE_ETABLISSEMENTINFORMATIONS;
                    break;

                // Selon la classe
                case 5:
                    return $DB_periodicite->gn4( 0, $request["ID_CLASSE"], false);
                    break;
            }
        }

        // Cat�gorie
        public function getDefaultCategorie($request)
        {
              if ((!isset($request["ID_CATEGORIE"]) || $request["ID_CATEGORIE"] != 0) && $request["ID_GENRE"] != 3) {
                  return null;
              }

            switch ($request["ID_GENRE"]) {

                // La plus grande des établissements enfants
                case 1:
                    $categorie = null;
                    if (count($request["ID_FILS_ETABLISSEMENT"]) == 0) {
                        return null;
                    }
                    foreach ($request["ID_FILS_ETABLISSEMENT"] as $item) {
                        if ($item > 0) {

                            //Cat�gorie
                            $tmp = $this->getInformations( $item )->ID_CATEGORIE;
                            if($categorie == null || $tmp < $categorie)
                                $categorie = $tmp;
                        }
                    }

                    return ($categorie) ? $categorie : "5";
                    break;

                case 2:
                    if($request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] > 0 && $request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] <= 10)

                        return 5;
                    elseif($request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] > 10 && $request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] <= 300)
                        return 4;
                    elseif($request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] > 300 && $request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] <= 700)
                        return 3;
                    elseif($request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] > 700 && $request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] <= 1500)
                        return 2;
                    elseif($request["EFFECTIFTOTAL_ETABLISSEMENTINFORMATIONS"] > 1500 )
                        return 1;
                    break;

                case 3:
                    return $this->getInformations( $request["ID_PERE"] )->ID_CATEGORIE;
                    break;

                    return 5;
                    break;
            }
        }

        // Commission
        public function getDefaultCommission($request)
        {
            if(isset($request["NUMINSEE_COMMUNE"]))
            {
                $index = isset($request["NUMINSEE_COMMUNE"][1]) && $request["NUMINSEE_COMMUNE"][1] != "" ? 1 : 0;

                switch ($request["ID_GENRE"]) {
                    case 2:
                        $model_commission = new Model_DbTable_Commission;

                        return $model_commission->getCommission($request["NUMINSEE_COMMUNE"][$index], $request["ID_CATEGORIE"], $request["ID_TYPE"], isset($request["LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS"]) && $request["LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS"] == 1 ? true : false);
                        break;
                    case 5:
                        $model_commission = new Model_DbTable_Commission;

                        return $model_commission->getCommissionIGH($request["NUMINSEE_COMMUNE"][$index], $request["ID_CLASSE"], isset($request["LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS"]) && $request["LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS"] == 1 ? true : false);
                        break;
                }
            }
            else
            {
                return null;
            }
        }

        // Pr�ventionnistes
        public function getDefaultPrev($request)
        {
            if(isset($request["NUMINSEE_COMMUNE"]))
            {
                $index = isset($request["NUMINSEE_COMMUNE"][1]) && $request["NUMINSEE_COMMUNE"][1] != "" ? 1 : 0;
                include_once 'Preventioniste.php';
                $model_prev = new Model_DbTable_Preventionniste;

                return $model_prev->getPrev($request["NUMINSEE_COMMUNE"][$index], isset($request["ID_PERE"]) ? $request["ID_PERE"] : '');
            }
            else
            {
                return null;
            }
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

        public function getVisiteLastPeriodique($id_etablissement)
        {
            $select = "SELECT MAX( dossier.DATEVISITE_DOSSIER ) AS DATEVISITE_DOSSIER FROM etablissementdossier, dossier, dossiernature, etablissement
                WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                AND dossiernature.ID_DOSSIER = dossier.ID_DOSSIER
                AND etablissementdossier.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT
                AND etablissement.ID_ETABLISSEMENT = '".$id_etablissement."'
                AND dossiernature.ID_NATURE = '21'
                AND ( dossier.TYPE_DOSSIER = '2' || dossier.TYPE_DOSSIER = '3')
                AND UNIX_TIMESTAMP(dossier.DATEVISITE_DOSSIER) < UNIX_TIMESTAMP(NOW())
                GROUP BY etablissement.ID_ETABLISSEMENT";

            if(null != ($row = $this->getAdapter()->fetchRow($select)))

                return new Zend_Date($row["DATEVISITE_DOSSIER"], Zend_Date::DATES);
            else
                return null;
        }

        public function getVisiteNextPeriodique($id_etablissement)
        {
            $select = "SELECT MIN( dossier.DATEVISITE_DOSSIER ) AS DATEVISITE_DOSSIER FROM etablissementdossier, dossier, dossiernature, etablissement
                WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                AND dossiernature.ID_DOSSIER = dossier.ID_DOSSIER
                AND etablissementdossier.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT
                AND etablissement.ID_ETABLISSEMENT = '".$id_etablissement."'
                AND dossiernature.ID_NATURE = '21'
                AND ( dossier.TYPE_DOSSIER = '2' || dossier.TYPE_DOSSIER = '3')
                AND UNIX_TIMESTAMP(dossier.DATEVISITE_DOSSIER) >= UNIX_TIMESTAMP(NOW())
                GROUP BY etablissement.ID_ETABLISSEMENT";

            if (null != ($row = $this->getAdapter()->fetchRow($select))) {
                return new Zend_Date($row["DATEVISITE_DOSSIER"], Zend_Date::DATES);
            } else {

                $last_visite = $this->getVisiteLastPeriodique( $id_etablissement );

                if ($last_visite != null) {

                    $date = new Zend_Date($last_visite, Zend_Date::DATES);
                    $date->add($this->getPeriodicite($id_etablissement), Zend_Date::MONTH);

                    return $date;
                } else {
                    return null;
                }
            }
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
                ->where("PLACEMENT_ETABLISSEMENTPJ = 0")
                ->where("etablissementpj.ID_ETABLISSEMENT = " . $id_etablissement);

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
