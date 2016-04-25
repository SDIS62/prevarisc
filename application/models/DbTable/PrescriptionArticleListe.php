<?php

class Model_DbTable_PrescriptionArticleListe extends Zend_Db_Table_Abstract
{
    protected $_name="prescriptionarticleliste"; // Nom de la base
    protected $_primary = "ID_ARTICLE"; // Clé primaire
	
	public function getAllArticles($visible = null)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('ptl' => 'prescriptionarticleliste'));

        if($visible != null)
			$select->where("ptl.VISIBLE_ARTICLE = ?", $visible);

		$select->order("ptl.LIBELLE_ARTICLE");
			 
		return $this->getAdapter()->fetchAll($select);
	}

	public function getArticle($idArticle)
	{
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->where("ID_ARTICLE = ?",$idArticle);
			 
		return $this->getAdapter()->fetchRow($select);
	}

	public function replace($idOldArticle, $idNewArticle){
		$data = array('ID_ARTICLE' => $idNewArticle);
		$where[] = "ID_ARTICLE = ".$idOldArticle;
		//MAJ des id des textes dans les tables : prescriptiondossierassoc, prescriptiontypeassoc
		$this->getAdapter()->update('prescriptiondossierassoc',$data,$where);
		$this->getAdapter()->update('prescriptiontypeassoc',$data,$where);
		$this->getAdapter()->update('prescriptionreglassoc',$data,$where);
		//Suppression du texte
		$this->delete("ID_ARTICLE = " . $idOldArticle);
	}
}
