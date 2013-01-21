<?php
	class Model_DbTable_AdresseCommune extends Zend_Db_Table_Abstract {
	
		protected $_name="adressecommune"; // Nom de la base
		protected $_primary = "NUMINSEE_COMMUNE"; // Clé primaire
		
		public function get($q) {
		
			$select = $this->select()->setIntegrityCheck(false);
		
			$select->from("adressecommune")
				   ->where("LIBELLE_COMMUNE LIKE ?", "%".$q."%");
				 
			return $this->fetchAll($select)->toArray();
		}
		
	}

?>