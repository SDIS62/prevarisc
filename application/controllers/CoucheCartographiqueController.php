<?php

    class CoucheCartographiqueController extends Zend_Controller_Action
    {
        public function init()
        {
            $this->_helper->layout->setLayout("menu_left");
        }
        
        public function indexAction()
        {
            $model_couchecarto = new Model_DbTable_CoucheCarto;
            $this->view->rowset_couches = $model_couchecarto->getList();
        }

        public function viewAction()
        {
        }

        public function addAction()
        {
            $model_couchecarto = new Model_DbTable_CoucheCarto;
            $model_couchecartotype = new Model_DbTable_CoucheCartoType;

            $this->view->rowset_couchecartotypes = $model_couchecartotype->fetchAll();

            // On process les données
            if (null != ($data = $this->getRequest()->getPost())) {
                $model_couchecarto->insert($data);
                $this->_redirect("/couche-cartographique");
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
                $this->_redirect("/couche-cartographique");
            }

            $this->_forward("add");
        }

        public function deleteAction()
        {
            $model_couchecarto = new Model_DbTable_CoucheCarto;
            $model_couchecarto->delete("ID_COUCHECARTO = " . $this->getRequest()->getParam('id'));
            $this->_redirect("/couche-cartographique");
        }
    }
