<?php

class Model_DbTable_PrescriptionTexteListe extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptiontexteliste"; // Nom de la base
    protected $_primary = "ID_TEXTE"; // Clé primaire
	
	public function getAllTextes($visible = null)
	{
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('ptl' => 'prescriptiontexteliste'));
		
		if($visible != null)
			$select->where("VISIBLE_TEXTE = ?", $visible);

		$select->order("ptl.LIBELLE_TEXTE");
			 
		return $this->getAdapter()->fetchAll($select);
	}

	public function getTexte($idTexte)
	{
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->where("ID_TEXTE = ?",$idTexte);
			 
		return $this->getAdapter()->fetchRow($select);
	}

	public function replace($idOldTexte, $idNewTexte){
		$data = array('ID_TEXTE' => $idNewTexte);
		$where[] = "ID_TEXTE = ".$idOldTexte;
		//MAJ des id des textes dans les tables : prescriptiondossierassoc, prescriptiontypeassoc
		$this->getAdapter()->update('prescriptiondossierassoc',$data,$where);
		$this->getAdapter()->update('prescriptiontypeassoc',$data,$where);
		$this->getAdapter()->update('prescriptionreglassoc',$data,$where);
		//Suppression du texte
		$this->delete("ID_TEXTE = " . $idOldTexte);
	}
}
