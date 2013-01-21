<?php
	class Model_DbTable_Utilisateur extends Zend_Db_Table_Abstract {
		protected $_name="utilisateur";
		protected $_primary = "ID_UTILISATEUR";
		
		// Mot de passe crypt� : md5($username."7aec3ab8e8d025c19e8fc8b6e0d75227".$passwd)
		
		public function getDroits($id_user) {
		
			$auth = Zend_Auth::getInstance()->getIdentity();
			
			// modèle
			$model_groupes = new Model_DbTable_Groupe;
			
			// Récupération du groupe de l'user
			if($auth->ID_UTILISATEUR != $id_user)
				$id_groupe = $this->find($id_user)->current()->ID_GROUPE;
			else
				$id_groupe = $auth->ID_GROUPE;
			
			// On retourne les droits de l'user
			return $model_groupes->getDroits($id_groupe);
		}
		
		public function getUsersWithInformations($group = null) {
		
			$this->select = $this->select()->setIntegrityCheck(false);
			$select = $this	 ->select
							 ->from(array("u" => "utilisateur"), array(
								 "uid" => "ID_UTILISATEUR",
								 "ID_UTILISATEUR",
								 "USERNAME_UTILISATEUR",
								 "PASSWD_UTILISATEUR",
								 "ID_UTILISATEURINFORMATIONS",
								 "ACTIF_UTILISATEUR",
								 "ID_GROUPE",
								 "SESSIONID_UTILISATEUR"
							))
							 ->join("utilisateurinformations", "u.ID_UTILISATEURINFORMATIONS = utilisateurinformations.ID_UTILISATEURINFORMATIONS")
							 ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION")
							 ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS ASC");
							 
			if( !empty($group) )
				$select->where("ID_GROUPE = $group");
				 
			return $this->fetchAll($select)->toArray();
		}
		
		public function isRegistered($login) {
		
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("utilisateur")
				->where("USERNAME_UTILISATEUR = ?", $login)
				->limit(1);
				
			$result = $this->fetchRow($select);
			
			return ( $result != null ) ? true : false;
		}
		
		public function getId($login) {
		
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("utilisateur", "ID_UTILISATEUR")
				->where("USERNAME_UTILISATEUR = ?", $login)
				->limit(1);
				
			$result = $this->fetchRow($select);
			
			return ( $result != null ) ? $result->ID_UTILISATEUR : null;
		}
		
		public function getCommissions($id) {
			
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("utilisateurcommission")
				->join("commission", "commission.ID_COMMISSION = utilisateurcommission.ID_COMMISSION")
				->where("ID_UTILISATEUR = ?", $id);
				
			return $this->fetchAll($select);
		}
		
		public function getCommissionsArray($id) {
			
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("utilisateurcommission", "ID_COMMISSION")
				->where("ID_UTILISATEUR = ?", $id);
				
			$all = $this->fetchAll($select);
			
			if( $all == null)
				return array();

			$all = $all->toArray();
			$result = array();
			foreach($all as $row) {
				$result[] = $row["ID_COMMISSION"];
			}
			return $result;
		}
		
		public function getGroupements($id) {
			
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("utilisateurgroupement")
				->join("groupement", "groupement.ID_GROUPEMENT = utilisateurgroupement.ID_GROUPEMENT")
				->where("ID_UTILISATEUR = ?", $id);
				
			return $this->fetchAll($select);
		}
		
		public function getVillesDeSesGroupements($id) {
		
			$model_groupementCommune = new Model_DbTable_GroupementCommune;
		
			$rowset_groupements = $this->getGroupements($id);
			
			$villes = array();
			
			// pr chq gpt on prend ses ville qu'on met ds un tableau
			foreach($rowset_groupements as $row_groupement) {
				
				foreach($model_groupementCommune->find($row_groupement->ID_GROUPEMENT) as $row) {
					$villes[] = $row->NUMINSEE_COMMUNE;
				}
			}
			
			// on enlève les doublons
			$villes = array_unique($villes);

			// on envoit
			return $villes;
		}
	}
?>