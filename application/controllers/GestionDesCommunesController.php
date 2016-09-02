<?php

class GestionDesCommunesController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Initialisation des services
        $service_adresse = new Service_Adresse();

        // Liste des communes
        $communes = $service_adresse->getAllCommunes();

        // Liste des villes pour le select
        $this->view->rowset_communes = $communes;
    }

    public function displayAction()
    {
        // Initialisation des services
        $service_adresse = new Service_Adresse();

        // On récupère la commune
        $commune = $service_adresse->find($this->_request->numinsee);

        // On envoie le tout sur la vue
        $this->view->user_info = $commune['coord'];
        $this->view->commune = $commune['commune'];
        $this->view->ext = $this->_request->ext;
    }

    public function saveAction()
    {
        // Initialisation des services
        $service_adresse = new Service_Adresse();

        // On désactive le rendu de la vue
        $this->_helper->viewRenderer->setNoRender();

        // On tente la sauvegarde de la commune
        try {
            $service_adresse->save($_GET["numinsee"], $this->_request->getPost());
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Sauvegarde réussie !','message' => 'La commune a été enregistrée.'));
        } catch (Exception $ex) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => 'Aie','message' => $ex->getMessage()));
        }

        // Redirection vers l'action index
        $this->_helper->redirector('index');
    }
}
