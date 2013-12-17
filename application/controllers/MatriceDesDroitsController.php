<?php

    class MatriceDesDroitsController extends Zend_Controller_Action
    {
        public function indexAction()
        {
            $this->_helper->layout->setLayout("menu_left");
            
            // Modèles
            $model_groupes = new Model_DbTable_Groupe;
            $model_resource = new Model_DbTable_Resource;
            $model_groupes_privilege = new Model_DbTable_GroupePrivilege;

            // On envoit les données sur la vue
            $this->view->rowset_groupes = $model_groupes->fetchAll();
            $this->view->rowset_resources = $model_resource->fetchAll();
            $this->view->rowset_groupes_privilege = $model_groupes_privilege->fetchAll()->toArray();
        }

        public function saveAction()
        {
            try
            {
                $model_groupes_privilege = new Model_DbTable_GroupePrivilege;
                
                foreach($this->_request->getParam('groupe') as $id_groupe => $privileges)
                { 
                    foreach($privileges as $id_privilege => $value_privilege)
                    {
                        $groupe_privilege_exists = $model_groupes_privilege->find($id_groupe, $id_privilege)->current() !== null;
                        
                        if($value_privilege == 1 && !$groupe_privilege_exists)
                        {
                            $row_groupe_priv = $model_groupes_privilege->createRow();
                            $row_groupe_priv->ID_GROUPE = $id_groupe;
                            $row_groupe_priv->id_privilege = $id_privilege;
                            $row_groupe_priv->save();
                        }
                        
                        if($value_privilege == 0 && $groupe_privilege_exists)
                        {
                            $model_groupes_privilege->delete('ID_GROUPE = ' . $id_groupe . ' AND id_privilege = ' . $id_privilege);
                        }
                    }
                }
                
                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Mise à jour réussie !',
                    'message' => 'La matrice des droits a bien été mise à jour.'
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
