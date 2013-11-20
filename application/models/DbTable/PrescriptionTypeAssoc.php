<?php

class Model_DbTable_PrescriptionTypeAssoc extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptiontypeassoc"; // Nom de la base
    protected $_primary = array("ID_PRESCRIPTIONTYPE","NUM_PRESCRIPTIONASSOC"); // Clé primaire
	

	public function getPrescriptionAssoc($idPrescriptionType)
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array("pt" => "prescriptiontype"))
			->join(array("pta" => "prescriptiontypeassoc") , "pt.ID_PRESCRIPTIONTYPE = pta.ID_PRESCRIPTIONTYPE")
			->join(array("pal" => "prescriptionarticleliste"), "pal.ID_ARTICLE = pta.ID_ARTICLE")
			->join(array("ptl" => "prescriptiontexteliste"), "ptl.ID_TEXTE = pta.ID_TEXTE")
			->where("pta.ID_PRESCRIPTIONTYPE = ?",$idPrescriptionType)
			->order("pta.NUM_PRESCRIPTIONASSOC");
		
		//echo $select->__toString();
		return $this->getAdapter()->fetchAll($select);	
	}

}
