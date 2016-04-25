<?php

class Model_DbTable_PrescriptionCat extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptioncat"; // Nom de la base
    protected $_primary = "ID_PRESCRIPTION_CAT"; // Clé primaire
	
	public function recupPrescriptionCat()
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pc' => 'prescriptioncat'))
			 ->order('pc.NUM_PRESCRIPTION_CAT');
			 
		return $this->getAdapter()->fetchAll($select);	

	}
	
	public function recupMaxNumCat()
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pc' => 'prescriptioncat'), "max(pc.NUM_PRESCRIPTION_CAT) as maxnum");
			 
		return $this->getAdapter()->fetchRow($select);	

	}

}
