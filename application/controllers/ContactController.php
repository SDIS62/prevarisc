<?php

class ContactController extends Zend_Controller_Action
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        // Actions à effectuées en AJAX
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
                    ->addActionContext('display', 'html')
                    ->addActionContext('delete', 'json')
                    ->addActionContext('add', 'json')
                    ->addActionContext('get', 'json')
                    ->addActionContext('edit', 'html')
                    ->initContext();
    }

    public function indexAction()
    {
        $DB_contact = new Model_DbTable_UtilisateurInformations;
        $this->view->contacts = $DB_contact->getContact($this->_request->item, $this->_request->id);

        // Placement
        $this->view->item = $this->_request->item;
        $this->view->id = $this->_request->id;
		$this->view->verrou = $this->_request->verrou;
		
        $this->view->ajax = $this->_request->ajax;

        // Si on est dans un établissement, on cherche les contacts des ets parents
        if ($this->_request->item == "etablissement") {

            $model_ets = new Model_DbTable_Etablissement;
            $etablissement_parents = $model_ets->getAllParents($this->_request->id);
            $array = array();

            if($etablissement_parents != null)
                foreach($etablissement_parents as $ets)
                    if ($ets != null) {
                        $contacts = $DB_contact->getContact($this->_request->item, $ets["ID_ETABLISSEMENT"]);
                        if($contacts != null)
                            $array[] = $contacts;
                    }
            $this->view->contacts_parent = $array;
        }

        // Taille des cases
        $this->view->size = ( $this->_request->item == "dossier" ) ? 3 : 4;
    }

    public function formAction()
    {
        // On récupère la liste des fonctions des contacts
        $DB_contactfonction = new Model_DbTable_Fonction;
        $this->view->contact_fonction_list = $DB_contactfonction->fetchAll()->toArray();

        // On récupère la liste des civilités
        $DB_civilite = new Model_DbTable_UtilisateurCivilite;
        $this->view->civilite_list = $DB_civilite->fetchAll()->toArray();

        // Groupes
        $DB_groupe = new Model_DbTable_Groupe;
        $this->view->groupes = $DB_groupe->fetchAll()->toArray();

        // Placement
        $this->view->item = $this->_request->item;
        $this->view->id = $this->_request->id;
    }

    public function addAction()
    {
        try {
            if(isset($_POST["ID_UTILISATEURCIVILITE"]) && $_POST["ID_UTILISATEURCIVILITE"] == "null")
                unset($_POST["ID_UTILISATEURCIVILITE"]);

            $key = null;
            $DB_contact = null;

            // Initalisation des modèles
            $DB_informations = new Model_DbTable_UtilisateurInformations;

            switch ($this->_request->item) {
                case "etablissement":
                    $DB_contact = new Model_DbTable_EtablissementContact;
                    $key = "ID_ETABLISSEMENT";
                    break;
                case "dossier":
                    $DB_contact = new Model_DbTable_DossierContact;
                    $key = "ID_DOSSIER";
                    break;
                case "groupement":
                    $DB_contact = new Model_DbTable_GroupementContact;
                    $key = "ID_GROUPEMENT";
                    break;
                case "commission":
                    $DB_contact = new Model_DbTable_CommissionContact;
                    $key = "ID_COMMISSION";
                    break;
            }

            $id_item = $this->_request->id;
            $exist = isset($_POST["exist"]) ? $_POST["exist"] : false;

            if (!$exist) {
                // Mise en base du contact
                $id = $DB_informations->insert(array_intersect_key($_POST, $DB_informations->info('metadata')));
            }

            // Association du contact.
            $contact = $DB_contact->createRow();
            $contact->$key = $id_item;
            $contact->ID_UTILISATEURINFORMATIONS = $exist ? $_POST["ID_UTILISATEURINFORMATIONS"] : $id;
            $contact->save();
            
            // Suppression du cache de l'item
            $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
            $cache->remove($this->_request->item.'_id_' .$id_item);

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Le contact a bien été ajouté',
                'message' => ''
            ));

        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de l\'ajout du contact',
                'message' => $e->getMessage()
            ));
        }
    }

    public function editAction()
    {
        try {
            if(isset($_POST["ID_UTILISATEURCIVILITE"]) && $_POST["ID_UTILISATEURCIVILITE"] == "null")
                unset($_POST["ID_UTILISATEURCIVILITE"]);

            $DB_informations = new Model_DbTable_UtilisateurInformations;
            $DB_contact = new Model_DbTable_EtablissementContact;
            $row = $DB_informations->find( $this->_request->id )->current();
            $this->view->user_info = $row;
            
            if ($_POST) {
                $this->_helper->viewRenderer->setNoRender(); // On desactive la vue
                $row->setFromArray(array_intersect_key($_POST, $DB_informations->info('metadata')))->save();
                
                // Suppression du cache des l'items associés
                $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
                $items = $DB_contact->fetchAll("ID_UTILISATEURINFORMATIONS = " . $this->_request->id)->toArray();
                foreach($items as $item) {
                    $cache->remove('etablissement_id_' .$item['ID_ETABLISSEMENT']);
                }
                
            } else
                $this->_forward("form");

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Le contact a bien été modifié',
                'message' => ''
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la modification du contact',
                'message' => $e->getMessage()
            ));
        }
    }

    public function deleteAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();

            $DB_current = null;
            $DB_informations = new Model_DbTable_UtilisateurInformations;
            $DB_contact = array(
                new Model_DbTable_EtablissementContact,
                new Model_DbTable_DossierContact,
                new Model_DbTable_GroupementContact,
                new Model_DbTable_CommissionContact
            );
            $primary = null;

            // Initalisation des modèles
            switch ($this->_request->item) {
                case "etablissement":
                    $DB_current = $DB_contact[0];
                    $primary = "ID_ETABLISSEMENT";
                    break;
                case "dossier":
                    $DB_current = $DB_contact[1];
                    $primary = "ID_DOSSIER";
                    break;
                case "groupement":
                    $DB_current = $DB_contact[2];
                    $primary = "ID_GROUPEMENT";
                    break;
                case "commission":
                    $DB_current = $DB_contact[3];
                    $primary = "ID_COMMISSION";
                    break;
            }

            // Appartient à d'autre ets ?
            $exist = false;
            foreach ($DB_contact as $key => $model) {
                if (count($model->fetchAll("ID_UTILISATEURINFORMATIONS = " . $this->_request->id)->toArray()) > (($model == $DB_current) ? 1 : 0) ) {
                    $exist = true;
                }
            }

            // Est ce que le contact n'appartient pas à d'autre etablissement ?
            if (!$exist) {
                $DB_current->delete("ID_UTILISATEURINFORMATIONS = " . $this->_request->id); // Porteuse
                $DB_informations->delete( "ID_UTILISATEURINFORMATIONS = " . $this->_request->id ); // Contact
            } else {
                $DB_current->delete("ID_UTILISATEURINFORMATIONS = " . $this->_request->id . " AND " . $primary . " = " . $this->_request->id_item); // Porteuse
            }
            
            
            $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
            $cache->remove($this->_request->item.'_id_' .$this->_request->id);

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Le contact a bien été supprimé',
                'message' => ''
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la suppression du contact',
                'message' => $e->getMessage()
            ));
        }
    }

    public function getAction()
    {
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $this->view->resultats = $DB_informations->getAllContacts($this->_request->q);
    }
}
