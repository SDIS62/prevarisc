<?php

    /*
        Type de plan

        Cette classe sert pour récupérer les catégories, et les administrer

    */

    class Model_DbTable_TypePlan extends Zend_Db_Table_Abstract
    {

        protected $_name="typeplan"; // Nom de la base
        protected $_primary = "ID_TYPEPLAN"; // Clé primaire

        public function fetchAllPK()
        {
            $all = $this->fetchAll()->toArray();
            $result = array();
            foreach ($all as $row) {
                $result[$row["ID_TYPEPLAN"]] = $row;
            }

            return $result;
        }

    }
