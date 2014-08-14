<?php

class NewsController extends Zend_Controller_Action
{
    public function addAction()
    {
        $this->_helper->layout->disableLayout();

        $service_user = new Service_User;

        $this->view->groupes = $service_user->getAllGroupes();

        if($this->_request->isPost()) {
            try {
                $service_feed = new Service_Feed;
                $service_feed->addMessage($this->_request->getParam('type'), $this->_request->getParam('text'), Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'], $this->_request->getParam('conf') );
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message ajouté !','message' => 'Le message a bien été ajouté.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Erreur !','message' => 'Erreur lors de l\'ajout du message : ' . $e->getMessage()));
            }

            $this->_helper->redirector('index', 'index');
        }
    }

    public function deleteAction()
    {
        try {
            $service_feed = new Service_Feed;
            $service_feed->deleteMessage($this->_request->getParam('id'));
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message supprimé !','message' => 'Le message a bien été supprimé.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => 'Erreur !','message' => 'Erreur lors de la suppression du message : ' . $e->getMessage()));
        }

        $this->_helper->redirector('index', 'index');
    }
}
