<?php

    /*
        Famille
    */

    class Model_DbTable_Famille extends Zend_Db_Table_Abstract
    {
        protected $_name="famille"; // Nom de la base
        protected $_primary = "ID_FAMILLE"; // Clé primaire

        public function fetchAllPK()
        {
            $all = $this->fetchAll()->toArray();
            $result = array();
            foreach ($all as $row) {
                $result[$row["ID_FAMILLE"]] = $row;
            }

            return $result;
        }
    }
