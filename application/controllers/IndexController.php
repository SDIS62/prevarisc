<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * Page d'accueil
     *
     */
    public function indexAction()
    {
        // Définition du layout
        $this->_helper->layout->setLayout('menu_left');
        
        // Modèles
        $DB_messages = new Model_DbTable_News;
        $DB_groupe = new Model_DbTable_Groupe;
        
        // Récupération du fil d'actualité pour l'utilisateur
        $this->view->flux = $DB_messages->getNews(Zend_Auth::getInstance()->getIdentity()->ID_GROUPE);
        
        // Récupération de l'ensemble des groupes
        $this->view->groupes = $DB_groupe->fetchAll()->toArray();
    }
    
    /**
     * Ajouter un message dans le feed
     *
     */
    public function addMessageAction()
    {
        try
        {
            // Modèle
            $model = new Model_DbTable_News;
            
            // On ajoute la news dans la db
            $model->add($this->_request->getParam('type'), $this->_request->getParam('text'), $this->_request->getParam('conf') );
            
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Ajout réussi !',
                'message' => 'Le message a bien été ajouté.'
            ));
        }
        catch(Exception $e)
        {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Aie',
                'message' => $e->getMessage()
            ));
        }
        
        // Redirection
        $this->_helper->redirector('index');
    }

    /**
     * Supprimer un message du feed
     *
     */
    public function deleteMessageAction()
    {
        try
        {
            // Modèle
            $model = new Model_DbTable_News;

            // On supprime la news dans la db
            $news = $model->deleteNews($this->_request->getParam('id'));
            
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Suppression réussie !',
                'message' => 'Le message a bien été supprimé.'
            ));
        }
        catch(Exception $e)
        {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Aie',
                'message' => $e->getMessage()
            ));
        }
        
        // Redirection
        $this->_helper->redirector('index');
    }
}