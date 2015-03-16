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
	
	public function getPrescriptionTypeByWords($tabMotCles)
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array("pt" => "prescriptiontype"));
			//->join(array("pta" => "prescriptiontypeassoc") , "pt.ID_PRESCRIPTIONTYPE = pta.ID_PRESCRIPTIONTYPE")
			//->join(array("pal" => "prescriptionarticleliste"), "pal.ID_ARTICLE = pta.ID_ARTICLE")
			//->join(array("ptl" => "prescriptiontexteliste"), "ptl.ID_TEXTE = pta.ID_TEXTE");
			
		foreach($tabMotCles as $val => $ue)
		{
			$select->where("pt.PRESCRIPTIONTYPE_LIBELLE like '%".$ue."%'");
		}
		//echo $select->__toString();
		return $this->getAdapter()->fetchAll($select);
	}

	public function replaceId($idOldType, $idNewType){
		$data = array('ID_PRESCRIPTION_TYPE' => $idNewType);
		$where[] = "ID_PRESCRIPTION_TYPE = ".$idOldType;
		//MAJ des id des textes dans les tables : prescriptiondossierassoc, prescriptiontypeassoc
		$this->getAdapter()->update('prescriptiondossier',$data,$where);
	}
}
