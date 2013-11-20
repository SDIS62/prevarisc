<?php

class Model_DbTable_PrescriptionTexte extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptiontexte"; // Nom de la base
    protected $_primary = "ID_PRESCRIPTIONTEXTE"; // Clé primaire
	
	public function recupPrescriptionTexte($idCategorie)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array("pt" => "prescriptiontexte"))
			 ->where("ID_PRESCRIPTIONCAT = ?",$idCategorie)
			 ->order("pt.NUM_PRESCRIPTIONTEXTE");
			 
		return $this->getAdapter()->fetchAll($select);	

	}

	public function recupMaxNumTexte($idCategorie)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pt' => 'prescriptiontexte'), "max(pt.NUM_PRESCRIPTIONTEXTE) as maxnum")
			 ->where("ID_PRESCRIPTIONCAT = ?", $idCategorie);
			 
		return $this->getAdapter()->fetchRow($select);
	}
}
