<?php

    /*
        Classe
    */

    class Model_DbTable_Classe extends Zend_Db_Table_Abstract
    {
        protected $_name="classe"; // Nom de la base
        protected $_primary = "ID_CLASSE"; // Clé primaire

        public function fetchAllPK()
        {
            $all = $this->fetchAll()->toArray();
            $result = array();
            foreach ($all as $row) {
                $result[$row["ID_CLASSE"]] = $row;
            }

            return $result;
        }
    }
