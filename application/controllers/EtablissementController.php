<?php

class EtablissementController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;
        $service_groupement_communes = new Service_GroupementCommunes;

        $etablissement = $service_etablissement->get($this->_request->id);

        $this->view->etablissement = $etablissement;
        $this->view->groupements_de_communes = count($etablissement['adresses']) == 0 ? array() : $service_groupement_communes->findAll($etablissement['adresses'][0]["NUMINSEE_COMMUNE"]);
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;

        $etablissement = $service_etablissement->get($this->_request->id);

        $this->view->etablissement = $etablissement;

        $service_genre = new Service_Genre;
        $service_statut = new Service_Statut;
        $service_avis = new Service_Avis;
        $service_categorie = new Service_Categorie;                    
        $service_type = new Service_Type;                              
        $service_typeactivite = new Service_TypeActivite;                  
        $service_commission = new Service_Commission;                       
        $service_typesplan = new Service_TypePlan;                     
        $service_famille = new Service_Famille;
        $service_classe = new Service_Classe;                          

        $this->view->DB_genre = $service_genre->getAll();
        $this->view->DB_statut = $service_statut->getAll();
        $this->view->DB_avis = $service_avis->getAll();
        $this->view->DB_categorie = $service_categorie->getAll();
        $this->view->DB_type = $service_type->getAll();
        $this->view->DB_activite = $service_typeactivite->getAll();
        $this->view->DB_commission = $service_commission->getAll();
        $this->view->DB_typesplan = $service_typesplan->getAll();
        $this->view->DB_famille = $service_famille->getAll();
        $this->view->DB_classe = $service_classe->getAll();

        $this->view->add = false;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->save($post['ID_GENRE'], $post, $this->_request->id, $post['date']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'L\'établissement a bien été mis à jour.'));
                $this->_helper->redirector('index', null, null, array('id' => $this->_request->id));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'L\'établissement n\'a pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function addAction()
    {
        $service_etablissement = new Service_Etablissement;
        $service_genre = new Service_Genre;
        $service_statut = new Service_Statut;
        $service_avis = new Service_Avis;
        $service_categorie = new Service_Categorie;                    
        $service_type = new Service_Type;                              
        $service_typeactivite = new Service_TypeActivite;                  
        $service_commission = new Service_Commission;                       
        $service_typesplan = new Service_TypePlan;                     
        $service_famille = new Service_Famille;
        $service_classe = new Service_Classe;                          

        $this->view->DB_genre = $service_genre->getAll();
        $this->view->DB_statut = $service_statut->getAll();
        $this->view->DB_avis = $service_avis->getAll();
        $this->view->DB_categorie = $service_categorie->getAll();
        $this->view->DB_type = $service_type->getAll();
        $this->view->DB_activite = $service_typeactivite->getAll();
        $this->view->DB_commission = $service_commission->getAll();
        $this->view->DB_typesplan = $service_typesplan->getAll();
        $this->view->DB_famille = $service_famille->getAll();
        $this->view->DB_classe = $service_classe->getAll();
        
        $this->view->add = true;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $id_etablissement = $service_etablissement->save($post['ID_GENRE'], $post);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Ajout réussi !', 'message' => 'L\'établissement a bien été ajouté.'));
                $this->_helper->redirector('index', null, null, array('id' => $id_etablissement));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'L\'établissement n\'a pas été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        $this->render('edit');
    }

    public function descriptifAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);

        $descriptifs = $service_etablissement->getDescriptifs($this->_request->id);

        $this->view->descriptif = $descriptifs['descriptif'];
        $this->view->historique = $descriptifs['historique'];
        $this->view->derogations = $descriptifs['derogations'];
        $this->view->champs_descriptif_technique = $descriptifs['descriptifs_techniques'];
    }

    public function editDescriptifAction()
    {
        $service_etablissement = new Service_Etablissement;

        $this->descriptifAction();

        if($this->_request->isPost())
        {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->saveDescriptifs($this->_request->id, $post['historique'], $post['descriptif'], $post['derogations'], $post['descriptifs_techniques']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Les descriptifs ont bien été mis à jour.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Mise à jour annulée', 'message' => 'Les descriptifs n\'ont pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('descriptif', null, null, array('id' => $this->_request->id));
        }
    }

    public function textesApplicablesAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;
        $service_textesapplicables = new Service_TextesApplicables;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);

        $this->view->textes_applicables = $service_textesapplicables->getAll();
        $this->view->textes_applicables_de_etablissement = $service_etablissement->getAllTextesApplicables($this->_request->id);
    }

    public function editTextesApplicablesAction()
    {
        $service_etablissement = new Service_Etablissement;
        
        $this->textesApplicablesAction();

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->saveTextesApplicables($this->_request->id, $post['textes_applicables']);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les textes applicables ont bien été mis à jour.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Mise à jour annulée','message' => 'Les textes applicables n\'ont pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('textes-applicables', null, null, array('id' => $this->_request->id));
        }
    }

    public function piecesJointesAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);
        $this->view->pieces_jointes = $service_etablissement->getAllPJ($this->_request->id);
    }

    public function editPiecesJointesAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);
        $this->view->pieces_jointes = $service_etablissement->getAllPJ($this->_request->id);
    }

    public function addPieceJointeAction()
    {
        $this->_helper->layout->disableLayout();

        $service_etablissement = new Service_Etablissement;

        if($this->_request->isPost())
        {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->addPJ($this->_request->id, $_FILES['file'], $post['name'], $post['description'], $post['mise_en_avant']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'La pièce jointe a bien été ajoutée.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Mise à jour annulée', 'message' => 'La pièce jointe n\'a été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('edit-pieces-jointes', null, null, array('id' => $this->_request->id));
        }
    }

    public function deletePieceJointeAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $service_etablissement = new Service_Etablissement;

        if($this->_request->isGet())
        {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->deletePJ($this->_request->id, $this->_request->id_pj);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'La pièce jointe a bien été supprimée.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Suppression annulée', 'message' => 'La pièce jointe n\'a été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('edit-pieces-jointes', null, null, array('id' => $this->_request->id));
        }
    }

    public function contactsAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);
    }

    public function dossiersAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);

        $dossiers = $service_etablissement->getDossiers($this->_request->id);

        $this->view->etudes = $dossiers['etudes'];
        $this->view->visites = $dossiers['visites'];
        $this->view->autres = $dossiers['autres'];
    }

    public function historiqueAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);

        $this->view->historique = $service_etablissement->getHistorique($this->_request->id);
    }

    /* API */

    public function getAction()
    {
        header('Content-type: application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // CrÃ©ation de l'objet recherche
        $search = new Model_DbTable_Search;

        // On set le type de recherche
        $search->setItem("etablissement");
        $search->limit(5);

        // On recherche avec le libellé
        $search->setCriteria("LIBELLE_ETABLISSEMENTINFORMATIONS", $this->_request->q, false);

        // On filtre par le genre
        if ($this->_request->g) {
            if ($this->_request->genre_pere == 1) {
                if($this->_request->g == 2)
                    $search->setCriteria("etablissementinformations.ID_GENRE", 1);
                elseif($this->_request->g == 3)
                    $search->setCriteria("etablissementinformations.ID_GENRE", 2);
            }

            if ($this->_request->genre_enfant == 1) {
                if($this->_request->g == 1)
                    $search->setCriteria("etablissementinformations.ID_GENRE", array(2,4,5,6));
                elseif($this->_request->g == 2)
                    $search->setCriteria("etablissementinformations.ID_GENRE", 3);
            }
        }

        // On balance le rÃ©sultat sur la vue
        echo Zend_Json::Encode(array('resultats' => $search->run()->getAdapter()->getItems(0, 99999999999)->toArray()));
    }

    public function ficheExisteAction()
    {
        header('Content-type: application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $DB_information = new Model_DbTable_EtablissementInformations;

        if ($this->_request->date != "undefined") {

            $array_date = $this->getDate($this->_request->date);
            $this->view->bool_fiche = (null != ($row = $DB_information->fetchRow("ID_ETABLISSEMENT = '" .  $this->_request->id . "' AND DATE_ETABLISSEMENTINFORMATIONS = '" . $array_date . "'"))) ? true : false;
        } else {

            $this->view->bool_fiche = false;
        }
    }

    public function getDefaultValuesAction()
    {
        header('Content-type: application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $service_etablissement = new Service_Etablissement;

        $service_etablissement->getDefaultValues($genre, $data);
    }
}
