<?php

	class Model_DbTable_EtablissementAdresse extends Zend_Db_Table_Abstract {

		protected $_name="etablissementadresse"; // Nom de la base
		protected $_primary = "ID_ADRESSE"; // Clé primaire
		
		public function get( $id_etablissement ) {
			
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("etablissementadresse")
				->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", array("LIBELLE_COMMUNE", "CODEPOSTAL_COMMUNE"))
				->joinLeft("adresserue", "etablissementadresse.ID_RUE = adresserue.ID_RUE AND etablissementadresse.NUMINSEE_COMMUNE = adresserue.NUMINSEE_COMMUNE", "LIBELLE_RUE")
				->where("etablissementadresse.ID_ETABLISSEMENT = '$id_etablissement'");
				
			return $this->fetchAll($select)->toArray();
		}

		// Donne la liste des rues
		public function getTypeRue( $id = null ) {
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("adresseruetype");
				
			if($id != null)
			{
				$select->where("ID_RUETYPE = $id");
				return $this->fetchRow($select)->toArray();
			}
			else
				return $this->fetchAll($select)->toArray();
		}
		
		// Donne la liste de ville par rapport à un code postal
		public function getVilleByCP( $code_postal ) {
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("adressecommune")
				->where("CODEPOSTAL_COMMUNE = '$code_postal'");
				
			return $this->fetchAll($select)->toArray();
		}
		
		// Retourne les types de voie d'une commune
		public function getTypesVoieByVille( $code_insee ) {
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("adresserue", null)
				->join("adresseruetype", "adresserue.ID_RUETYPE = adresseruetype.ID_RUETYPE")
				->where("NUMINSEE_COMMUNE = '$code_insee'")
				->group("ID_RUETYPE");
				
			return $this->fetchAll($select)->toArray();
		}
		
		// Retourne les voies par rapport à une ville et un type de voie
		public function getVoies( $code_insee, $q = null ) {
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("adresserue")
				->where("NUMINSEE_COMMUNE = '$code_insee'");
				
			if( $q != null ) {
				$select->where("LIBELLE_RUE LIKE ?", "%".$q."%");
			}
				
			return $this->fetchAll($select)->toArray();
		}
		
	}

?>