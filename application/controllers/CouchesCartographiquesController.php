<?php

class CouchesCartographiquesController extends Zend_Controller_Action
{
    public function listAction()
    {
        $this->_helper->layout->setLayout('menu_left');

        $service_carto = new Service_Carto;
        $this->view->couches_cartographiques = $service_carto->getAll();
    }

    public function addAction()
    {
        $this->_helper->layout->setLayout('menu_left');

        $service_carto = new Service_Carto;

        if ($this->_request->isPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $service_carto->add($data);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'La couche cartographique a été ajoutée.'));
                $this->_helper->redirector('list');
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'La couche cartographique n\'a pas été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('menu_left');

        $service_carto = new Service_Carto;

        if ($this->_request->isPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $service_carto->edit($this->getRequest()->getParam('id'), $data);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'La couche cartographique a été ajoutée.'));
                $this->_helper->redirector('list');
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'La couche cartographique n\'a pas été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        $this->render('add');
    }

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $service_carto = new Service_Carto;

        try {
            $service_carto->delete($this->getRequest()->getParam('id'));
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Ajout réussi !','message' => 'La couche cartographique a été supprimée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'La couche cartographique n\'a pas été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector('list');
    }
}
