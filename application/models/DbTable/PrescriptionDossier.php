<?php

class Model_DbTable_PrescriptionDossier extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptiondossier"; // Nom de la base
    protected $_primary = "ID_PRESCRIPTION_DOSSIER"; // Clé primaire

	public function recupMaxNumPrescDossier($idDossier,$type)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pd' => 'prescriptiondossier'), "max(pd.NUM_PRESCRIPTION_DOSSIER) as maxnum")
			 ->where("ID_DOSSIER = ?", $idDossier)
			 ->where("TYPE_PRESCRIPTION_DOSSIER = ?",$type);

		return $this->getAdapter()->fetchRow($select);
	}
	
	public function recupPrescDossier($idDossier,$type)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pd' => 'prescriptiondossier'))
			 ->where("pd.ID_DOSSIER = ?",$idDossier)
			 ->where("pd.TYPE_PRESCRIPTION_DOSSIER = ?",$type)
			 ->order("pd.NUM_PRESCRIPTION_DOSSIER");
			 
		return $this->getAdapter()->fetchAll($select);
	}
	
	public function recupPrescInfos($id_prescription)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pd' => 'prescriptiondossier'))
			 ->where("pd.ID_PRESCRIPTION_DOSSIER = ?",$id_prescription);
			 
		return $this->getAdapter()->fetchRow($select);
	}
}
