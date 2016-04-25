<?php

    /*
        Famille
    */

    class Model_DbTable_Famille extends Zend_Db_Table_Abstract
    {
        protected $_name="famille"; // Nom de la base
        protected $_primary = "ID_FAMILLE"; // Cl� primaire

        public function fetchAllPK()
        {
            $all = $this->fetchAll(null, 'LIBELLE_FAMILLE')->toArray();
            $result = array();
            foreach ($all as $row) {
                $result[$row["ID_FAMILLE"]] = $row;
            }
            
            $aucuneFamille = $result[1];
            unset($result[1]);
            array_unshift($result, $aucuneFamille);

            return $result;
        }
    }
