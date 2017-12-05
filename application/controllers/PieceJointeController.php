<?php

class PieceJointeController extends Zend_Controller_Action
{
    public $store;

    public function init()
    {
        $this->store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');

        // Actions à effectuées en AJAX
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('check', 'json')
            ->initContext();
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
            $this->view->verrou = $this->_request->verrou;
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
        
        // Cas par défaut
        else {
            $listePj = array();
        }

        // On envoi la liste des PJ dans la vue
        $this->view->listePj = $listePj;

    }
    
    public function getAction()
    {

        // Modèles
        $DBused = new Model_DbTable_PieceJointe;

        // Cas dossier
        if ($this->_request->type == "dossier") {
            $type = "dossier";
            $identifiant = $this->_request->id;
            $piece_jointe = $DBused->affichagePieceJointe("dossierpj", "piecejointe.ID_PIECEJOINTE", $this->_request->idpj);
        }

        // Cas établissement
        else if ($this->_request->type == "etablissement") {
            $type = "etablissement";
            $identifiant = $this->_request->id;
            $piece_jointe = $DBused->affichagePieceJointe("etablissementpj", "piecejointe.ID_PIECEJOINTE", $this->_request->idpj);
        }

        // Cas d'une date de commission
        else if ($this->_request->type == "dateCommission") {
            $type = "dateCommission";
            $identifiant = $this->_request->id;
            $piece_jointe = $DBused->affichagePieceJointe("datecommissionpj", "piecejointe.ID_PIECEJOINTE", $this->_request->idpj);
        }
        
        if (!$piece_jointe || count($piece_jointe) == 0) {
            throw new Zend_Controller_Action_Exception('Cannot find piece jointe for id '.$this->_request->idpj, 404);
        }
        
        $piece_jointe = $piece_jointe[0];
        
        $filepath = $this->store->getFilePath($piece_jointe, $type, $identifiant);
        $filename = $this->store->getFormattedFilename($piece_jointe, $type, $identifiant);
        
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        
	if (!is_readable($filepath)) {
            throw new Zend_Controller_Action_Exception('Cannot read file '.$filepath, 404);
        }
	
        ob_get_clean();
	
        header("Pragma: public");
        header("Expires: -1");
        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header("Content-Type: application/octet-stream");
        
	readfile($filepath);
	exit();
    }


    public function formAction()
    {
        // Placement
        $this->view->type = $this->_getParam('type');
        $this->view->identifiant = $this->_getParam('id');

        // Ici suivant le type on change toutes les infos nécessaires pour lier aux différents établissements, dossiers
        if ($this->view->type == 'dossier') {

            $DBdossier = new Model_DbTable_Dossier;
            $this->view->listeEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("id"));
        }

    }

    public function addAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();

            // Modèles
            $DBpieceJointe = new Model_DbTable_PieceJointe;

            // Un fichier est-il envoyé ?
            if (!isset($_FILES['fichier'])) {
                throw new Exception('Aucun fichier reçu');
            }
            
            // Extension du fichier
            $extension = strtolower(strrchr($_FILES['fichier']['name'], "."));
            if (in_array($extension, array('.php', '.php4', '.php5', '.sh', '.ksh', '.csh'))) {
                throw new Exception("Ce type de fichier n'est pas autorisé en upload");
            }
            
            // Date d'aujourd'hui
            $dateNow = new Zend_Date();

            // Création d'une nouvelle ligne dans la base de données
            $nouvellePJ = $DBpieceJointe->createRow();

            // Données de la pièce jointe
            $nouvellePJ->EXTENSION_PIECEJOINTE = $extension;
            $nouvellePJ->NOM_PIECEJOINTE = $this->_getParam('nomFichier') == '' ? substr($_FILES['fichier']['name'], 0, -4) : $this->_getParam('nomFichier');
            $nouvellePJ->DESCRIPTION_PIECEJOINTE = $this->_getParam('descriptionFichier');
            $nouvellePJ->DATE_PIECEJOINTE = $dateNow->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY." ".Zend_Date::HOUR.":".Zend_Date::MINUTE.":".Zend_Date::SECOND);

            // Sauvegarde de la BDD
            $nouvellePJ->save();
            
            $file_path = $this->store->getFilePath($nouvellePJ, $this->_getParam('type'), $this->_getParam('id'), true);

            // On check si l'upload est okay
            if (!move_uploaded_file($_FILES['fichier']['tmp_name'], $file_path) ) {
                $nouvellePJ->delete();
                throw new Exception('Impossible de charger la pièce jointe');
                
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
                        
                        $miniature = $nouvellePJ;
                        $miniature['EXTENSION_PIECEJOINTE'] = '.jpg';
                        $miniature_path = $this->store->getFilePath($miniature, 'etablissement_miniature', $this->_getParam('id'), true);

                        
                        // On resize l'image
                        GD_Resize::run($file_path, $miniature_path, 450);

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

                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'La pièce jointe '.$nouvellePJ->NOM_PIECEJOINTE.' a bien été ajoutée',
                    'message' => ''
                ));

                // CALLBACK
                echo "<script type='text/javascript'>window.top.window.callback('".$nouvellePJ->ID_PIECEJOINTE."', '".$extension."');</script>";
            }
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de l\'ajout de la pièce jointe',
                'message' => $e->getMessage()
            ));
            
            // CALLBACK
            echo "<script type='text/javascript'>window.top.window.location.reload();</script>";
        }
    }

    public function deleteAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender(true);

            // Modèle
            $DBpieceJointe = new Model_DbTable_PieceJointe;
            $DBitem = null;

            // On récupère la pièce jointe
            $pj = $DBpieceJointe->find($this->_request->id_pj)->current();

//            var_dump($pj);exit();
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
            if ($pj != null && $DBitem != null) {
                
                $file_path = $this->store->getFilePath($pj, $this->_request->type, $this->_request->id);
                $miniature_pj = $pj;
                $miniature_pj['EXTENSION_PIECEJOINTE'] = '.jpg';
                $miniature_path = $this->store->getFilePath($miniature_pj, 'etablissement_miniature', $this->_request->id);
                
                
                if( file_exists($file_path) )           unlink($file_path);
                if( file_exists($miniature_path) )	unlink($miniature_path);
                $DBitem->delete("ID_PIECEJOINTE = " . (int) $this->_request->id_pj);
                $pj->delete();
            }

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'La pièce jointe a été supprimée',
                'message' => ''
            ));

        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la suppression de la pièce jointe',
                'message' => $e->getMessage()
            ));
        }

        //redirection
        $this->_helper->redirector('index');
    }

    public function checkAction()
    {
        // Modèle
        $DBused = new Model_DbTable_PieceJointe;

        // Cas dossier
       if ($this->_request->type == "dossier") {
           $listePj = $DBused->affichagePieceJointe("dossierpj", "dossierpj.ID_PIECEJOINTE", $this->_request->idpj);
       }

       // Cas établissement
       else if ($this->_request->type == "etablissement") {
           $listePj = $DBused->affichagePieceJointe("etablissementpj", "etablissementpj.ID_PIECEJOINTE", $this->_request->idpj);
       }

       // Cas d'une date de commission
       else if ($this->_request->type == "dateCommission") {
           $listePj = $DBused->affichagePieceJointe("datecommissionpj", "datecommissionpj.ID_PIECEJOINTE", $this->_request->idpj);
       }

       // Cas par défaut
       else {
           $listePj = array();
       }
       
       $pj = count($listePj) > 0 ? $listePj[0] : null;
       
       if (!$pj) {
           return ;
       }
       
       $file_path = $this->store->getFilePath($pj, $this->_request->type, $this->_request->id);
       $this->view->exists = file_exists($file_path);
       
       if ($this->view->exists) {
            // Données de la pj
            $this->view->html = $this->view->partial("piece-jointe/display.phtml", array (
                "path" => $this->getHelper('url')->url(array('controller' => 'piece-jointe', 'id' => $this->_request->id, 'action' => 'get', 'idpj' => $this->_request->idpj, 'type' => $this->_request->type)),
                "listePj" => $listePj,
                "droit_ecriture" => true,
                "type" => $this->_request->type,
                "id" => $this->_request->id,
            ));
       }
    }
    
    
}
