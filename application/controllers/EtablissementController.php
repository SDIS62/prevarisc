<?php

class EtablissementController extends Zend_Controller_Action
{
    public function init()
    {
        // Gestion de la nav side, on l'affiche uniquement si un établissement est
        // demandé.
        if ($this->_request->id) {
            $this->view->nav_side_items = array(
                array("text" => "Général", "icon" => "info-sign", "link" => '/etablissement/index/id/'.$this->_request->id),
                array("text" => "Textes applicables", "icon" => "align-center", "link" => '/etablissement/textes-applicables/id/'.$this->_request->id),
                array("text" => "Descriptifs", "icon" => "book", "link" => '/etablissement/descriptif/id/'.$this->_request->id),
                array("text" => "Pièces jointes", "icon" => "share", "link" => '/etablissement/pieces-jointes/id/'.$this->_request->id),
                array("text" => "Contacts", "icon" => "user", "link" => '/etablissement/contacts/id/'.$this->_request->id),
                array("text" => "Dossiers", "icon" => "folder-open", "link" => '/etablissement/dossiers/id/'.$this->_request->id),
                array("text" => "Afficher l'historique", "icon" => "repeat", "link" => '/etablissement/historique/id/'.$this->_request->id),
                array("text" => "Ajouter un dossier", "icon" => "plus", "link" => '/dossier/add/id_etablissement/'.$this->_request->id),
            );
        }
    }

    public function indexAction()
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // Initialisation des services
        $service_etablissement = new Service_Etablissement;
        $service_groupement_communes = new Service_GroupementCommunes;
        $service_carto = new Service_Carto;
        $DB_periodicite = new Model_DbTable_Periodicite;

        // Récupération des ressources
        $store = $this->getInvokeArg('bootstrap')->getResource('dataStore');

        // Récupération des données
        $etablissement = $service_etablissement->get($this->_request->id);
        $couches_cartographiques = $service_carto->getAll();
        $default_periodicite = $DB_periodicite->gn4ForEtablissement($etablissement);
        $groupements_de_communes = count($etablissement['adresses']) == 0 ? array() : $service_groupement_communes->findAll($etablissement['adresses'][0]["NUMINSEE_COMMUNE"]);
        $avis = $service_etablissement->getAvisEtablissement($this->_request->id, $etablissement['general']['ID_DOSSIER_DONNANT_AVIS']);

        // On envoie sur la vue les données nécessaires
        $this->view->couches_cartographiques = $couches_cartographiques;
        $this->view->key_ign = $options['carto']['ign'];
        $this->view->key_googlemap = $options['carto']['google'];
        $this->view->etablissement = $etablissement;
        $this->view->default_periodicite = $default_periodicite;
        $this->view->groupements_de_communes = $groupements_de_communes;
        $this->view->avis = $avis;
        $this->view->store = $store;
    }

    public function editAction()
    {
        // On effectue les mêmes actions que dans l'action index
        $this->indexAction();

        // Initialisation des services
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
        $service_classement = new Service_Classement;

        // Récupération des ressources
        $acl = $this->getInvokeArg('bootstrap')->getResource('acl');

        // On envoie sur la vue les données nécessaires
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
        $this->view->DB_classement = $service_classement->getAll();
        $this->view->add = false;
        $this->view->is_allowed_change_statut = $acl->isUserAllowed("statut_etablissement", "edit_statut");

        // Si un POST a été détecté, on sauvegarde l'établissement
        if($this->_request->isPost()) {
            try {
                // On sauvegarde
                $post = $this->_request->getPost();
                $date = date("Y-m-d");
                $service_etablissement->save($post['ID_GENRE'], $post, $this->_request->id, $date);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'L\'établissement a bien été mis à jour.'));
                $this->_helper->redirector('index', null, null, array('id' => $this->_request->id));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Aie','message' => $e->getMessage()));
            }
        }
    }

    public function addAction()
    {
        // Initialisation des services
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
        $service_classement = new Service_Classement;

        // Récupération des ressources
        $acl = $this->getInvokeArg('bootstrap')->getResource('acl');

        // On envoie sur la vue les données nécessaires
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
        $this->view->DB_classement = $service_classement->getAll();
        $this->view->add = true;
        $this->view->is_allowed_change_statut = $acl->isUserAllowed("statut_etablissement", "edit_statut");

        // Si un POST a été détecté, on ajoute l'établissement
        if($this->_request->isPost()) {
            try {
                // On sauvegarde
                $post = $this->_request->getPost();
                $id_etablissement = $service_etablissement->save($post['ID_GENRE'], $post);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Ajout réussi !', 'message' => 'L\'établissement a bien été ajouté.'));

                // Si un site a été ajouté, on émet un warning sur le fait que le droit
                // d'accès est défini par ses enfants.
                if($post['ID_GENRE'] == 1 && count($post['ID_FILS_ETABLISSEMENT']) == 1) {
                    $this->_helper->flashMessenger(array('context' => 'warning', 'title' => 'Ajout des établissements enfants', 'message' => "Les droits d'accès au site sont déterminés par les droits d'accès aux établissements qui le compose. Veillez à ajouter des établissements afin de garantir l'accès au site dans Prevarisc."));
                    $this->_helper->redirector('edit', null, null, array('id' => $id_etablissement));
                }

                // On redirige vers la fiche établissement créée
                $this->_helper->redirector('index', null, null, array('id' => $id_etablissement));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Aie','message' => $e->getMessage()));
            }
        }

        // La vue rendue est la même que pour la vue d'édition
        $this->render('edit');
    }

    public function descriptifAction()
    {
        // On effectue les mêmes actions que dans l'action index
        $this->indexAction();

        // Initialisation des services
        $service_etablissement = new Service_Etablissement;

        // On récupère les données
        $descriptifs = $service_etablissement->getDescriptifs($this->_request->id);

        // On envoie sur la vue les données nécessaires
        $this->view->descriptif = $descriptifs['descriptif'];
        $this->view->historique = $descriptifs['historique'];
        $this->view->derogations = $descriptifs['derogations'];
        $this->view->champs_descriptif_technique = $descriptifs['descriptifs_techniques'];
    }

    public function editDescriptifAction()
    {
        // On effectue les mêmes actions que dans l'action descriptif
        $this->descriptifAction();

        // Initialisation des services
        $service_etablissement = new Service_Etablissement;

        // Si un POST a été détecté, on édite les descriptifs
        if($this->_request->isPost())
        {
            try {
                // On sauvegarde
                $post = $this->_request->getPost();
                $service_etablissement->saveDescriptifs($this->_request->id, $post['historique'], $post['descriptif'], $post['derogations'], $post['descriptifs_techniques']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Les descriptifs ont bien été mis à jour.'));
                $this->_helper->redirector('descriptif', null, null, array('id' => $this->_request->id));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Mise à jour annulée', 'message' => $e->getMessage()));
            }
        }
    }

    public function textesApplicablesAction()
    {
        // On effectue les mêmes actions que dans l'action index
        $this->indexAction();

        // Initialisation des services
        $service_etablissement = new Service_Etablissement;

        // On récupère les données
        $textes_applicables = $service_etablissement->getAllTextesApplicables($this->_request->id);

        // On envoie sur la vue les données nécessaires
        $this->view->textes_applicables_de_etablissement = $textes_applicables;
    }

    public function editTextesApplicablesAction()
    {
        // On effectue les mêmes actions que dans l'action textes applicables
        $this->textesApplicablesAction();

        // Initialisation des services
        $service_etablissement = new Service_Etablissement;
        $service_textes_applicables = new Service_TextesApplicables;

        // On récupère les données
        $textes_applicables = $service_textes_applicables->getAll();

        // On envoie sur la vue les données nécessaires
        $this->view->textes_applicables = $textes_applicables;

        // Si un POST a été détecté, on édite les textes applicables
        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_etablissement->saveTextesApplicables($this->_request->id, $post['textes_applicables']);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les textes applicables ont bien été mis à jour.'));
                $this->_helper->redirector('textes-applicables', null, null, array('id' => $this->_request->id));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Mise à jour annulée','message' => $e->getMessage()));
            }
        }
    }

    public function piecesJointesAction()
    {
        // On effectue les mêmes actions que dans l'action index
        $this->indexAction();

        // Initialisation des services
        $service_etablissement = new Service_Etablissement;

        // On récupère les données
        $pieces_jointes = $service_etablissement->getAllPJ($this->_request->id);

        // On envoie sur la vue les données nécessaires
        $this->view->pieces_jointes = $pieces_jointes;
    }

    public function getPieceJointeAction()
    {
        // La gestion des pièces jointes est déléguée au controller PieceJointe
        $this->forward('get', 'piece-jointe');
    }

    public function editPiecesJointesAction()
    {
        $service_etablissement = new Service_Etablissement;

        $this->view->etablissement = $service_etablissement->get($this->_request->id);

        $this->view->avis = $service_etablissement->getAvisEtablissement($this->view->etablissement['general']['ID_ETABLISSEMENT'], $this->view->etablissement['general']['ID_DOSSIER_DONNANT_AVIS']);

        $this->view->pieces_jointes = $service_etablissement->getAllPJ($this->_request->id);
        $this->view->store = $this->getInvokeArg('bootstrap')->getResource('dataStore');

    }

    public function addPieceJointeAction()
    {
        $this->_helper->layout->disableLayout();

        $service_etablissement = new Service_Etablissement;

        if($this->_request->isPost())
        {
            try {
                $post = $this->_request->getPost();
                $name = isset($post['name']) ? $post['name'] : '';
                $description = isset($post['description']) ? $post['description'] : '';
                $mise_en_avant = isset($post['mise_en_avant']) ? $post['mise_en_avant'] : 0;
                $service_etablissement->addPJ($this->_request->id, $_FILES['file'], $name, $description, $mise_en_avant);
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
        $service_etablissement = new Service_Etablissement;

        $etablissement = $service_etablissement->get($this->_request->id);

        $this->view->avis = $service_etablissement->getAvisEtablissement($etablissement['general']['ID_ETABLISSEMENT'], $etablissement['general']['ID_DOSSIER_DONNANT_AVIS']);

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
        // On effectue les mêmes actions que dans l'action index
        $this->indexAction();

        // Initialisation des services
        $service_etablissement = new Service_Etablissement;

        // On récupère les données
        $dossiers = $service_etablissement->getDossiers($this->_request->id);

        // On envoie sur la vue les données nécessaires
        $this->view->etudes = $dossiers['etudes'];
        $this->view->visites = $dossiers['visites'];
        $this->view->autres = $dossiers['autres'];
    }

    public function historiqueAction()
    {
        // On effectue les mêmes actions que dans l'action index
        $this->indexAction();

        // Initialisation des services
        $service_etablissement = new Service_Etablissement;

        // On récupère les données
        $historique = $service_etablissement->getHistorique($this->_request->id);

        // On envoie sur la vue les données nécessaires
        $this->view->historique = $historique;
    }
}
