<?php

    class Model_DbTable_Admin extends Zend_Db_Table_Abstract
    {
        protected $_name="admin"; // Nom de la base
        protected $_primary="id"; // Nom de la base

        public function getParams()
        {
            $tmp = $this->fetchAll()->toArray();

            return $tmp[0];
        }

    }
