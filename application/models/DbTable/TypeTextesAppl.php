<?php

    class Model_DbTable_TypeTextesAppl extends Zend_Db_Table_Abstract
    {
        protected $_name="typetextesappl"; // Nom de la base
        protected $_primary = "ID_TYPETEXTEAPPL"; // Clé primaire
		
		//récupération de la liste des types
		public function getType()
		{
			$select = $this->select()
				->from(array('ty' => 'typetextesappl'))
				->order('ty.ID_TYPETEXTEAPPL');
				 
			return $this->getAdapter()->fetchAll($select);
		}

    }
