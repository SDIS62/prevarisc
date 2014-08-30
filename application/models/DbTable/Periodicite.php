<?php

    class Model_DbTable_Periodicite extends Zend_Db_Table_Abstract
    {
        protected $_name= "periodicite"; // Nom de la base
        protected $_primary = array("ID_CATEGORIE", "ID_TYPE", "LOCALSOMMEIL_PERIODICITE"); // Clé primaire

        public function gn4($categorie, $type, $local_sommeil)
        {
            // On check la périodicité par rapport au GE4
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("periodicite", "PERIODICITE_PERIODICITE")
                ->where("ID_CATEGORIE = ?", $categorie)
                ->where("ID_TYPE = ?", $type)
                ->where("LOCALSOMMEIL_PERIODICITE = ?", $local_sommeil);

            // Retourne le résultat
            $result = $this->getAdapter()->fetchOne($select);

            return $result === false ? "0" : $result;
        }

    }