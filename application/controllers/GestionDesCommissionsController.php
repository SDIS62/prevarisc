<?php

    class GestionDesCommissionsController extends Zend_Controller_Action
    {
        // Page d'acceuil
        public function indexAction()
        {
            try {
                // Titre
                $this->view->title = "Gestion des commissions";

                $this->_helper->layout->setLayout('menu_left');

                // Modèles de données
                $model_typesDesCommissions = new Model_DbTable_CommissionType;

                $this->view->rowset_typesDesCommissions = $model_typesDesCommissions->fetchAll();
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Erreur inattendue',
                    'message' => $e->getMessage()
                ));
            }
        }

        public function formAction()
        {
            try {
                // Modèles de données
                $model_typesDesCommissions = new Model_DbTable_CommissionType;
                $model_commissions = new Model_DbTable_Commission;

                $this->view->rowset_typesDesCommissions = $model_typesDesCommissions->fetchAll();
                $this->view->rowset_commissions = $model_commissions->fetchAll();
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Erreur inattendue',
                    'message' => $e->getMessage()
                ));
            }
        }

        public function saveAction()
        {
            try {
                $this->_helper->viewRenderer->setNoRender();

                // Modèles de données
                $model_commissions = new Model_DbTable_Commission;

                // Sauvegarde
                for ($i = 0; $i < count($this->_request->id_commission); $i++) {
                    if ($_POST["id_commission"][$i] != 0) {
                        $item = $model_commissions->find($_POST["id_commission"][$i])->current();
                        $item->ID_COMMISSIONTYPE = $_POST["idtype_commission"][$i];
                        $item->LIBELLE_COMMISSION = $_POST["nom_commission"][$i];
                        $item->save();
                    } else {
                        $item = $model_commissions->createRow();
                        $item->ID_COMMISSIONTYPE = $_POST["idtype_commission"][$i];
                        $item->LIBELLE_COMMISSION = $_POST["nom_commission"][$i];
                        $item->save();
                    }
                }
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Erreur inattendue',
                    'message' => $e->getMessage()
                ));
            }
        }

        // Récupération des commission avec un type donné
        public function getCommissionsAction()
        {
            try {
                // Modèles de données
                $model_commission = new Model_DbTable_Commission;
                $model_typesDesCommissions = new Model_DbTable_CommissionType;

                // On récupère les commissions du type demandé
                $this->view->rowset_commissions = $model_commission->fetchAll("ID_COMMISSIONTYPE = " . $this->_request->id_type_des_commissions );

                // On récupère le type
                $this->view->row_typeDesCommissions = $model_typesDesCommissions->fetchRow("ID_COMMISSIONTYPE = " . $this->_request->id_type_des_commissions);
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Erreur inattendue',
                    'message' => $e->getMessage()
                ));
            }
        }

        public function addCommissionAction()
        {
            try {
                // Modèle
                $DB_commission = new Model_DbTable_Commission;

                // Si on sauvegarde, on désactive le rendu
                if (isset($_GET["action"]) && $_GET["action"] == "save") {
                    $this->_helper->viewRenderer->setNoRender(true);
                }

                if ( !empty($this->_request->cid) ) {

                    $this->view->commission = $DB_commission->find( $this->_request->cid )->current();

                    if (isset($_GET["action"]) && $_GET["action"] == "save") {
                        $this->view->commission->setFromArray(array_intersect_key($_POST, $DB_commission->info('metadata')))->save();
                    }
                } elseif (isset($_GET["action"]) && $_GET["action"] == "save") {

                    $DB_commission->insert(array_intersect_key($_POST, $DB_commission->info('metadata')));
                }

                $this->view->tid = $_GET["tid"];
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Erreur inattendue',
                    'message' => $e->getMessage()
                ));
            }
        }
    }
