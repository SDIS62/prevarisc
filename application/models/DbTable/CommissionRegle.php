<?php

    class Model_DbTable_CommissionRegle extends Zend_Db_Table_Abstract
    {
        protected $_name="commissionregle"; // Nom de la base
        protected $_primary = array("ID_REGLE"); // Cl� primaire

        private function fullJoinRegle($first_table, $second_table, $key, $id_regle)
        {
            // On fait une union entre ce qu'il y a dans la base et les crit�res enregistr�
            $return = $this->fetchAll($this->select()->union(array(
                $this->select()->setIntegrityCheck(false)->from($first_table)->joinLeft($second_table, "$first_table.$key = $second_table.$key AND ID_REGLE = $id_regle"),
                $this->select()->setIntegrityCheck(false)->from($first_table)->joinRight($second_table, "$first_table.$key = $second_table.$key AND ID_REGLE = $id_regle")
            )))->toArray();

            // Requete sur la table finale
            $primary = $this->fetchAll($this->select()->setIntegrityCheck(false)->from($first_table))->toArray();

            // On limite les resultats
            $return = array_slice($return, 0, count($primary));

            // On rajoute les valeurs de toutes les cl� primaires
            foreach($return as $pos => $item) :	$return[$pos][$key] = $primary[$pos][$key]; endforeach;

            // On envoi le tout
            return $return;
        }

        // Formaliser les resultats envoy�s
        private function mapResult($array, $key)
        {
            $result = array();

            // On parcours le tableau
            foreach ($array as $value) {

                $result[] = $value[$key];
            }

            return $result;
        }

        public function get($id_commission)
        {
            // Mod�le de la commission
            $model_commission = new Model_DbTable_Commission;
            $model_commune = new Model_DbTable_AdresseCommune;

            // On r�cup�re les r�gles de la commission
            $rowset_reglesDeLaCommission = $this->fetchAll("ID_COMMISSION = " . $id_commission);

            // On initialise le tableau qui contiendra l'ensemble des crit�res
            $array_regles = array();

            // Pour chaques r�gles, on va chercher les crit�res
            foreach ($rowset_reglesDeLaCommission as $row_regleDeLaCommission) {
                $array_regles[] = array(
                    "id_regle" => $row_regleDeLaCommission["ID_REGLE"],
                    "commune" => ($row_regleDeLaCommission["NUMINSEE_COMMUNE"]) ? $model_commune->fetchRow("NUMINSEE_COMMUNE = " . $row_regleDeLaCommission["NUMINSEE_COMMUNE"]) : null,
                    "groupement" => $row_regleDeLaCommission["ID_GROUPEMENT"],
                    "categories" => $this->fullJoinRegle("categorie", "commissionreglecategorie", "ID_CATEGORIE", $row_regleDeLaCommission["ID_REGLE"]),
                    "classes" => $this->fullJoinRegle("classe", "commissionregleclasse", "ID_CLASSE", $row_regleDeLaCommission["ID_REGLE"]),
                    "types" => $this->fullJoinRegle("type", "commissionregletype", "ID_TYPE", $row_regleDeLaCommission["ID_REGLE"]),
                    "local_sommeil" => $this->mapResult($this->fetchAll($this->select()->setIntegrityCheck(false)->from("commissionreglelocalsommeil")->where("ID_REGLE = " . $row_regleDeLaCommission["ID_REGLE"]))->toArray(), "LOCALSOMMEIL"),
                    "etude_visite" => $this->mapResult($this->fetchAll($this->select()->setIntegrityCheck(false)->from("commissionregleetudevisite")->where("ID_REGLE = " . $row_regleDeLaCommission["ID_REGLE"]))->toArray(), "ETUDEVISITE"),
                    "infos" => $model_commission->fetchRow("ID_COMMISSION = " . $id_commission)
                );
            }

            // Zend_Debug::Dump($this->fetchAll($this->select()->setIntegrityCheck(false)->from("commissionreglelocalsommeil", "LOCALSOMMEIL")->where("ID_REGLE = " . $row_regleDeLaCommission["ID_REGLE"]))->toArray());
            return $array_regles;
        }

    }
