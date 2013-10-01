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
    }	