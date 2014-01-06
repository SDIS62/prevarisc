<?php

class CouchesCartographiquesController extends Zend_Controller_Action
{
    /**
     * @inheritdoc
     */  
    public function init()
    {
        // Définition du layout menu_left
        $this->_helper->layout->setLayout('menu_left');
    }
    
    /**
     * Liste des couches cartographiques
     *
     */
    public function listAction()
    {
        // Modèles 
        $model_couchecarto = new Model_DbTable_CoucheCarto;
        
        // On envoie la liste complète sur la vue
        $this->view->rowset_couches = $model_couchecarto->getList();
    }

    /**
     * Ajouter une couche cartographique
     *
     */
    public function addAction()
    {
        // Modèles
        $model_couchecarto = new Model_DbTable_CoucheCarto;
        $model_couchecartotype = new Model_DbTable_CoucheCartoType;

        // On envoie sur la vue les différents type de couches
        $this->view->rowset_couchecartotypes = $model_couchecartotype->fetchAll();

        // On process les données
        if($this->_request->isPost())
        {
            $data = $this->getRequest()->getPost();
            $model_couchecarto->insert($data);
            
            // Redirection
            $this->_helper->redirector('list');
        }
    }

    public function editAction()
    {
        $model_couchecarto = new Model_DbTable_CoucheCarto;
        $this->view->row = $model_couchecarto->fetchRow("ID_COUCHECARTO = " . $this->getRequest()->getParam('id'));

        // On process les données
        if (null != ($data = $this->getRequest()->getPost())) {
            $row = $model_couchecarto->find($this->getRequest()->getParam('id'))->current();
            $row->ISBASELAYER_COUCHECARTO = 0;
            $row->TRANSPARENT_COUCHECARTO = 0;
            $row->INTERACT_COUCHECARTO = 0;
            $row->setFromArray(array_intersect_key($data, $model_couchecarto->info('metadata')))->save();
            
            // Redirection
            $this->_helper->redirector('list');
        }

        $this->_forward("add");
    }

    public function deleteAction()
    {
        // Modèle
        $model_couchecarto = new Model_DbTable_CoucheCarto;
        
        // Suppression
        $model_couchecarto->delete("ID_COUCHECARTO = " . $this->getRequest()->getParam('id'));
        
        // Redirection
        $this->_helper->redirector('list');
    }
}