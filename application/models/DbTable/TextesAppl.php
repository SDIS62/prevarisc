<?php

    class Model_DbTable_TextesAppl extends Zend_Db_Table_Abstract
    {
        protected $_name="textesappl"; // Nom de la base
        protected $_primary = "ID_TEXTESAPPL"; // Clé primaire

		//récupération des textes applicables et de leurs type associé
		public function recupTextesAppl(){
			$select = $this->select()
				->setIntegrityCheck(false)
				->from(array('ta' => 'textesappl'))
				->join(array('ty' => 'typetextesappl'),'ta.ID_TYPETEXTEAPPL = ty.ID_TYPETEXTEAPPL')
				->order('ta.ID_TYPETEXTEAPPL')
				->order('ta.NUM_TEXTESAPPL');
				 
			return $this->getAdapter()->fetchAll($select);
		}
		
		public function recupTextesApplVisible(){
			$select = $this->select()
				->setIntegrityCheck(false)
				->from(array('ta' => 'textesappl'))
				->join(array('ty' => 'typetextesappl'),'ta.ID_TYPETEXTEAPPL = ty.ID_TYPETEXTEAPPL')
				->where('VISIBLE_TEXTESAPPL = 1')
				->order('ta.ID_TYPETEXTEAPPL')
				->order('ta.NUM_TEXTESAPPL');
				 
			return $this->getAdapter()->fetchAll($select);
		}
    }
