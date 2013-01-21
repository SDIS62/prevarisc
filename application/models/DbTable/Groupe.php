<?php

	/*
		Groupe
		Classe gérant les groupes, ainsi que les droits leur appartenant.

	*/

	class Model_DbTable_Groupe extends Zend_Db_Table_Abstract
	{
		protected $_name="groupe"; // Nom de la base
		protected $_primary = "ID_GROUPE"; // Clé primaire
		
		public function getDroits($id_groupe) {
		
			// Récupère tout les groupes
			$array_groupe = $this->find($id_groupe)->current()->toArray();

			$array_groupe["ID_GENRE"] = $this->getDroitsGenres($array_groupe["ID_GROUPE"]);
			$array_groupe["ID_TYPE"] = $this->getDroitsTypes($array_groupe["ID_GROUPE"]); 
			$array_groupe["ID_DOSSIERNATURE"] = $this->getDroitsNatures($array_groupe["ID_GROUPE"]);

			return (object) $array_groupe;
		}
		
		public function myFetchAll() {
		
			// Récupère tout les groupes
			$array_groupes = $this->fetchAll()->toArray();
			
			// On va ajouter des propriétés concernant les tables porteuses
			foreach($array_groupes as &$array_groupe) {

				$array_groupe["ID_GENRE"] = $this->getDroitsGenres($array_groupe["ID_GROUPE"]);
				$array_groupe["ID_TYPE"] = $this->getDroitsTypes($array_groupe["ID_GROUPE"]); 
				$array_groupe["ID_DOSSIERNATURE"] = $this->getDroitsNatures($array_groupe["ID_GROUPE"]);
				
				$array_groupe = (object) $array_groupe;
			}

			return (object) $array_groupes;
		}
		
		public function getDroitsGenres($id_groupe) {
		
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("groupegenre", array("ID_GENRE", "DROITLECTURE_GROUPEGENRE", "DROITECRITURE_GROUPEGENRE"))
				->where("ID_GROUPE = ?", $id_groupe);
				
			$rowset = $this->fetchAll($select);
			
			if($rowset == null) {
				return null;
			}
				
			$all = $rowset->toArray();
			$result = array();
			foreach($all as $row) {
				$result[$row["ID_GENRE"]] = $row;
			}
			return $result;
		}
		
		public function getDroitsTypes($id_groupe) {
		
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("groupetype", "ID_TYPE")
				->where("ID_GROUPE = ?", $id_groupe);
				
			$rowset = $this->fetchAll($select);
				
			if($rowset == null) {
				return array();
			}
				
			$all = $rowset->toArray();
			$result = array();
			foreach($all as $row) {
				$result[] = $row["ID_TYPE"];
			}
				
			return $result;
		}
		
		public function getDroitsNatures($id_groupe) {
		
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("groupenature", "ID_DOSSIERNATURE")
				->where("ID_GROUPE = ?", $id_groupe);
				
			$rowset = $this->fetchAll($select);
				
			if($rowset == null) {
				return array();
			}
				
			$all = $rowset->toArray();
			$result = array();
			foreach($all as $row) {
				$result[] = $row["ID_DOSSIERNATURE"];
			}
				
			return $result;
		}
		
	}

?>