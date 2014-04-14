<?php

class GestionTextesApplicablesController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        //on commence par afficher tous les texte applicables regroupés par leurs type
        $dbTextesAppl = new Model_DbTable_TextesAppl;
        $this->view->listeTextesAppl = $dbTextesAppl->recupTextesAppl();

    }

    public function formtexteapplAction()
    {
        //Cas d'une création d'un texte
        $dbTypeTextesAppl = new Model_DbTable_TypeTextesAppl;
        $this->view->listeType = $dbTypeTextesAppl->getType();
        if ($this->_getParam("id")) {
            $this->view->idTexteAppl = $this->_getParam("id");
            $dbTextesAppl = new Model_DbTable_TextesAppl;
            $this->view->texteEdit = $dbTextesAppl->find($this->_getParam("id"));
        }
    }

    public function saveAction()
    {
        try {
            //sauvegarde d'un nouveau texte ou mise à jour d'un texte existant
            $dbTextesAppl = new Model_DbTable_TextesAppl;
            if ($this->_getParam("idTexteAppl")) {
                //cas d'une édition
                $rowEdit = $dbTextesAppl->find($this->_getParam("idTexteAppl"))->current();
                $rowEdit['LIBELLE_TEXTESAPPL'] = $this->_getParam("libelle");
                $rowEdit['VISIBLE_TEXTESAPPL'] = $this->_getParam("visible");
                $rowEdit['ID_TYPETEXTEAPPL'] = $this->_getParam("type");
                $rowEdit->save();
            } else {
                //cas d'une création
                $newRow = $dbTextesAppl->createRow();
                $newRow['LIBELLE_TEXTESAPPL'] = $this->_getParam("libelle");
                $newRow['VISIBLE_TEXTESAPPL'] = $this->_getParam("visible");
                $newRow['ID_TYPETEXTEAPPL'] = $this->_getParam("type");
                $newRow->save();
            }

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Le texte a bien été sauvegardé',
                'message' => ''
            ));

        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la sauvegarde du texte',
                'message' => $e->getMessage()
            ));
        }

        // Redirection
        $this->_helper->redirector('index');

    }
}
