<?php

class Model_DbTable_DossierAvis extends Zend_Db_Table_Abstract
{

	protected $_name="dossieravis"; // Nom de la base
	protected $_primary = "ID_DOSSIERAVIS"; // Clé primaire
			
	//Fonction qui récupère tous les doc de viste
	public function getDossierAvis($id_dossier) { 
		$select = "SELECT *
			FROM dossieravis, avis
			WHERE dossieravis.id_avis = avis.id_avis
			AND id_dossier = '".$id_dossier."'
			AND dossieravis.id_dossieravis = ( 
				SELECT MAX(id_dossieravis) FROM dossieravis
				WHERE id_dossier = '".$id_dossier."'
			)
		";
		//echo $select;	
		return $this->getAdapter()->fetchRow($select);
	}
	
	//Fonction utilisée pour la partie suivie elle affiche tous les différents changement d'avis concernant le dossier
	public function getListeDossierAvis($id_dossier) { 
		$select = "SELECT *
			FROM dossieravis, avis
			WHERE dossieravis.id_avis = avis.id_avis
			AND id_dossier = '".$id_dossier."'
			ORDER BY DATE_DOSSIERAVIS DESC
		";
		//echo $select;	
		return $this->getAdapter()->fetchAll($select);
	}
	
	public function getListeDossierAvisLast($id_dossier) { 
		$select = "SELECT *
			FROM dossieravis, avis
			WHERE dossieravis.id_avis = avis.id_avis
			AND id_dossier = '".$id_dossier."'
			AND dossieravis.date_dossieravis = ( 
				SELECT MAX(date_dossieravis) FROM dossieravis
				WHERE id_dossier = '".$id_dossier."'
			)
		";
		//echo $select;	
		return $this->getAdapter()->fetchRow($select);
	}

}

?>