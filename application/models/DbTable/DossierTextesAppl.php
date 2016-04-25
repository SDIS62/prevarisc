<?php

    class Model_DbTable_DossierTextesAppl extends Zend_Db_Table_Abstract
    {
        protected $_name = "dossiertextesappl"; // Nom de la base
        protected $_primary = array("ID_TEXTESAPPL","ID_DOSSIER"); // Clé primaire

		public function recupTextesDossier($idDossier)
		{
			$select = $this->select()
				->from('dossiertextesappl','ID_TEXTESAPPL')
				->where("ID_DOSSIER = ?",$idDossier);
				 
			return $this->getAdapter()->fetchAll($select);
		}
		
		public function recupTextesDossierGenDoc($idDossier)
		{
			$select = $this->select()
				->setIntegrityCheck(false)
				->from(array('dta' =>'dossiertextesappl'))
				->join(array('ta' => 'textesappl'),'dta.ID_TEXTESAPPL = ta.ID_TEXTESAPPL')
				->join(array('tta' => 'typetextesappl'),'tta.ID_TYPETEXTEAPPL = ta.ID_TYPETEXTEAPPL')
				->where("dta.ID_DOSSIER = ?",$idDossier)
				->order("ta.ID_TYPETEXTEAPPL");
				
			//echo $select->__toString();
			
			return $this->getAdapter()->fetchAll($select);
		}
		
		 public function recupTextes($idDossier)
		{
			$select = $this->select()
				->setIntegrityCheck(false)
				->from(array('dta' =>'dossiertextesappl'))
				->join(array('ta' => 'textesappl'),'dta.ID_TEXTESAPPL = ta.ID_TEXTESAPPL')
				->join(array('tta' => 'typetextesappl'),'tta.ID_TYPETEXTEAPPL = ta.ID_TYPETEXTEAPPL')
				->where("dta.ID_DOSSIER = ?",$idDossier);

			$results = $this->fetchAll($select);

			return $results !== null ? $results->toArray() : array();
		}
    }	