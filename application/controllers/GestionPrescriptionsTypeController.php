<?php

class GestionPrescriptionsTypeController extends Zend_Controller_Action
{
 
    public function init()
    {
            
    }

    public function indexAction()
    {

        $dbpresctype = new Model_DbTable_PrescriptionType;
        $dbtexte = new Model_DbTable_PrescriptionTexte;
        $dbarticle = new Model_DbTable_PrescriptionArticle;
		
		//Zend_Debug::dump($dbpresctype->getPrescType());
        $this->view->listePrescription = $dbpresctype->getPrescType();

        $compteur = 1;

        //Arrays contenant toutes les infos sur les différentes prescriptions qui seront envoyées à la vue
        $Textes = array();
        $Articles = array();
        $PrescriptionsLibelle = array();

        //Array temporaire d'une prescription qui récolte les infos de chaque description 1 par 1 puis vient s'inclure aux arrays décrits au dessus
        //On initialise pour le premier
        $listeTextePresc = array();
        $listeArticlePresc = array();
		//Zend_Debug::dump($this->view->listePrescription);
        foreach ($this->view->listePrescription as $indPresc => $val) {
            //On liste les prescriptions
            $numPrescription = $val['ID_PRESCRIPTIONTYPE'];

            //la prescription se trouve dans prescription TYPE
            //$infosPrescription = $dblistepresc->getPrescriptionType($val['PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER']);

            //Zend_Debug::dump($infosPrescription);

            if ($compteur != $numPrescription) {
                //On réinitialise les tableau après les avoir ajoutés au tableau contenant toute les infos
                array_push($Textes, $listeTextePresc);
                array_push($Articles, $listeArticlePresc);
                $compteur ++;
                $listeTextePresc = array();
                $listeArticlePresc = array();
            }

            $tabTexte = explode("_",$val['TEXTE_PRESCRIPTIONTYPE']);

            foreach ($tabTexte as $indText => $valText) {
                //secho $valText."<br/>";
                $texte = $dbtexte->find($valText)->current();
                //echo $texte['LIBELLE_TEXTE'];
                array_push($listeTextePresc, $texte['LIBELLE_TEXTE'] );
            }

            $tabArticle = explode("_",$val['ARTICLE_PRESCRIPTIONTYPE']);

            foreach ($tabArticle as $indArticle => $valArticle) {
                //echo $valArticle."<br/>";
                $article = $dbarticle->find($valArticle)->current();
                //echo $article['LIBELLE_ARTICLE'];
                array_push($listeArticlePresc, $article['LIBELLE_ARTICLE'] );
            }
            array_push($PrescriptionsLibelle, $val['LIBELLE_PRESCRIPTIONLIBELLE']);
        }
        array_push($Textes, $listeTextePresc);
        array_push($Articles, $listeArticlePresc);
		/*
		unset($Textes[0]);
		unset($Articles[0]);
		*/
        $this->view->nbPrescription = count($this->view->listePrescription);
        $this->view->listeTextes = $Textes;
        //Zend_Debug::dump($this->view->listeTextes);
        $this->view->listeArticles = $Articles;
        //Zend_Debug::dump($this->view->listeArticles);
        $this->view->ListePrescriptionsLibelle = $PrescriptionsLibelle;
        //Zend_Debug::dump($this->view->ListePrescriptionsLibelle);

        $this->showprescriptionAction($Textes,$Articles,$PrescriptionsLibelle," ","loadPrescriptionAjax");

    }
	
	public function showprescriptionAction($tabTexte,$tabArticle,$tabLibelle,$idPrescDossier,$type)
    {
        $this->view->listeTextes = $tabTexte;
        $this->view->listeArticles = $tabArticle;
        $this->view->listeLibelles = $tabLibelle;
        $this->view->idPrescDossier = $idPrescDossier;
        $this->view->type = $type;

        $this->render('showprescription');
    }
	
	public function formpresctypeAction()
    {
        //echo $this->_getParam('id');
		//$this->_helper->viewRenderer->setNoRender();
        //echo $this->_getParam('idDossier')." - ".$this->_getParam('idPrescription');
        $idPrescription = (int) $this->_getParam('id');
		
		$this->view->idPrescType = $idPrescription;

        $dbPrescDossier = new Model_DbTable_PrescriptionType;
		$dbtexte = new Model_DbTable_PrescriptionTexte;
        $dbarticle = new Model_DbTable_PrescriptionArticle;
        //On récupere les infos de la prescriptions permettant de savoir s'il sagit d'une prescription type ou non
        //$prescription = $dbPrescDossier->find($idPrescription)->current()->toArray();

        // Zend_Debug::dump($prescription);
        //Récupération des infos concernant l'association texte/article/libelle
        $infosPrescription = $dbPrescDossier->getPrescTypeInfo($idPrescription);
		//Zend_Debug::dump($prescription);
        //$this->view->prescInfo = json_encode($infosPrescription);
        $this->view->prescInfo = $infosPrescription;
		
		$textesId = $infosPrescription['TEXTE_PRESCRIPTIONTYPE'];
		$articlesId = $infosPrescription['ARTICLE_PRESCRIPTIONTYPE'];
		
		
		$tabTextesId = explode('_',$textesId);
		//Zend_Debug::dump($tabTextesId);
		
		
		$tabArticlesId = explode('_',$articlesId);
		//Zend_Debug::dump($tabArticlesId);
		
		$listeTextePresc = array();
		foreach ($tabTextesId as $indText => $valText) {
			//echo $valText."<br/>";
			$texte = $dbtexte->find($valText)->current();
			//echo $texte['LIBELLE_TEXTE'];
			array_push($listeTextePresc, $texte['LIBELLE_TEXTE'] );
		}
		
		$listeArticlePresc = array();
		foreach ($tabArticlesId as $indArticle => $valArticle) {
			$article = $dbarticle->find($valArticle)->current();
			//echo $article['LIBELLE_ARTICLE'];
			array_push($listeArticlePresc, $article['LIBELLE_ARTICLE'] );
		}
		
		$this->view->textes = $listeTextePresc;
		$this->view->articles = $listeArticlePresc;

		//Zend_Debug::dump($listeTextePresc);
		//Zend_Debug::dump($listeArticlePresc);
		
    }

	public function saveAction()
    {
		$this->_helper->viewRenderer->setNoRender();
        //echo $this->_getParam('idDossier')." - ".$this->_getParam('idPrescription');
        $idPrescription = (int) $this->_getParam('idPrescription');
		
		$dbpresctype = new Model_DbTable_PrescriptionType;
		$dblibelle = new Model_DbTable_PrescriptionLibelle;
        $DBtexte = new Model_DbTable_PrescriptionTexte;
        $DBarticle = new Model_DbTable_PrescriptionArticle;
		
		//On récupere la prescription type à modifier
		$prescToEdit = $dbpresctype->find($this->_getParam('idPrescType'))->current();
		
		//On edite le libelle de la prescription
		$libelleToEdit = $dblibelle->find($prescToEdit['LIBELLE_PRESCRIPTIONTYPE'])->current();
		$libelleToEdit->LIBELLE_PRESCRIPTIONLIBELLE = $this->_getParam('PRESCRIPTIONLIBELLE');
		$libelleToEdit->save();
		
		$listeTexte = "";
        $listeArticle = "";
		$texteArray = array();
        $articleArray = array();
		//On boucle sur les hidden POST -> text et on vérifie aussi les articles
		foreach ($_POST['texte'] as $libelle => $valueTexte) {
			if(isset($valueTexte)){
				if ($valueTexte == "") {
					//si vide
					$listeTexte .= "_";
					$valueTexte = '';
				}else if(is_numeric($valueTexte)) {
					//si il s'agit d'un id
					$listeTexte .= $valueTexte."_";
					$libelleTexte = $DBtexte->find($valueTexte)->current();
					$valueTexte = $libelleTexte->LIBELLE_TEXTE;
				} else {
					//Si c'est un nouveau -> inserer texte et récup id
					$idExistant = $DBtexte->verifTexteExiste($valueTexte);
					$idExistant = $idExistant['ID_TEXTE'];
					if ($idExistant == false) {
						$newTexte = $DBtexte->createRow();
						$newTexte->LIBELLE_TEXTE = $valueTexte;
						$newTexte->save();
						$listeTexte .= $newTexte->ID_TEXTE."_";
					} else {
						$listeTexte .= $idExistant."_";
					}
				}
			}

			if ($_POST['article'][$libelle] == "") {
				//si vide
				$listeArticle .= "_";
				$valueArticle = '';
			} elseif (is_numeric($_POST['article'][$libelle])) {
				//si il s'agit d'un id
				$listeArticle .= $_POST['article'][$libelle]."_";
				$libelleArticle = $DBarticle->find($_POST['article'][$libelle])->current();
				$valueArticle = $libelleArticle->LIBELLE_ARTICLE;
			} else {
				//Si c'est un nouveau -> inserer texte et récup id
				$idExistant = $DBarticle->verifArticleExiste($_POST['article'][$libelle]);
				$idExistant = $idExistant['ID_ARTICLE'];
				if ($idExistant == false) {
					$newArticle = $DBarticle->createRow();
					$newArticle->LIBELLE_ARTICLE = $_POST['article'][$libelle];
					$valueArticle = $_POST['article'][$libelle];
					$newArticle->save();
					$listeArticle .= $newArticle->ID_ARTICLE."_";
				} else {
					$valueArticle = $_POST['article'][$libelle];
					$listeArticle .= $idExistant."_";
				}
			}

			array_push($texteArray, $valueTexte);
			array_push($articleArray, $valueArticle);
		}
		
		$newPrescType = $dbpresctype->find($this->_getParam('idPrescType'))->current();
		$newPrescType->TEXTE_PRESCRIPTIONTYPE = $listeTexte;
		$newPrescType->ARTICLE_PRESCRIPTIONTYPE = $listeArticle;
		$newPrescType->ABREVIATION_PRESCRIPTIONTYPE = $this->_getParam('ABREVIATION');
		$newPrescType->save();
		//Zend_Debug::dump($texteArray);
		
		
//		Zend_Debug::dump($libelleToEdit->toArray());
		
	/*
        $dbPrescDossier = new Model_DbTable_PrescriptionDossier;
        //On récupere les infos de la prescriptions permettant de savoir s'il sagit d'une prescription type ou non
        $prescription = $dbPrescDossier->find($idPrescription)->current()->toArray();

        //$$infosPrescription = $dbPrescDossier->getPrescriptionType($prescription["PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER"]);
      */  
	}
}
