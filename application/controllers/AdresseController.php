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

    public function buildAction() 
    {
        $this->_helper->layout->disableLayout();
    }

    /**
     * Donne la liste de ville par rapport à un code postal
     *
     */
    public function getVilleParCodePostalAction()
    {
        $DB_adresse = new Model_DbTable_EtablissementAdresse;
        $this->view->villes = $DB_adresse->getVilleByCP( $this->_request->code_postal );
    }

    /**
     * Retourne les types de voie d'une commune
     *
     */
    public function getTypesVoieParVilleAction()
    {
        $DB_adresse = new Model_DbTable_EtablissementAdresse;
        $this->view->types_voies = $DB_adresse->getTypesVoieByVille( $this->_request->code_insee );
    }

    /**
     * Retourne les voies par rapport à une ville
     *
     */
    public function getVoiesAction()
    {
        $DB_adresse = new Model_DbTable_EtablissementAdresse;
        $this->view->resultats = $DB_adresse->getVoies( $this->_request->code_insee, $this->_request->q );
    }

    /**
     * Récupération d'un enregistrement commune
     *
     */
    public function getAction()
    {
        if(strlen($this->_request->q) == 5 && is_numeric($this->_request->q)) {
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            $this->view->resultats = $DB_adresse->getVilleByCP( $this->_request->q );
        }
        else {
            $model_adresse = new Model_DbTable_AdresseCommune;
            $this->view->resultats = $model_adresse->get($this->_request->q);
        }
    }
}
