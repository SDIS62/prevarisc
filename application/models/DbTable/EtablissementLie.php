<?php
    class Model_DbTable_EtablissementLie extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissementlie";
        protected $_primary = array("ID_ETABLISSEMENT", "ID_FILS_ETABLISSEMENT");
		
		public function recupEtabCellule($idCellule)
		{
			//retourne le/les établissements qui sont père de la cellule
			$select = $this->select()
				->from(array('etabLie' => 'etablissementlie'))
				->where("ID_FILS_ETABLISSEMENT = ?",$idCellule);
			
			return $this->getAdapter()->fetchAll($select);
		}
    }
