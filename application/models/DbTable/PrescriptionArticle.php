<?php

class Model_DbTable_PrescriptionArticle extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptionarticle"; // Nom de la base
    protected $_primary = "ID_PRESCRIPTIONARTICLE"; // Cl� primaire
	
	public function recupPrescriptionArticle($idTexte)
	{
		//retourne la liste des cat�gories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array("pa" => "prescriptionarticle"))
			 ->where("ID_PRESCRIPTIONTEXTE = ?",$idTexte)
			 ->order("pa.NUM_PRESCRIPTIONARTICLE");
			 
		return $this->getAdapter()->fetchAll($select);	

	}
	
	public function recupMaxNumArticle($idTexte)
	{
		//retourne la liste des cat�gories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('pa' => 'prescriptionarticle'), "max(pa.NUM_PRESCRIPTIONARTICLE) as maxnum")
			 ->where("ID_PRESCRIPTIONTEXTE = ?", $idTexte);
			 
		return $this->getAdapter()->fetchRow($select);
	}
}
