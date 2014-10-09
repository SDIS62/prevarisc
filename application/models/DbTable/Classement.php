<?php

    /*
        Classement

        Cette classe sert pour recuperer les classement, et les administrer

    */

    class Model_DbTable_Classement extends Zend_Db_Table_Abstract
    {

        protected $_name="classement"; // Nom de la base
        protected $_primary = "ID_CLASSEMENT"; // Cle primaire
        
        
        public function fetchAllPK()
        {
            $all = $this->fetchAll()->toArray();
            $result = array();
            foreach ($all as $row) {
                $result[$row["ID_CLASSEMENT"]] = $row;
            }

            return $result;
        }
    }
