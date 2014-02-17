<?php

class AdresseController extends Zend_Controller_Action
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-ville-par-code-postal', 'json')
                    ->addActionContext('get-types-voie-par-ville', 'json')
                    ->addActionContext('get-voies', 'json')
                    ->addActionContext('get', 'json')
                    ->initContext();
    }

    /**
     * Donne la liste de ville par rapport à un code postal
     *
     */
    public function getVilleParCodePostalAction()
    {
        try {
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            $this->view->villes = $DB_adresse->getVilleByCP( $this->_request->code_postal );
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur inattendue',
                'message' => $e->getMessage()
            ));
        }
    }

    /**
     * Retourne les types de voie d'une commune
     *
     */
    public function getTypesVoieParVilleAction()
    {
        try {
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            $this->view->types_voies = $DB_adresse->getTypesVoieByVille( $this->_request->code_insee );
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur inattendue',
                'message' => $e->getMessage()
            ));
        }
    }

    /**
     * Retourne les voies par rapport à une ville
     *
     */
    public function getVoiesAction()
    {
        try {
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            $this->view->resultats = $DB_adresse->getVoies( $this->_request->code_insee, $this->_request->q );
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur inattendue',
                'message' => $e->getMessage()
            ));
        }
    }

    /**
     * Récupération d'un enregistrement commune
     *
     */
    public function getAction()
    {
        try {
            $model_adresse = new Model_DbTable_AdresseCommune;
            $this->view->resultats = $model_adresse->get($this->_request->q);
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur inattendue',
                'message' => $e->getMessage()
            ));
        }
    }
}
