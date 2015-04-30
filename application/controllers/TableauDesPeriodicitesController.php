<?php

class TableauDesPeriodicitesController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Définition du layout
        $this->_helper->layout->setLayout('menu_admin');

        // Liste des types d'activité
        $activite_model = new Model_DbTable_Type();
        $this->view->array_types = $activite_model->fetchAll()->toArray();

        // Liste des catégorie
        $cat_model = new Model_DbTable_Categorie();
        $this->view->array_categories = $cat_model->fetchAll()->toArray();

        // Liste des classes
        $classe_model = new Model_DbTable_Classe();
        $this->view->array_classes = $classe_model->fetchAll()->toArray();

        // Les périodicités
        $perio_model = new Model_DbTable_Periodicite();
        $tableau = $perio_model->fetchAll()->toArray();

        $result = array();

        for ($i=0; $i < count($tableau); $i++) {
            // Sans local sommeil
            $result[$tableau[$i]["ID_CATEGORIE"]][$tableau[$i]["ID_TYPE"]][$tableau[$i]["LOCALSOMMEIL_PERIODICITE"]] = $tableau[$i]["PERIODICITE_PERIODICITE"];

            // Avec local (on exclu igh == categ à 0)
            if($tableau[$i]["ID_CATEGORIE"] != 0)
                $result[$tableau[$i++]["ID_CATEGORIE"]][$tableau[$i]["ID_TYPE"]][$tableau[$i]["LOCALSOMMEIL_PERIODICITE"]] = $tableau[$i]["PERIODICITE_PERIODICITE"];
        }

        $this->view->tableau = $result;

    }

    public function saveAction()
    {
        try {
            // Model des périodicités
            $perio_model = new Model_DbTable_Periodicite();

            // Requests
            $request = $this->getRequest();

            foreach ( $request->getPost() as $key => $value ) {
                $result = explode("_", $key);

                if(  $item = $perio_model->find($result[0], $result[1], $result[2])->current() == null )
                    $item = $perio_model->createRow();
                else
                    $item = $perio_model->find($result[0], $result[1], $result[2])->current();

                $item->ID_CATEGORIE = $result[0];
                $item->ID_TYPE = $result[1];
                $item->LOCALSOMMEIL_PERIODICITE = $result[2];
                $item->PERIODICITE_PERIODICITE = $value;
                $item->save();

                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Le tableau des périodicités a bien été sauvegardé',
                    'message' => ''
                ));
            }
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la sauvegarde du tableau des périodicités',
                'message' => $e->getMessage()
            ));
        }

        // Redirection
        $this->_helper->redirector('index');
    }

    public function applyAction()
    {
        try {
            // Model des périodicités
            $perio_model = new Model_DbTable_Periodicite();
            $perio_model->apply();

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'OKAY!',
                'message' => 'Le tableau des périodicités a bien été appliqué'
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la sauvegarde du tableau des périodicités',
                'message' => $e->getMessage()
            ));
        }

        // Redirection
        $this->_helper->redirector('index');
    }
}
