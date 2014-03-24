<?php

    class Model_DbTable_Periodicite extends Zend_Db_Table_Abstract
    {
        protected $_name= "periodicite"; // Nom de la base
        protected $_primary = array("ID_CATEGORIE", "ID_TYPE", "LOCALSOMMEIL_PERIODICITE"); // Cl� primaire

        public function gn4($categorie, $type, $local_sommeil)
        {
            // On check la p�riodicit� par rapport au GE4
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("periodicite", "PERIODICITE_PERIODICITE")
                ->where("ID_CATEGORIE = ?", $categorie)
                ->where("ID_TYPE = ?", $type)
                ->where("LOCALSOMMEIL_PERIODICITE = ?", $local_sommeil);

            // Retourne le r�sultat
            $result = $this->getAdapter()->fetchOne($select);

            return $result === false ? "0" : $result;
        }

    }