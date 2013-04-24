<?php

    class PieceJointeController extends Zend_Controller_Action
    {
        public $path = "/data/uploads/pieces-jointes/";

        public function init()
        {
            // Actions à effectuées en AJAX
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
            $ajaxContext->addActionContext('check', 'json')
                ->initContext();

            // Droits
            $droits = $this->_helper->Droits()->get();
            $this->view->droit_ecriture = false;

            // Droits sur la page
            switch ($this->_request->type) {

                case "etablissement" :
                    if($this->_helper->Droits()->checkEtablissement($this->_request->id))
                        $this->_helper->Droits()->redirect();
                    else {

                        $model_etablissement = new Model_DbTable_Etablissement;
                        $informations = $model_etablissement->getInformations( $this->_request->id );

                        $droit_ecriture = $droits->ID_GENRE[$informations["ID_GENRE"]]["DROITECRITURE_GROUPEGENRE"];
                        $this->view->droit_ecriture = $droit_ecriture;

                        if($droit_ecriture == 0 && !in_array($this->getRequest()->getActionName(), array("index", "check")))
                            $this->_helper->Droits()->redirect();
                    }
                    break;

                case "dossier":
                    if($this->_helper->Droits()->checkDossier($this->_request->id))
                        $this->_helper->Droits()->redirect();
                    else
                        $this->view->droit_ecriture = true;
                    break;

                case "dateCommission":
                    if($droits->DROITADMINPREV_GROUPE == 0)
                        $this->_helper->Droits()->redirect();
                    else
                        $this->view->droit_ecriture = true;
                    break;

                default:
                    $this->_helper->Droits()->redirect();
                    break;
            }

        }

        public function indexAction()
        {
            // Modèles
            $DBused = new Model_DbTable_PieceJointe;
			
			
			
            // Cas dossier
            if ($this->_request->type == "dossier") {				
                $this->view->type = "dossier";
                $this->view->identifiant = $this->_request->id;
				$this->view->pjcomm = $this->_request->pjcomm;
                $listePj = $DBused->affichagePieceJointe("dossierpj", "dossierpj.ID_DOSSIER", $this->_request->id);
            }

            // Cas établissement
            else if ($this->_request->type == "etablissement") {
                $this->view->type = "etablissement";
                $this->view->identifiant = $this->_request->id;
                $listePj = $DBused->affichagePieceJointe("etablissementpj", "etablissementpj.ID_ETABLISSEMENT", $this->_request->id);
            }

            // Cas d'une date de commission
            else if ($this->_request->type == "dateCommission") {
                $this->view->type = "dateCommission";
                $this->view->identifiant = $this->_request->id;
                $listePj = $DBused->affichagePieceJointe("datecommissionpj", "datecommissionpj.ID_DATECOMMISSION", $this->_request->id);
            }

            // On envoi la liste des PJ dans la vue
            $this->view->listePj = $listePj;
			//Zend_Debug::dump($this->view->listePj);
            $this->view->path = $this->path;
        }

        public function formAction()
        {
            // Placement
            $this->view->type = $this->_getParam('type');
            $this->view->identifiant = $this->_getParam('id');

            // Ici suivant le type on chage toutes les infos nécessaire pour lier aux différents établissements, dossiers
            if ($this->view->type == 'dossier') {

                $DBdossier = new Model_DbTable_Dossier;
                $this->view->listeEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("id"));
            }
        }

        public function addAction()
        {
            $this->_helper->viewRenderer->setNoRender(true);

            // Modèles
            $DBpieceJointe = new Model_DbTable_PieceJointe;

            // Extension du fichier
            $extension = strrchr($_FILES['fichier']['name'], ".");

            // Date d'aujourd'hui
            $dateNow = new Zend_Date();

            // Création d'une nouvelle ligne dans la base de données
            $nouvellePJ = $DBpieceJointe->createRow();

            // Données de la pièce jointe
            $nouvellePJ->EXTENSION_PIECEJOINTE = $extension;
            $nouvellePJ->NOM_PIECEJOINTE = $this->_getParam('nomFichier') == '' ? $_FILES['fichier']['name'] : $this->_getParam('nomFichier');
            $nouvellePJ->DESCRIPTION_PIECEJOINTE = $this->_getParam('descriptionFichier');
            $nouvellePJ->DATE_PIECEJOINTE = $dateNow->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY." ".Zend_Date::HOUR.":".Zend_Date::MINUTE.":".Zend_Date::SECOND);

            // Sauvegarde de la BDD
            $nouvellePJ->save();

            // On check si l'upload est okay
            if (!move_uploaded_file($_FILES['fichier']['tmp_name'], "." . $this->path . $nouvellePJ->ID_PIECEJOINTE . $extension) ) {

                $nouvellePJ->delete();
            } else {

                // Dans le cas d'un dossier
                if ($this->_getParam('type') == 'dossier') {

                    // Modèles
                    $DBetab = new Model_DbTable_EtablissementPj;
                    $DBsave = new Model_DbTable_DossierPj;

                    // On créé une nouvelle ligne, et on y met une bonne clé étrangère en fonction du type
                    $linkPj = $DBsave->createRow();
                    $linkPj->ID_DOSSIER = $this->_getParam('id');

                    // On fait les liens avec les différents établissements séléctionnés
                    if ($this->_getParam('etab')) {

                        foreach ($this->_getParam('etab') as $etabLink ) {

                            $linkEtab = $DBetab->createRow();
                            $linkEtab->ID_ETABLISSEMENT = $etabLink;
                            $linkEtab->ID_PIECEJOINTE = $nouvellePJ->ID_PIECEJOINTE;
                            $linkEtab->save();
                        }
                    }
                }
                // Dans le cas d'un établissement
                else if ($this->_getParam('type') == 'etablissement') {

                    // Modèles
                    $DBsave = new Model_DbTable_EtablissementPj;

                    // On créé une nouvelle ligne, et on y met une bonne clé étrangère en fonction du type
                    $linkPj = $DBsave->createRow();
                    $linkPj->ID_ETABLISSEMENT = $this->_getParam('id');

                    // Mise en avant d'une pièce jointe (null = nul part, 0 = plan, 1 = diapo)
                    if ( $this->_request->PLACEMENT_ETABLISSEMENTPJ != "null" && in_array($extension, array(".jpg", ".jpeg", ".png", ".gif")) ) {

                        // Lib pour resize l'image
                        require_once 'GD/GD_resize.php';

                        // On resize l'image
                        GD_resize("." . $this->path . $nouvellePJ->ID_PIECEJOINTE . $extension, "." . $this->path . "miniatures/" . $nouvellePJ->ID_PIECEJOINTE . ".jpg", 450);

                        $linkPj->PLACEMENT_ETABLISSEMENTPJ = $this->_request->PLACEMENT_ETABLISSEMENTPJ;
                    }
                } elseif ($this->_getParam('type') == 'dateCommission') {

                    // Modèles
                    $DBsave = new Model_DbTable_DateCommissionPj;

                    // On créé une nouvelle ligne, et on y met une bonne clé étrangère en fonction du type
                    $linkPj = $DBsave->createRow();
                    $linkPj->ID_DATECOMMISSION = $this->_getParam('id');

                }

                // On met l'id de la pièce jointe créée
                $linkPj->ID_PIECEJOINTE = $nouvellePJ->ID_PIECEJOINTE;

                // On sauvegarde le tout
                $linkPj->save();

                // CALLBACK
                echo "<script type='text/javascript'>window.top.window.callback('".$nouvellePJ->ID_PIECEJOINTE."', '".$extension."');</script>";
            }
        }

        public function deleteAction()
        {
            $this->_helper->viewRenderer->setNoRender(true);

            // Modèle
            $DBpieceJointe = new Model_DbTable_PieceJointe;
            $DBitem = null;

            // On récupère la pièce jointe
            $pj = $DBpieceJointe->find($this->_request->id_pj)->current();

            // Selon le type, on fixe le modèle à utiliser
            switch ($this->_request->type) {

                case "dossier":
                    $DBitem = new Model_DbTable_DossierPj;
                    break;

                case "etablissement":
                    $DBitem = new Model_DbTable_EtablissementPj;
                    break;

                case "dateCommission":
                    $DBitem = new Model_DbTable_DateCommissionPj;
                    break;
            }

            // On supprime dans la BDD et physiquement
            if ($DBitem != null) {

                if( file_exists("." . $this->path . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE) )					unlink("." . $this->path . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE);
                if( file_exists("." . $this->path . "miniatures/" . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE) )	unlink("." . $this->path . "miniatures/" . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE);
                $pj->delete();
                $DBitem->delete("ID_PIECEJOINTE = " . (int) $this->_request->id_pj);
            }
        }

        public function checkAction()
        {
			
            // Si elle existe
            $this->view->exists = file_exists("." . $this->path . $this->_request->idpj . $this->_request->ext);

            if ($this->view->exists) {
				
                // Modèle
                $DBused = new Model_DbTable_PieceJointe;

                // Données de la pj
                $this->view->html = $this->view->partial("piece-jointe/display.phtml", array (
                    "path" => $this->path,
                    "listePj" => $DBused->fetchAll("ID_PIECEJOINTE = " . $this->_request->idpj)->toArray(),
                    "droit_ecriture" => true,
                    "type" => $this->_request->type,
                    "id" => $this->_request->id,
                ));
            }
        }
    }
