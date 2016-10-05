<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Récupération de la configuration du dashboard
        $options = Zend_Registry::get('options')['dashboard'];

        // Initialisation des services
        $service_user = new Service_User;
        $service_dashboard = new Service_Dashboard($options);

        // On récupère la configuration des blocs
        $blocsConfig = $service_dashboard->getBlocConfig();

        // Récupération de l'ACL
        $acl = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('acl');

        // Récupération de l'utilisateur connecté
        $user = $service_user->find(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);

        // Variable qui contiendra les blocs de l'utilisateur
        $blocs = array();

        // Détermine l'ordre des blocs en fonction des préférences utilisateur
        if (!empty($user['preferences']['DASHBOARD_BLOCS']) && $blocsOrder = json_decode($user['preferences']['DASHBOARD_BLOCS'])) {
            foreach(array_keys($blocsConfig) as $defaultBloc) {
                if (!in_array($defaultBloc, $blocsOrder)) {
                    $blocsOrder[] = $defaultBloc;
                }
            }
        } else {
            $blocsOrder = array_keys($blocsConfig);
        }

        // On récupère, dans l'ordre, les blocs de l'utilisateur
        foreach($blocsConfig as $blocId => $blocConfig) {
            if (!$blocConfig['acl'] || ($acl->isUserAllowed($blocConfig['acl'][0], $blocConfig['acl'][1]))) {
                $method = $blocConfig['method'];
                $blocs[array_search($blocId, $blocsOrder)] = array(
                    'id' => $blocId,
                    'type' => $blocConfig['type'],
                    'title' => $blocConfig['title'],
                    'height' => $blocConfig['height'],
                    'width' => $blocConfig['width'],
                );
            }
        }

        // On tri le tableau des blocs en fonction de leur index
        ksort($blocs);

        // On envoie sur la vue les données nécessaires
        $this->view->user = $user;
        $this->view->blocs = $blocs;

        // On append la lib js permettant de gérer les blocs du dashboard
        $this->view->inlineScript()->appendFile("/js/jquery.packery.pkgd.min.js");
    }

    public function blocAction()
    {
        // Désactivation du layout
        $this->_helper->layout->disableLayout();

        // Initialisation des services
        $service_user = new Service_User;
        $service_dashboard = new Service_Dashboard;

        // On récupère l'id du bloc
        $id = $this->getParam('id');

        // On décide si on veut afficher une version allégée, ou non du bloc
        $light = $this->getParam('light');

        // On récupère la configuration des blocs
        $blocsConfig = $service_dashboard->getBlocConfig();

        // Si le bloc demandé n'existe pas, on lève une erreur
        if (!isset($blocsConfig[$id])) {
            throw new Exception("Erreur du bloc " . $id);
        }

        // Récupération de l'utilisateur connecté
        $user = Zend_Auth::getInstance()->getIdentity();

        // On envoie sur la vue les données du bloc
        $blocConfig = $blocsConfig[$id];
        $service = new $blocConfig['service'];
        $method = $blocConfig['method'];
        $this->view->id = $id;
        $this->view->data = $service->$method($user);
        $this->view->title = $blocConfig['title'];
        $this->view->height = $blocConfig['height'];
        $this->view->width = $blocConfig['width'];

        // On rend le bloc demandé
        $this->render("partials/blocs/contents/".$blocConfig['type']);
    }

    public function addMessageAction()
    {
        // Initialisation des services
        $service_feed = new Service_Feed;
        $service_user = new Service_User;

        $this->view->groupes = $service_user->getAllGroupes();

        // Si un POST a été détecté, on sauvegarde le message
        if ($this->_request->isPost()) {
            // On tente la sauvegarde
            try {
                $post = $this->_request->getPost();
                $id_user = Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'];
                $service_feed->addMessage($post['type'], $post['text'], $id_user, $post['conf']);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message ajouté !','message' => 'Le message a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur !','message' => $e->getMessage()));
            }
            // On revient sur la page index
            $this->_helper->redirector('index');
        }
    }

    public function deleteMessageAction()
    {
        // Initialisation des services
        $service_feed = new Service_Feed;

        // On tente la suppression
        try {
            $service_feed->deleteMessage($this->_request->getParam('id'));
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message supprimé !','message' => 'Le message a bien été supprimé.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur !','message' => $e->getMessage()));
        }

        // On revient sur la page index
        $this->_helper->redirector('index');
    }
}
