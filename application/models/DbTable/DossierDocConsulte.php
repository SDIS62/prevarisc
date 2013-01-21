<?php

class Model_DbTable_DossierDocConsulte extends Zend_Db_Table_Abstract
{

	protected $_name="dossierdocconsulte"; // Nom de la base
	protected $_primary = array("ID_DOSSIERDOCCONSULTE"); // Clé primaire

	public function getGeneral($idDossier,$idNature,$idDoc) { 
		$select = "SELECT *
			FROM dossierdocconsulte
			WHERE ID_DOSSIER = '".$idDossier."'
			AND ID_NATURE = '".$idNature."'
			AND ID_DOC = '".$idDoc."'
		;";
		//echo $select;
		return $this->getAdapter()->fetchRow($select);
	}
}

?>