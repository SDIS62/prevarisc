<?php

class Model_DbTable_PrescriptionDossier extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptiondossier"; // Nom de la base
    protected $_primary = "ID_PRESCRIPTION_DOSSIER"; // Clé primaire

	public function recupMaxNumPrescDossier($idDossier)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pd' => 'prescriptiondossier'), "max(pd.NUM_PRESCRIPTION_DOSSIER) as maxnum")
			 ->where("ID_DOSSIER = ?", $idDossier);
			 
		return $this->getAdapter()->fetchRow($select);
	}
	
	public function recupPrescDossier($idDossier)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pd' => 'prescriptiondossier'))
			 ->where("pd.ID_DOSSIER = ?",$idDossier)
			 ->order("pd.NUM_PRESCRIPTION_DOSSIER");
			 
		return $this->getAdapter()->fetchAll($select);
	}
}
