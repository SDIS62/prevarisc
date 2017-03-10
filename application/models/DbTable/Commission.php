<?php

    class Model_DbTable_Commission extends Zend_Db_Table_Abstract
    {

        protected $_name="commission"; // Nom de la base
        protected $_primary = "ID_COMMISSION"; // Cl� primaire
        protected $_referenceMap = array(
                "commissiontype" => array(
                    "columns" => "ID_COMMISSIONTYPE",
                    "refTableClass" => "Model_DbTable_CommissionType",
                    "refColumns" => "ID_COMMISSIONTYPE",
                )
        );

        public function fetchAllPK()
        {
            $all = $this->getCommissions();
            $result = array();
            foreach ($all as $row) {
                $result[$row["ID_COMMISSION"]] = $row;
            }

            return $result;
        }

        // Donne la liste des cat�gories
        public function getCommissions( $id = null )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("commission")
                ->join("commissiontype", "commission.ID_COMMISSIONTYPE = commissiontype.ID_COMMISSIONTYPE");

            if ($id != null) {
                $select->where("ID_COMMISSION = $id");

                return $this->fetchRow($select)->toArray();
            } else

                return $this->fetchAll($select)->toArray();

        }

        // Donne la liste des cat�gories
        public function getCommissionsByType( $type )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("commission")
                ->join("commissiontype", "commissiontype.ID_COMMISSIONTYPE = commission.ID_COMMISSIONTYPE", null)
                ->where("commission.ID_COMMISSIONTYPE = $type");

            return $this->fetchAll($select)->toArray();
        }

        public function commissionListe($crit)
        {
            //Autocompl�tion sur la liste des commission
            $select = "SELECT ID_COMMISSION, LIBELLE_COMMISSION
                FROM commission
                WHERE LIBELLE_COMMISSION LIKE '%".$crit."%';
            ";

            return $this->getAdapter()->fetchAll($select);
        }

        public function getAllCommissions()
        {
            //Récupération de l'ensemble des commissions
            $select = "SELECT ID_COMMISSION, LIBELLE_COMMISSION
                FROM commission
                ORDER BY LIBELLE_COMMISSION
            ";

            return $this->getAdapter()->fetchAll($select);
        }

        public function getLibelleCommissions($id)
        {
            //Récupération de l'ensemble des commissions
            $select = "SELECT LIBELLE_COMMISSION
                FROM commission
                WHERE ID_COMMISSION = '".$id."'";
            return $this->getAdapter()->fetchAll($select);
        }



        public function commissionPeriodicite($idCommission)
        {
            $select = "SELECT commissiontype.FREQUENCE_COMMISSIONTYPE
                FROM commissiontype, commission
                WHERE commission.ID_COMMISSIONTYPE = commissiontype.ID_COMMISSIONTYPE
                AND commission.ID_COMMISSION = '".$idCommission."';
            ";
            //echo $select;
            return $this->getAdapter()->fetchRow($select);
        }

        public function getCommission( $commune, $categorie, $type, $localsommeil)
        {
            // Check de la sous commission / comunale / interco / arrondissement
            // R�cup�ration des types de commission
            $model_types = new Model_DbTable_CommissionType;
            $array_typesCommission = $model_types->fetchAll(null, "ID_COMMISSIONTYPE DESC")->toArray();

            foreach ($array_typesCommission as $row_typeCommission) {

                $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from("commissionregle", array("ID_GROUPEMENT", "NUMINSEE_COMMUNE", "ID_COMMISSION") )
                    ->joinLeft("commission", "commission.ID_COMMISSION = commissionregle.ID_COMMISSION", null)
                    ->joinLeft("commissionregletype", "commissionregle.ID_REGLE = commissionregletype.ID_REGLE", null)
                    ->joinLeft("commissionreglecategorie", "commissionregle.ID_REGLE = commissionreglecategorie.ID_REGLE", null)
                    ->joinLeft("commissionreglelocalsommeil", "commissionregle.ID_REGLE = commissionreglelocalsommeil.ID_REGLE", null)
                    ->joinLeft("adressecommune", "adressecommune.NUMINSEE_COMMUNE = commissionregle.NUMINSEE_COMMUNE", null)
                    ->where("commissionreglecategorie.ID_CATEGORIE = ?", $categorie)
                    ->where("commissionregletype.ID_TYPE = ?", $type)
                    ->where("commissionreglelocalsommeil.LOCALSOMMEIL = ?", $localsommeil)
                    ->where("commission.ID_COMMISSIONTYPE = ?", $row_typeCommission["ID_COMMISSIONTYPE"]);

                $results = $this->fetchAll($select);

                if ($results != null) {

                    foreach($results as $result) {

                        if ($result->NUMINSEE_COMMUNE != null) {
                            if ($result->NUMINSEE_COMMUNE == $commune) {

                                $result = $this->find( $result->ID_COMMISSION )->toArray();

                                return $result;
                            }
                        } elseif ($result->ID_GROUPEMENT) {

                            $model_groupementCommune = new Model_DbTable_GroupementCommune;
                            $row_groupement = $model_groupementCommune->fetchRow("ID_GROUPEMENT = '" . $result->ID_GROUPEMENT . "' AND NUMINSEE_COMMUNE = '" . $commune . "'");

                            if (count($row_groupement) == 1) {
                                $result = $this->find( $result->ID_COMMISSION )->toArray();

                                return $result;
                            }
                        }
                    }

                }

            }
        }
        
        public function getCommissionDossier($commune, $categorie, $type, $localsommeil, $etudevisite)
        {
        	// Check de la sous commission / comunale / interco / arrondissement
        	// R�cup�ration des types de commission
        	$model_types = new Model_DbTable_CommissionType;
        	$array_typesCommission = $model_types->fetchAll(null, "ID_COMMISSIONTYPE DESC")->toArray();
        
        	foreach ($array_typesCommission as $row_typeCommission) {
        
        		$select = $this->select()
        		->setIntegrityCheck(false)
        		->from("commissionregle", array("ID_GROUPEMENT", "NUMINSEE_COMMUNE", "ID_COMMISSION") )
        		->joinLeft("commission", "commission.ID_COMMISSION = commissionregle.ID_COMMISSION", null)
        		->joinLeft("commissionregletype", "commissionregle.ID_REGLE = commissionregletype.ID_REGLE", null)
        		->joinLeft("commissionreglecategorie", "commissionregle.ID_REGLE = commissionreglecategorie.ID_REGLE", null)
        		->joinLeft("commissionreglelocalsommeil", "commissionregle.ID_REGLE = commissionreglelocalsommeil.ID_REGLE", null)
        		->joinLeft("commissionregleetudevisite", "commissionregle.ID_REGLE = commissionregleetudevisite.ID_REGLE", null)
        		->joinLeft("adressecommune", "adressecommune.NUMINSEE_COMMUNE = commissionregle.NUMINSEE_COMMUNE", null)
        		->where("commissionreglecategorie.ID_CATEGORIE = ?", $categorie)
        		->where("commissionregletype.ID_TYPE = ?", $type)
        		->where("commissionreglelocalsommeil.LOCALSOMMEIL = ?", $localsommeil)
        		->where("commissionregleetudevisite.ETUDEVISITE = ?", $etudevisite)
        		->where("commission.ID_COMMISSIONTYPE = ?", $row_typeCommission["ID_COMMISSIONTYPE"]);
        
        		$results = $this->fetchAll($select);
        
        		if ($results != null) {
        
        			foreach($results as $result) {
        
        				if ($result->NUMINSEE_COMMUNE != null) {
        					if ($result->NUMINSEE_COMMUNE == $commune) {
        
        						$result = $this->find( $result->ID_COMMISSION )->toArray();
        
        						return $result;
        					}
        				} elseif ($result->ID_GROUPEMENT) {
        
        					$model_groupementCommune = new Model_DbTable_GroupementCommune;
        					$row_groupement = $model_groupementCommune->fetchRow("ID_GROUPEMENT = '" . $result->ID_GROUPEMENT . "' AND NUMINSEE_COMMUNE = '" . $commune . "'");
        
        					if (count($row_groupement) == 1) {
        						$result = $this->find( $result->ID_COMMISSION )->toArray();
        
        						return $result;
        					}
        				}
        			}
        
        		}
        
        	}
        }

        public function getCommissionIGH( $commune, $classe, $localsommeil)
        {
            // Check de la sous commission / comunale / interco / arrondissement
            // R�cup�ration des types de commission
            $model_types = new Model_DbTable_CommissionType;
            $array_typesCommission = $model_types->fetchAll("ID_COMMISSIONTYPE != 5")->toArray();

            foreach ($array_typesCommission as $row_typeCommission) {

                $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from("commissionregle", array("ID_GROUPEMENT", "NUMINSEE_COMMUNE", "ID_COMMISSION") )
                    ->joinLeft("commission", "commission.ID_COMMISSION = commissionregle.ID_COMMISSION", null)
                    ->joinLeft("commissionregleclasse", "commissionregle.ID_REGLE = commissionregleclasse.ID_REGLE", null)
                    ->joinLeft("commissionreglelocalsommeil", "commissionregle.ID_REGLE = commissionreglelocalsommeil.ID_REGLE", null)
                    ->joinLeft("adressecommune", "adressecommune.NUMINSEE_COMMUNE = commissionregle.NUMINSEE_COMMUNE", null)
                    ->where("commissionregleclasse.ID_CLASSE = ?", $classe)
                    ->where("commissionreglelocalsommeil.LOCALSOMMEIL = ?", $localsommeil)
                    ->where("commission.ID_COMMISSIONTYPE = ?", $row_typeCommission["ID_COMMISSIONTYPE"]);

                $results = $this->fetchAll($select);

                if ($results != null) {

                    foreach($results as $result) {
                        if ($result->NUMINSEE_COMMUNE != null) {
                            if ($result->NUMINSEE_COMMUNE == $commune) {

                                $result = $this->find( $result->ID_COMMISSION )->toArray();

                                return $result;
                            }
                        } elseif ($result->ID_GROUPEMENT) {

                            $model_groupementCommune = new Model_DbTable_GroupementCommune;
                            $row_groupement = $model_groupementCommune->fetchRow("ID_GROUPEMENT = '" . $result->ID_GROUPEMENT . "' AND NUMINSEE_COMMUNE = '" . $commune . "'");

                            if (count($row_groupement) == 1) {
                                $result = $this->find( $result->ID_COMMISSION )->toArray();

                                return $result;
                            }
                        }
                    }

                }

            }
        }

    }
