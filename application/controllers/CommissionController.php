<?php

	class CommissionController extends Zend_Controller_Action {
	
		public function init() {
		
			// On check si l'utilisateur peut accéder à cette partie
			if($this->_helper->Droits()->get()->DROITADMINPREV_GROUPE == 0)
				$this->_helper->Droits()->redirect();
		}
		
		public function deleteAction() {
			
			$this->_helper->viewRenderer->setNoRender();
			
			// Modèles de données
			$model_commission = new Model_DbTable_Commission;
			$model_ContactCommission = new Model_DbTable_CommissionContact;
			$model_MembreCommission = new Model_DbTable_CommissionMembre;
			$model_RegleCommission = new Model_DbTable_CommissionRegle;
			
			// Suppression des contacts
			foreach($model_ContactCommission->fetchAll("ID_COMMISSION = " . $this->_request->id) as $row) {
				$this->_helper->actionStack('delete', 'contact', 'default', array('item' => 'commission', 'id' => $row["ID_UTILISATEURINFORMATIONS"], 'id_item' => $row["ID_COMMISSION"]));
			}

			// Suppression des membres
			foreach($model_MembreCommission->fetchAll("ID_COMMISSION = " . $this->_request->id) as $row) {
				$this->_helper->actionStack('delete-membre', 'commission', 'default', array('id_membre' => $row["ID_COMMISSIONMEMBRE"]));
			}
			
			// Suppression des règles
			foreach($model_RegleCommission->fetchAll("ID_COMMISSION = " . $this->_request->id) as $row) {
				$this->_helper->actionStack('delete-regle', 'commission', 'default', array('id_regle' => $row["ID_REGLE"]));
			}
			
			// Suppression de la commission
			$model_commission->delete("ID_COMMISSION = " . $this->_request->id);
		}

		// Champ de compétence de la commission
		public function competencesAction() {

			// Les modèles
			$model_regles = new Model_DbTable_CommissionRegle;

			// On récupère les règles de la commission
			$this->view->array_regles = $model_regles->get($this->_request->id_commission);
		}
		
		public function addRegleAction() {
			
			$this->_helper->viewRenderer->setNoRender();
			
			// Les modèles
			$model_regles = new Model_DbTable_CommissionRegle;
			
			// On ajoute une règle
			$row_regle = $model_regles->createRow();
			$row_regle->ID_COMMISSION = $this->_request->id_commission;
			$row_regle->save();
		}
		
		public function deleteRegleAction() {
			
			$this->_helper->viewRenderer->setNoRender();
			
			// Les modèles
			$model_regles = new Model_DbTable_CommissionRegle;
			$model_reglesTypes = new Model_DbTable_CommissionRegleType;
			$model_reglesClasses = new Model_DbTable_CommissionRegleClasse;
			$model_reglesCategories = new Model_DbTable_CommissionRegleCategorie;
			$model_reglesEtudeVisite = new Model_DbTable_CommissionRegleEtudeVisite;
			$model_reglesLocalSommeil = new Model_DbTable_CommissionRegleLocalSommeil;
			
			// On supprime la règle
			$model_regles->delete("ID_REGLE = " .  $this->_request->id_regle);
			$model_reglesTypes->delete("ID_REGLE = " .  $this->_request->id_regle);
			$model_reglesCategories->delete("ID_REGLE = " .  $this->_request->id_regle);
			$model_reglesClasses->delete("ID_REGLE = " .  $this->_request->id_regle);
			$model_reglesLocalSommeil->delete("ID_REGLE = " .  $this->_request->id_regle);
			$model_reglesEtudeVisite->delete("ID_REGLE = " .  $this->_request->id_regle);
		}
		
		public function saveReglesAction() {
		
			$this->_helper->viewRenderer->setNoRender();
		
			// Les modèles
			$model_commission = new Model_DbTable_Commission;
			$model_regles = new Model_DbTable_CommissionRegle;
			$model_reglesTypes = new Model_DbTable_CommissionRegleType;
			$model_reglesClasses = new Model_DbTable_CommissionRegleClasse;
			$model_reglesCategories = new Model_DbTable_CommissionRegleCategorie;
			$model_reglesEtudeVisite = new Model_DbTable_CommissionRegleEtudeVisite;
			$model_reglesLocalSommeil = new Model_DbTable_CommissionRegleLocalSommeil;
			
			// On spécifi l'ID de la règle à null
			$id_regle = null;
			$rowset_regle = null;
			
			// On analyse toutes les données envoyé en POST
			foreach( $_POST["ID_REGLE"] as $id_regle) {

				// Mise à jour de la règle à sauvegarder
				// On récupère la ligne
				$rowset_regle = $model_regles->find($id_regle)->current();
				
				// On regarde dans quelle commission nous sommes
				$row_commission = $model_commission->find($rowset_regle->ID_COMMISSION)->current();
				
				// On supprime les porteuses de la règle
				$model_reglesTypes->delete("ID_REGLE = $id_regle");
				$model_reglesClasses->delete("ID_REGLE = $id_regle");
				$model_reglesCategories->delete("ID_REGLE = $id_regle");
				$model_reglesLocalSommeil->delete("ID_REGLE = $id_regle");
				$model_reglesEtudeVisite->delete("ID_REGLE = $id_regle");
				
				// On met à jour la commune et le groupement
				$rowset_regle->NUMINSEE_COMMUNE = ($row_commission->ID_COMMISSIONTYPE == 2 ) ? $_POST[$id_regle."_NUMINSEE_COMMUNE"] : null;
				$rowset_regle->ID_GROUPEMENT = ($row_commission->ID_COMMISSIONTYPE != 2 ) ? $_POST[$id_regle."_ID_GROUPEMENT"] : null;
				
				// On sauvegarde la règle
				$rowset_regle->save();

				// On sauvegarde la catégorie
				foreach($_POST[$id_regle."_ID_CATEGORIE"] as $categorie) {
					$model_reglesCategories->insert(array(
						"ID_REGLE" => $id_regle,
						"ID_CATEGORIE" => $categorie
					));
				}
				
				// On sauvegarde les types d'activités
				foreach($_POST[$id_regle."_ID_TYPE"] as $type) {
					$model_reglesTypes->insert(array(
						"ID_REGLE" => $id_regle,
						"ID_TYPE" => $type
					));
				}
				
				// On sauvegarde les classes IGH
				foreach($_POST[$id_regle."_ID_CLASSE"] as $classe) {
					$model_reglesClasses->insert(array(
						"ID_REGLE" => $id_regle,
						"ID_CLASSE" => $classe
					));
				}
				
				// Local sommeil
				foreach($_POST[$id_regle."_LOCALSOMMEIL"] as $localsommeil) {
					$model_reglesLocalSommeil->insert(array(
						"ID_REGLE" => $id_regle,
						"LOCALSOMMEIL" => $localsommeil
					));
				}
				
				// Etude visite
				foreach($_POST[$id_regle."_ETUDEVISITE"] as $etudevisite) {	
					$model_reglesEtudeVisite->insert(array(
						"ID_REGLE" => $id_regle,
						"ETUDEVISITE" => $etudevisite
					));
				}
			}
		}
		
		public function applyReglesAction() {
			
			$this->_helper->viewRenderer->setNoRender();
			
			// Modèles
			$model_etablissement = new Model_DbTable_Etablissement;
			$model_etablissementInformation = new Model_DbTable_EtablissementInformations;
			$model_etablissementAdressse = new Model_DbTable_EtablissementAdresse;
			
			// On récup tout les établissements	
			$rowset_ets = $model_etablissement->fetchAll();
			
			// Pour tout les ets, on récup leur commission par défaut
			foreach($rowset_ets as $row) {
				
				// Adresses
				$rowset_adresse = $model_etablissementAdressse->get($row["ID_ETABLISSEMENT"]);
				
				// Si il y a une adresse
				if(count($rowset_adresse) > 0) {
					
					// On récupère les infos
					$info = $model_etablissement->getInformations($row["ID_ETABLISSEMENT"])->toArray();
					
					// On merge l'adresse
					$info["NUMINSEE_COMMUNE"][0] = $rowset_adresse[0]["NUMINSEE_COMMUNE"];
					
					// On récupère la commission
					$commission = $model_etablissement->getDefaultCommission($info);
					
					// Si elle n'est pas nulle on l'applique
					if($commission != null) {
						$row_ets = $model_etablissementInformation->find($info["ID_ETABLISSEMENTINFORMATIONS"])->current();
						$row_ets->ID_COMMISSION = $commission[0]["ID_COMMISSION"];
						$row_ets->save();
					}
				}
			}
		}
		
		// Membres de la commission
		public function membresAction() {
		
			// Les modèles
			$model_membres = new Model_DbTable_CommissionMembre;

			// On récupère les règles de la commission
			$this->view->array_membres = $model_membres->get($this->_request->id_commission);
		}
		
		public function addMembreAction() {
			
			$this->_helper->viewRenderer->setNoRender();
			
			// Les modèles
			$model_membres = new Model_DbTable_CommissionMembre;
			
			// On ajoute une règle
			$row_membre = $model_membres->createRow();
			$row_membre->ID_COMMISSION = $this->_request->id_commission;
			$row_membre->save();
		}
		
		public function deleteMembreAction() {
			
			$this->_helper->viewRenderer->setNoRender();
			
			// Les modèles
			$model_membres = new Model_DbTable_CommissionMembre;
			$model_membresTypes = new Model_DbTable_CommissionMembreType;
			$model_membresClasses = new Model_DbTable_CommissionMembreClasse;
			$model_membresCategories = new Model_DbTable_CommissionMembreCategorie;
			$model_membresDossierNatures = new Model_DbTable_CommissionMembreDossierNature;
			$model_membresDossierTypes = new Model_DbTable_CommissionMembreDossierType;
			
			// On supprime les courriers
			$row_membre = $model_membres->find($this->_request->id_membre)->current();
			unlink("./data/uploads/courriers/" . $this->_request->id_membre . "BE_" . $row_membre->COURRIER_BE);
			unlink("./data/uploads/courriers/" . $this->_request->id_membre . "CONVOCATION_" . $row_membre->COURRIER_CONVOCATION);

			// On supprime la règle
			$row_membre->delete();
			$model_membresTypes->delete("ID_COMMISSIONMEMBRE = " .  $this->_request->id_membre);
			$model_membresCategories->delete("ID_COMMISSIONMEMBRE = " .  $this->_request->id_membre);
			$model_membresClasses->delete("ID_COMMISSIONMEMBRE = " .  $this->_request->id_membre);
			$model_membresDossierNatures->delete("ID_COMMISSIONMEMBRE = " .  $this->_request->id_membre);
			$model_membresDossierTypes->delete("ID_COMMISSIONMEMBRE = " .  $this->_request->id_membre);
		}
		
		public function saveMembresAction() {
		
			$this->_helper->viewRenderer->setNoRender();
		
			// Les modèles
			$model_membres = new Model_DbTable_CommissionMembre;
			$model_membresTypes = new Model_DbTable_CommissionMembreType;
			$model_membresClasses = new Model_DbTable_CommissionMembreClasse;
			$model_membresCategories = new Model_DbTable_CommissionMembreCategorie;
			$model_membresDossierNatures = new Model_DbTable_CommissionMembreDossierNature;
			$model_membresDossierTypes = new Model_DbTable_CommissionMembreDossierType;
			
			// On spécifi l'ID de la règle à null
			$id_membre = null;
			$rowset_membre = null;
			
			// On analyse toutes les données envoyé en POST
			foreach( $_POST["ID_COMMISSIONMEMBRE"] as $id_membre) {

				// Mise à jour de la règle à sauvegarder
				// On récupère la ligne
				$rowset_membre = $model_membres->find($id_membre)->current();
				
				// On supprime les porteuses de la règle
				$model_membresTypes->delete("ID_COMMISSIONMEMBRE = $id_membre");
				$model_membresClasses->delete("ID_COMMISSIONMEMBRE = $id_membre");
				$model_membresCategories->delete("ID_COMMISSIONMEMBRE = $id_membre");
				$model_membresDossierNatures->delete("ID_COMMISSIONMEMBRE = $id_membre");
				$model_membresDossierTypes->delete("ID_COMMISSIONMEMBRE = $id_membre");
				
				// On met à jour la commune et le groupement
				$rowset_membre->LIBELLE_COMMISSIONMEMBRE = $_POST[$id_membre."_LIBELLE_COMMISSIONMEMBRE"];
				$rowset_membre->PRESENCE_COMMISSIONMEMBRE = $_POST[$id_membre."_PRESENCE_COMMISSIONMEMBRE"];
				$rowset_membre->ID_UTILISATEURINFORMATIONS = null;
				$rowset_membre->ID_GROUPEMENT = null;
				
				switch( $_POST[$id_membre."_typemembre"] ) {
				
					case 1:
						$rowset_membre->ID_GROUPEMENT = $_POST[$id_membre."_ID_GROUPEMENT"];
						break;
						
					case 2:
						$rowset_membre->ID_UTILISATEURINFORMATIONS = $_POST[$id_membre."_ID_UTILISATEURINFORMATIONS"];
						break;
				}

				// On sauvegarde la règle
				$rowset_membre->save();

				// On sauvegarde la catégorie
				foreach($_POST[$id_membre."_ID_CATEGORIE"] as $categorie) {
					$model_membresCategories->insert(array(
						"ID_COMMISSIONMEMBRE" => $id_membre,
						"ID_CATEGORIE" => $categorie
					));
				}
				
				// On sauvegarde les types d'activités
				foreach($_POST[$id_membre."_ID_TYPE"] as $type) {
					$model_membresTypes->insert(array(
						"ID_COMMISSIONMEMBRE" => $id_membre,
						"ID_TYPE" => $type
					));
				}
				
				// On sauvegarde les classes IGH
				foreach($_POST[$id_membre."_ID_CLASSE"] as $classe) {
					$model_membresClasses->insert(array(
						"ID_COMMISSIONMEMBRE" => $id_membre,
						"ID_CLASSE" => $classe
					));
				}
				
				// On sauvegarde les types de dossier
				foreach($_POST[$id_membre."_ID_DOSSIERTYPE"] as $type) {
					$model_membresDossierTypes->insert(array(
						"ID_COMMISSIONMEMBRE" => $id_membre,
						"ID_DOSSIERTYPE" => $type
					));
				}
				
				// On sauvegarde les natures du dossier
				if(count($_POST[$id_membre."_ID_DOSSIERNATURE"]) > 0) {
				
					foreach($_POST[$id_membre."_ID_DOSSIERNATURE"] as $type) {
						$model_membresDossierNatures->insert(array(
							"ID_COMMISSIONMEMBRE" => $id_membre,
							"ID_DOSSIERNATURE" => $type
						));
					}
				}
			}
		}
	
		// Contacts de la commission
		public function contactsAction() {}
		
		// Courriers types des membres de la commission
		public function courriersAction() {
		
			// Les modèles
			$model_membres = new Model_DbTable_CommissionMembre;
			
			// On récupère la liste des membres de la commission
			$this->view->rowset_membres = $model_membres->fetchAll("ID_COMMISSION = " . $this->_request->id_commission);
		}
		
		// Courriers types des membres de la commission
		public function addCourrierAction() {
		
			$this->_helper->viewRenderer->setNoRender();
			
			$error = "null";
			
			// Extension du fichier uploadé
			$string_extension = strrchr($_FILES['COURRIER']['name'], ".");
			
			// On check si on veut uploader un document odt
			if( $string_extension == ".odt" ) {
			
				if(move_uploaded_file($_FILES['COURRIER']['tmp_name'], "./data/uploads/courriers/" . $this->_request->id_membre . $this->_request->type . "_" . $_FILES['COURRIER']['name']) ) {
			
					// Les modèles
					$model_membres = new Model_DbTable_CommissionMembre;
					
					// On récupère l'instance du membres
					$row_membre = $model_membres->find($this->_request->id_membre)->current();
					$row = "COURRIER_" . $this->_request->type;
					
					// Si il y a déjà un courrier, on le supprime
					if($row_membre->$row != null) {
					
						unlink("./data/uploads/courriers/" . $this->_request->id_membre . $this->_request->type . "_" . $row_membre->$row);
					}
					
					// On met à jour le libellé du courrier modifié
					$row_membre->$row = $_FILES['COURRIER']['name'];
					
					// et on sauvegarde
					$row_membre->save();
				}
				else {
				
					$error = "Le téléchargement a échoué.";
				}
			}
			else {
			
				$error = "Extension non supportée.";
			}
			
			// CALLBACK
			echo "<script type='text/javascript'>window.top.window.callback('$error');</script>";
		}
		
		public function deleteCourrierAction() {
		
			$this->_helper->viewRenderer->setNoRender();
			
			// Les modèles
			$model_membres = new Model_DbTable_CommissionMembre;
			
			// On récupère l'instance du membres
			$row_membre = $model_membres->find($this->_request->id_membre)->current();
			$row = "COURRIER_" . $this->_request->type;	
			
			// On supprime le fichier
			unlink("./data/uploads/courriers/" . $this->_request->id_membre . $this->_request->type . "_" . $row_membre->$row);
			
			// On met à null dans la DB
			$row_membre->$row = null;
			
			$row_membre->save();
		}
	}