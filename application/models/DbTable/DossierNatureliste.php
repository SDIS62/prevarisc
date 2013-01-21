<?php

class Model_DbTable_DossierNatureliste extends Zend_Db_Table_Abstract
{
	protected $_name="dossiernatureliste"; // Nom de la base
	protected $_primary = "ID_DOSSIERNATURE"; // Clé primaire
	
	public function getDossierNature($type){ 
		$select = "SELECT *
			FROM dossiernatureliste
			WHERE ID_DOSSIERTYPE = '".$type."'
		;";
		//echo $select;
		return $this->getAdapter()->fetchAll($select);
	}
	
	
}
?>