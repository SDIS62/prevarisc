<?php
    class Model_DbTable_TypeActivite extends Zend_Db_Table_Abstract
    {
        protected $_name="typeactivite"; // Nom de la base
        protected $_primary = "ID_TYPEACTIVITE"; // Clé primaire

        public function myfetchAll()
        {
        	$select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from("typeactivite")
                    ->join("type", "type.ID_TYPE = typeactivite.ID_TYPE")
                    ->order('type.LIBELLE_TYPE');
                    
            $result = $this->fetchAll($select);

            return $result == null ? null : $result->toArray();
        }
    }
