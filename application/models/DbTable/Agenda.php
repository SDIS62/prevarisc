<?php

	/*
		Model Agenda
		Pour TYPE_AGENDA : 1 = Dossier, 2 = Préventionniste, 3 = Commission
	*/

	class Model_DbTable_Agenda extends Zend_Db_Table_Abstract
	{
		protected $_name="agenda"; // Nom de la base
		protected $_primary = "ID_AGENDA"; // Clé primaire
		
		//Fonction qui récupère toutes evenement d'un mois et d'une année donnée en fonction du type pour les afficher dans l'agenda
		public function getEvenement($type, $id, $mois, $annee) { 
			$select = "
				SELECT *
				FROM agenda, agenda_type_evenement
				WHERE agenda.TYPE_EVENEMENT = agenda_type_evenement.ID_TYPE_EVENEMENT
				AND TYPE_AGENDA = '".$type."'
				AND ID_CONCERNE = '".$id."'
				AND MONTH(DATE_DEBUT) = '".$mois."'
				AND YEAR(DATE_DEBUT) = '".$annee."'
			";
			//echo $select;
			return $this->getAdapter()->fetchAll($select);
		}
		
		
		
	}

?>