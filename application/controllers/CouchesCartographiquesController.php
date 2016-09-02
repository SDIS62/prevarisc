<?php

class CouchesCartographiquesController extends Zend_Controller_Action
{
    public function listAction()
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // Initialisation des services
        $service_carto = new Service_Carto;

        // On envoie sur la vue les couches + la clé IGN
        $this->view->couches_cartographiques = $service_carto->getAll();
        $this->view->key_ign = $options['carto']['ign'];
    }

    public function addAction()
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // On envoie sur la vue la clé IGN (pour l'affichage de la carte)
        $this->view->key_ign = $options['carto']['ign'];

        // Initialisation des services
        $service_carto = new Service_Carto;

        // Si on détecte un POST, on lance la sauvegarde
        if ($this->_request->isPost()) {
            try {
                // On tente l'ajout
                $data = $this->getRequest()->getPost();
                $service_carto->save($data);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'La couche cartographique a été ajoutée.'));
                $this->_helper->redirector('list');
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Aie','message' => $e->getMessage()));
            }
        }
    }

    public function editAction()
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // Initialisation des services
        $service_carto = new Service_Carto;

        // On récupère la couche cartographique à éditer
        $couche = $service_carto->findById($this->getRequest()->getParam('id'));

        // On envoie sur la vue les données
        $this->view->row = $couche;
        $this->view->key_ign = $options['carto']['ign'];

        // Si on détecte un POST, on lance la sauvegarde
        if ($this->_request->isPost()) {
            // On tente la sauvegarde
            try {
                $data = $this->getRequest()->getPost();
                $service_carto->save($data, $this->getRequest()->getParam('id'));
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'La couche cartographique a été ajoutée.'));
                $this->_helper->redirector('list');
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'La couche cartographique n\'a pas été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        // On utilise la même vue que pour l'ajout
        $this->render('add');
    }

    public function deleteAction()
    {
        // On désactive le rendu sur la vue
        $this->_helper->viewRenderer->setNoRender(true);

        // Initialisation des services
        $service_carto = new Service_Carto;

        // On tente la suppression
        try {
            $service_carto->delete($this->getRequest()->getParam('id'));
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'La couche cartographique a été supprimée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => 'Aie','message' => $e->getMessage()));
        }

        // On redirige vers la liste des couches
        $this->_helper->redirector('list');
    }
}
