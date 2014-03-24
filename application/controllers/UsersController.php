<?php

class UsersController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;
        $service_search = new Service_Search;

        $this->view->users = $service_search->users(null, null, $this->hasParam('gid') ? $this->_request->getParam('gid') : null, false, 100)['results'];
        $this->view->groupes = $service_user->getAllGroupes();
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;
        $service_groupement = new Service_GroupementCommunes;
        $service_commission = new Service_Commission;
        $service_adresse = new Service_Adresse;

        $this->view->user = $service_user->find($this->_request->getParam('uid'));
        $this->view->commissions = $service_commission->getAll();
        $this->view->groupements = $service_groupement->findAll();
        $this->view->fonctions = $service_user->getAllFonctions();
        $this->view->communes = $service_adresse->getAllCommunes();
        $this->view->groupes = $service_user->getAllGroupes();
        $this->view->params = array("LDAP_ACTIF" => Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('ldap')['enabled']);

        $this->view->add = false;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_user->save($post, $_FILES['avatar'], $this->_request->uid);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'L\'utilisateur a bien été mis à jour.'));
                $this->_helper->redirector('index', null, null);
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'L\'utilisateur n\'a pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function addAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;
        $service_groupement = new Service_GroupementCommunes;
        $service_commission = new Service_Commission;
        $service_adresse = new Service_Adresse;

        $this->view->commissions = $service_commission->getAll();
        $this->view->groupements = $service_groupement->findAll();
        $this->view->fonctions = $service_user->getAllFonctions();
        $this->view->communes = $service_adresse->getAllCommunes();
        $this->view->groupes = $service_user->getAllGroupes();
        $this->view->params = array("LDAP_ACTIF" => Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('ldap')['enabled']);

        $this->view->add = true;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_user->save($post, $_FILES['avatar']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'L\'utilisateur a bien été ajouté.'));
                $this->_helper->redirector('index', null, null);
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'L\'utilisateur n\'a pas ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        $this->render('edit');
    }

    public function matriceDesDroitsAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        // Modèles
        $model_groupes = new Model_DbTable_Groupe;
        $model_resource = new Model_DbTable_Resource;
        $model_groupes_privilege = new Model_DbTable_GroupePrivilege;

        // On envoit les données sur la vue
        $this->view->rowset_groupes = $model_groupes->fetchAll();
        $this->view->rowset_resources = $model_resource->fetchAll();
        $this->view->rowset_groupes_privilege = $model_groupes_privilege->fetchAll()->toArray();

        // Si des données sont envoyées, on procède à leur traitement
        if ($this->_request->isPost()) {
            try {
                foreach ($this->_request->getParam('groupe') as $id_groupe => $privileges) {
                    foreach ($privileges as $id_privilege => $value_privilege) {
                        $groupe_privilege_exists = $model_groupes_privilege->find($id_groupe, $id_privilege)->current() !== null;

                        if ($value_privilege == 1 && !$groupe_privilege_exists) {
                            $row_groupe_priv = $model_groupes_privilege->createRow();
                            $row_groupe_priv->ID_GROUPE = $id_groupe;
                            $row_groupe_priv->id_privilege = $id_privilege;
                            $row_groupe_priv->save();
                        }

                        if ($value_privilege == 0 && $groupe_privilege_exists) {
                            $model_groupes_privilege->delete('ID_GROUPE = ' . $id_groupe . ' AND id_privilege = ' . $id_privilege);
                        }
                    }
                }

                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Mise à jour réussie !',
                    'message' => 'La matrice des droits a bien été mise à jour.'
                ));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Aie',
                    'message' => $e->getMessage()
                ));
            }
            // Redirection
            $this->_helper->redirector('matrice-des-droits');
        }
    }

    public function editGroupAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;

        $this->view->group = $service_user->getGroup($this->_request->gid);

        $this->view->add = false;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_user->saveGroup($post, $this->_request->gid);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le groupe a bien été mis à jour.'));
                $this->_helper->redirector('index', null, null);
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'Le groupe n\'a pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function addGroupAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;

        $this->view->add = true;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_user->saveGroup($post);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le groupe a bien été ajouté.'));
                $this->_helper->redirector('index', null, null);
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'Le groupe n\'a pas été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        $this->render('edit-group');
    }

    public function deleteGroupAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;

        $this->view->add = true;

        try {
            $post = $this->_request->getPost();
            $service_user->deleteGroup($this->_request->gid);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Suppression réussie !','message' => 'Le groupe a été supprimé.'));
        }
        catch(Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'Erreur dans la suppression du groupe. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector('index', null, null);
    }
}
