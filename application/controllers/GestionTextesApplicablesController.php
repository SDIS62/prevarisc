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
				$rowEdit['NUM_TEXTESAPPL'] = "99999";
                $rowEdit->save();
            } else {
                //cas d'une création
                $newRow = $dbTextesAppl->createRow();
                $newRow['LIBELLE_TEXTESAPPL'] = $this->_getParam("libelle");
                $newRow['VISIBLE_TEXTESAPPL'] = $this->_getParam("visible");
                $newRow['ID_TYPETEXTEAPPL'] = $this->_getParam("type");
				$newRow['NUM_TEXTESAPPL'] = "99999";
                $newRow->save();
            }

            if($this->_getParam("defPrescription") == "yes"){
                //on enregistre le texte dans la table prescriptiontexteliste
                $dbPrescTextes = new Model_DbTable_PrescriptionTexteListe;
                $newTexte = $dbPrescTextes->createRow();
                $newTexte->LIBELLE_TEXTE = $this->_getParam("libelle");
                $newTexte->save();
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

	public function updateorderAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$tabId = explode(",",$this->_getParam("tableUpdate"));
		$dbTexteAppl = new Model_DbTable_TextesAppl;
		$num = 0;
		foreach($tabId as $id){
			$updateTexteAppl = $dbTexteAppl->find($id)->current();
            $updateTexteAppl->NUM_TEXTESAPPL = $num;
            $updateTexteAppl->save();
			$num++;
		}
	}
}
