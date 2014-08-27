<?php

class Model_DbTable_DossierPj extends Zend_Db_Table_Abstract
{

    protected $_name="dossierpj"; // Nom de la base
    protected $_primary = array("ID_DOSSIER","ID_PIECEJOINTE");// Clé primaire
	
	public function getdossierpj($idDossier, $idPj)
	{
		//echo "les champs : ".$table.$champ.$identifiant."<br/>";
		$select = "SELECT *
			FROM dossierpj
			WHERE ID_DOSSIER = '".$idDossier."'
			AND ID_PIECEJOINTE = '".$idPj."'
		;";
		//echo $select;
		return $this->getAdapter()->fetchRow($select);
	}
	
	public function countcommissionpj($idDossier)
	{
		//echo "les champs : ".$table.$champ.$identifiant."<br/>";
		$select = "SELECT count(*) as nbcommpj
			FROM dossierpj
			WHERE ID_DOSSIER = '".$idDossier."'
			AND PJ_COMMISSION = '1'
		;";
		//echo $select;
		return $this->getAdapter()->fetchRow($select);
	}
}
