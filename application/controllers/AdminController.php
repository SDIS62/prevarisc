<?php

    class AdminController extends Zend_Controller_Action
    {
        public function init()
        {
            $this->view->action = $this->_request->getActionName();
        }

        // Accueil forwardé vers groupement de communes
        public function indexAction()
        {
            $this->_helper->layout->setLayout('menu_left');
            $this->view->title = "Administration système";

            // Modèle de données
            $model_admin = new Model_DbTable_Admin;
            $this->view->info = $model_admin->getParams();
        }

        public function saveAction()
        {
            try
            {
                // Modèle de données
                $model_admin = new Model_DbTable_Admin;

                $infos = $model_admin->find(1)->current();
                $infos->LDAP_ACTIF = 0;
                $infos->LDAP_LOGIN = 0;
                $infos->setFromArray(array_intersect_key($_POST, $model_admin->info('metadata')))->save();
                
                // Message de réussite
                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Sauvegarde réussie !',
                    'message' => 'Vos paramètres ont bien été sauvegardés.'
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
