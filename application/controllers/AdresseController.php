<?php

    class AdresseController extends Zend_Controller_Action
    {
        private $DB_adresse = null;

        public function init()
        {
            // Actions à effectuées en AJAX
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
            $ajaxContext->addActionContext('get-ville-par-code-postal', 'json')
                        ->addActionContext('get-types-voie-par-ville', 'json')
                        ->addActionContext('get-voies', 'json')
                        ->addActionContext('get-lat-lng', 'json')
                        ->addActionContext('get', 'json')
                        ->initContext();

            $this->DB_adresse = new Model_DbTable_EtablissementAdresse;
        }

        // Donne la liste de ville par rapport à un code postal
        public function getVilleParCodePostalAction()
        {
            $this->view->villes = $this->DB_adresse->getVilleByCP( $this->_request->code_postal );
        }

        // Retourne les types de voie d'une commune
        public function getTypesVoieParVilleAction()
        {
            $this->view->types_voies = $this->DB_adresse->getTypesVoieByVille( $this->_request->code_insee );
        }

        // Retourne les voies par rapport à une ville
        public function getVoiesAction()
        {
            $this->view->resultats = $this->DB_adresse->getVoies( $this->_request->code_insee, $this->_request->q );
        }

        public function getAction()
        {
            // Création de l'objet recherche
            $model_adresse = new Model_DbTable_AdresseCommune;

            // On balance le résultat sur la vue
            $this->view->resultats = $model_adresse->get($this->_request->q);
        }

    }
