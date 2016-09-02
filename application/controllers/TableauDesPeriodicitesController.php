<?php

class TableauDesPeriodicitesController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // Initialisation des services
        $service_type = new Service_Type();
        $service_categorie = new Service_Categorie();
        $service_classe = new Service_Classe();
        $service_periodicite = new Service_Periodicite();

        // Récupération des types / catégories / classes et tableau pour la vue
        $array_types = $service_type->getAll();
        $array_categories = $service_categorie->getAll();
        $array_classes = $service_classe->getAll();
        $tableau = $service_periodicite->getAll();

        // On envoie sur la vue les données nécessaires
        $this->view->tableau = $tableau;
        $this->view->array_types = $array_types;
        $this->view->array_categories = $array_categories;
        $this->view->array_classes = $array_classes;
        $this->view->localsommeil_types = $options['types_sans_local_sommeil'];
    }

    public function saveAction()
    {
        // Initialisation des services
        $service_periodicite = new Service_Periodicite();

        // On tente la sauvegarde
        try {
            // On sauvegarde le tableau des périodicités
            $service_periodicite->save($this->_request->getPost());
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Sauvegardé !','message' => 'Le tableau des périodicités a bien été sauvegardé'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => 'Erreur lors de la sauvegarde du tableau des périodicités','message' => $e->getMessage()));
        }

        // On revient sur la page principale
        $this->_helper->redirector('index');
    }

    public function applyAction()
    {
        // Initialisation des services
        $service_periodicite = new Service_Periodicite();

        // On tente d'appliquer le tableau des périodicités
        try {
            $service_periodicite->apply();
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Tableau des périodicités appliqué!','message' => "Le tableau des périodicités a bien été appliqué sur l'ensemble des établissements de Prevarisc"));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => 'Erreur lors de la sauvegarde du tableau des périodicités','message' => $e->getMessage()));
        }

        // On revient sur la page principale
        $this->_helper->redirector('index');
    }
}
