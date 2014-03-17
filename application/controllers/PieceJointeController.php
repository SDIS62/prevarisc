<?php

class PieceJointeController extends Zend_Controller_Action
{
    public $path;

    public function init()
    {
        $this->path = REAL_DATA_PATH . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "pieces-jointes" . DIRECTORY_SEPARATOR;

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

        $this->view->path = DATA_PATH . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "pieces-jointes" . DIRECTORY_SEPARATOR;;

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
            if (!move_uploaded_file($_FILES['fichier']['tmp_name'], $this->path . $nouvellePJ->ID_PIECEJOINTE . $extension) ) {
                $nouvellePJ->delete();
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Impossible de charger la pièce jointe',
                    'message' => ''
                ));
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

                        // On resize l'image
                        GD_Resize::run($this->path . $nouvellePJ->ID_PIECEJOINTE . $extension, $this->path . "miniatures" . DIRECTORY_SEPARATOR . $nouvellePJ->ID_PIECEJOINTE . ".jpg", 450);

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

                if( file_exists($this->path . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE) )					unlink($this->path . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE);
                if( file_exists($this->path . "miniatures/" . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE) )	unlink($this->path . "miniatures/" . $pj->ID_PIECEJOINTE . $pj->EXTENSION_PIECEJOINTE);
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
            // Si elle existe
         $this->view->exists = file_exists($this->path . $this->_request->idpj . $this->_request->ext);

         if ($this->view->exists) {

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

             // Données de la pj
             $this->view->html = $this->view->partial("piece-jointe/display.phtml", array (
                 "path" => DATA_PATH . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "pieces-jointes" . DIRECTORY_SEPARATOR,
                 "listePj" => $listePj,
                 "droit_ecriture" => true,
                 "type" => $this->_request->type,
                 "id" => $this->_request->id,
             ));
         }

    }
}
