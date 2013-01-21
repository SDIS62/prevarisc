<?php

	class Model_DbTable_DossierEvenement extends Zend_Db_Table_Abstract
	{

		protected $_name="dossierevenement"; // Nom de la base
		protected $_primary = array("DATE_DOSSIEREVENEMENT", "ID_DOSSIER"); // Clé primaire
		
		public function getDossierEvenement($id_dossier){
			$select = "SELECT dossierevenement.*, evenement.*
			FROM dossierevenement, evenement
			WHERE dossierevenement.id_dossier = '".$id_dossier."'
			AND dossierevenement.id_evenement = evenement.id_evenement
			ORDER BY dossierevenement.date_dossierevenement DESC";
			
			//return $select;
			return $this->getAdapter()->fetchAll($select);
		}
		
	}

?>