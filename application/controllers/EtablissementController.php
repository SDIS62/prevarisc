<?php

class EtablissementController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;
        $service_groupement_communes = new Service_GroupementCommunes;
        $service_carto = new Service_Carto;

        $etablissement = $service_etablissement->get($this->_request->id);

        $this->view->couches_cartographiques = $service_carto->getAll();
        $this->view->key_ign = getenv('PREVARISC_PLUGIN_IGNKEY');
        $this->view->key_googlemap = getenv('PREVARISC_PLUGIN_GOOGLEMAPKEY');

        $this->view->etablissement = $etablissement;
        $this->view->groupements_de_communes = count($etablissement['adresses']) == 0 ? array() : $service_groupement_communes->findAll($etablissement['adresses'][0]["NUMINSEE_COMMUNE"]);
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;
        $service_carto = new Service_Carto;

        $etablissement = $service_etablissement->get($this->_request->id);

        $this->view->etablissement = $etablissement;

        $this->view->key_ign = getenv('PREVARISC_PLUGIN_IGNKEY');

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

        $this->view->key_ign = getenv('PREVARISC_PLUGIN_IGNKEY');

        $this->view->add = false;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $date = date("Y-m-d");
                $service_etablissement->save($post['ID_GENRE'], $post, $this->_request->id, $date);
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

                if($post['ID_GENRE'] == 1 && count($post['ID_FILS_ETABLISSEMENT']) == 1) {
                  $this->_helper->flashMessenger(array('context' => 'warning', 'title' => 'Ajout des établissements enfants', 'message' => "Les droits d'accès au site sont déterminés par les droits d'accès aux établissements qui le compose. Veillez à ajouter des établissements afin de garantir l'accès au site dans Prevarisc."));
                  $this->_helper->redirector('edit', null, null, array('id' => $id_etablissement));
                }
                else {
                  $this->_helper->redirector('index', null, null, array('id' => $id_etablissement));
                }
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

        $this->view->etablissement = $service_etablissement->get($this->_request->id);
        $this->view->textes_applicables_de_etablissement = $service_etablissement->getAllTextesApplicables($this->_request->id);
    }

    public function editTextesApplicablesAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;
        $service_textes_applicables = new Service_TextesApplicables;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);
        $this->view->textes_applicables_de_etablissement = $service_etablissement->getAllTextesApplicables($this->_request->id);
        $this->view->textes_applicables = $service_textes_applicables->getAll();

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

        $etablissement = $service_etablissement->get($this->_request->id);
        $contacts_etablissements_parents = array();

        // Récupération des contacts des établissements parents
        foreach($etablissement['parents'] as $etablissement_parent) {
            $contacts_etablissements_parents = array_merge($contacts_etablissements_parents, $service_etablissement->getAllContacts($etablissement_parent['ID_ETABLISSEMENT']));
        }

        $this->view->etablissement = $etablissement;
        $this->view->contacts = $service_etablissement->getAllContacts($this->_request->id);
        $this->view->contacts_etablissements_parents = $contacts_etablissements_parents;
    }

    public function editContactsAction()
    {
        $this->_helper->layout->setLayout('etablissement');

        $service_etablissement = new Service_Etablissement;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);
        $this->view->contacts = $service_etablissement->getAllContacts($this->_request->id);
    }

    public function addContactAction()
    {
        $this->_helper->layout->disableLayout();

        $service_etablissement = new Service_Etablissement;
        $service_contact = new Service_Contact;

        $this->view->fonctions = $service_contact->getFonctions();

        if($this->_request->isPost())
        {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->addContact($this->_request->id, $post['firstname'], $post['lastname'], $post['id_fonction'], $post['societe'], $post['fixe'], $post['mobile'], $post['fax'], $post['mail'], $post['adresse'], $post['web']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le contact a bien été ajouté.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Mise à jour annulée', 'message' => 'Le contact n\'a été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('edit-contacts', null, null, array('id' => $this->_request->id));
        }
    }

    public function addContactExistantAction()
    {
        $this->_helper->layout->disableLayout();

        $service_etablissement = new Service_Etablissement;
        $service_contact = new Service_Contact;

        if($this->_request->isPost())
        {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->addContactExistant($this->_request->id, $this->_request->id_contact);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le contact a bien été ajouté.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Mise à jour annulée', 'message' => 'Le contact n\'a été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('edit-contacts', null, null, array('id' => $this->_request->id));
        }
    }

    public function deleteContactAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $service_etablissement = new Service_Etablissement;

        if($this->_request->isGet())
        {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->deleteContact($this->_request->id, $this->_request->id_contact);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'Le contact a bien été supprimé de la fiche établissement.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Suppression annulée', 'message' => 'Le contact n\'a été supprimé. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('edit-contacts', null, null, array('id' => $this->_request->id));
        }
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
}
