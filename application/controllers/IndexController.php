<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_left');

        $service_feed = new Service_Feed;
        $service_user = new Service_User;

        $this->view->flux = $service_feed->get(Zend_Auth::getInstance()->getIdentity()->ID_GROUPE);
        $this->view->groupes = $service_user->getAllGroupes();
    }

    public function addMessageAction()
    {
        try {
            $service_feed = new Service_Feed;
            $service_feed->addMessage($this->_request->getParam('type'), $this->_request->getParam('text'), $this->_request->getParam('conf') );
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message ajouté !','message' => 'Le message a bien été ajouté.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => 'Erreur !','message' => 'Erreur lors de l\'ajout du message : ' . $e->getMessage()));
        }

        $this->_helper->redirector('index');
    }

    public function deleteMessageAction()
    {
        try {
            $service_feed = new Service_Feed;
            $service_feed->deleteMessage($this->_request->getParam('id'));
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message supprimé !','message' => 'Le message a bien été supprimé.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => 'Erreur !','message' => 'Erreur lors de la suppression du message : ' . $e->getMessage()));
        }

        $this->_helper->redirector('index');
    }
}
