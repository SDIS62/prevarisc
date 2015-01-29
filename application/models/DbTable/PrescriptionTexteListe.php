<?php

class Model_DbTable_PrescriptionTexteListe extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptiontexteliste"; // Nom de la base
    protected $_primary = "ID_TEXTE"; // Clé primaire
	
	public function getAllTextes()
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('ptl' => 'prescriptiontexteliste'))
			 ->order("ptl.LIBELLE_TEXTE");
			 
		return $this->getAdapter()->fetchAll($select);
	}

	public function getTexte($idTexte)
	{
		//retourne la liste des catégories de prescriptions par ordre
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
		//Suppression du texte
		$this->delete("ID_TEXTE = " . $idOldTexte);
	}
}
