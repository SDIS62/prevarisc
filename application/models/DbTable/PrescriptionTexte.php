<?php

class Model_DbTable_PrescriptionTexte extends Zend_Db_Table_Abstract
{

	protected $_name="prescriptiontexte"; // Nom de la base
	protected $_primary = "ID_TEXTE"; // Clé primaire

	public function selectTexte($crit){ 
		//Autocomplétion sur la liste des textes
		$select = "SELECT *
			FROM prescriptiontexte
			WHERE LIBELLE_TEXTE LIKE '".$crit."%';
		";
		return $this->getAdapter()->fetchAll($select);
	}
	
	public function verifTexteExiste($texte){
		$select = "SELECT ID_TEXTE
			FROM prescriptiontexte
			WHERE LIBELLE_TEXTE LIKE '".$texte."';
		";
		return $this->getAdapter()->fetchRow($select);
	}
}

?>