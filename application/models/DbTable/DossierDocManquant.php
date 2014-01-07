<?php

class Model_DbTable_DossierDocManquant extends Zend_Db_Table_Abstract
{

    protected $_name="dossierdocmanquant"; // Nom de la base
    protected $_primary = "ID_DOCMANQUANT"; // Clé primaire
	
	
	public function getDocManquantDoss($idDossier)
	{
		//retourne la liste des catégories de prescriptions par ordre
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('ddm' => 'dossierdocmanquant'))
			 ->where("ddm.ID_DOSSIER = ?",$idDossier)
			 ->order("ddm.NUM_DOCSMANQUANT");
			 
		return $this->getAdapter()->fetchAll($select);	

	}
	
	public function getDocManquantDossLast($idDossier)
	{
		//retourne la liste des documents manquant (uniquement les derniers pour la génération de document)
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('ddm' => 'dossierdocmanquant'))
			 ->where("ddm.ID_DOSSIER = ?",$idDossier)
			 ->order("ddm.NUM_DOCSMANQUANT DESC");
			 
		return $this->getAdapter()->fetchRow($select);	

	}
	
	public function getDocManquantDossNum($idDossier,$num)
	{
		//retourne la liste des documents manquant (uniquement les derniers pour la génération de document)
		$select = $this->select()
			 ->setIntegrityCheck(false)
             ->from(array('ddm' => 'dossierdocmanquant'))
			 ->where("ddm.ID_DOSSIER = ?",$idDossier)
			 ->where("ddm.NUM_DOCSMANQUANT = ?",$num);
			 
		return $this->getAdapter()->fetchRow($select);	

	}
}
