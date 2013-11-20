<?php

class Model_DbTable_PrescriptionType extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptiontype"; // Nom de la base
    protected $_primary = "ID_PRESCRIPTIONTYPE"; // Clé primaire
	
	public function getPrescriptionType($categorie,$texte,$article)
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array("pt" => "prescriptiontype"))
			->where("pt.PRESCRIPTIONTYPE_CATEGORIE = ?",$categorie)
			->where("pt.PRESCRIPTIONTYPE_TEXTE = ?",$texte)
			->where("pt.PRESCRIPTIONTYPE_ARTICLE = ?",$article);
			 
		return $this->getAdapter()->fetchAll($select);	
	}

}
