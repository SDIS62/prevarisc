<?php

    class Model_DbTable_EtablissementAdresse extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissementadresse"; // Nom de la base
        protected $_primary = "ID_ADRESSE"; // Cl� primaire

        public function get( $id_etablissement )
        {
            $model_etablissement = new Model_DbTable_Etablissement;
            $informations = $model_etablissement->getInformations($id_etablissement);

            switch($informations->ID_GENRE) {
                // Adresse d'un site
                case 1:
                    $search = new Model_DbTable_Search;

                    $etablissement_enfants = $search->setItem("etablissement")->setCriteria("etablissementlie.ID_ETABLISSEMENT", $id_etablissement)->run()->getAdapter()->getItems(0, 99999999999)->toArray();

                    if(count($etablissement_enfants) > 0) {
                        $i = 0;
                        foreach ($etablissement_enfants as $key => $ets) {
                            if (($ets["EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"] + $ets["EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]) > ($etablissement_enfants[$i]["EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"] + $etablissement_enfants[$i]["EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"])) {
                                $i = $key;
                            }
                        }
                        return $this->get($etablissement_enfants[$i]["ID_ETABLISSEMENT"]);
                    }
                    else {
                        return array();
                    }
                    break;

                // Adresse d'une cellule
                case 3:
                    // R�cup�ration des parents de l'�tablissement
                    $results = array();
                    $id_enfant = $id_etablissement;
                    do {
                        $parent = $model_etablissement->getParent($id_enfant);
                        if ($parent != null) {
                            $results[] = $parent;
                            $id_enfant = $parent["ID_ETABLISSEMENT"];
                        }
                    } while($parent != null);
                    $etablissement_parents = count($results) == 0 ? array() : array_reverse($results);

                    $pere = end($etablissement_parents);

                    if ($pere) {
                        return $this->get($pere["ID_ETABLISSEMENT"]);
                    }

                    return array();
                    break;

                // Adresse par d�faut
                default: 
                    $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from("etablissementadresse")
                        ->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", array("LIBELLE_COMMUNE", "CODEPOSTAL_COMMUNE"))
                        ->joinLeft("adresserue", "etablissementadresse.ID_RUE = adresserue.ID_RUE AND etablissementadresse.NUMINSEE_COMMUNE = adresserue.NUMINSEE_COMMUNE", "LIBELLE_RUE")
                        ->joinLeft("adresseruetype", "adresseruetype.ID_RUETYPE = adresserue.ID_RUETYPE", array("LIBELLE_RUETYPE", "ABREVIATION_RUETYPE"))
                        ->where("etablissementadresse.ID_ETABLISSEMENT = '$id_etablissement'");
                    return $this->fetchAll($select)->toArray();
            }
        }

        // Donne la liste des rues
        public function getTypeRue( $id = null )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("adresseruetype");

            if ($id != null) {
                $select->where("ID_RUETYPE = $id");

                return $this->fetchRow($select)->toArray();
            } else

                return $this->fetchAll($select)->toArray();
        }

        // Donne la liste de ville par rapport � un code postal
        public function getVilleByCP( $code_postal )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("adressecommune")
                ->where("CODEPOSTAL_COMMUNE = '$code_postal'");

            return $this->fetchAll($select)->toArray();
        }

        // Retourne les types de voie d'une commune
        public function getTypesVoieByVille( $code_insee )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("adresserue", null)
                ->join("adresseruetype", "adresserue.ID_RUETYPE = adresseruetype.ID_RUETYPE")
                ->where("NUMINSEE_COMMUNE = '$code_insee'")
                ->group("ID_RUETYPE");

            return $this->fetchAll($select)->toArray();
        }

        // Retourne les voies par rapport � une ville et un type de voie
        public function getVoies( $code_insee, $q = null )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("adresserue")
                ->where("NUMINSEE_COMMUNE = '$code_insee'");

            if ($q != null) {
                $select->where("LIBELLE_RUE LIKE ?", "%".$q."%");
            }

            return $this->fetchAll($select)->toArray();
        }

    }
