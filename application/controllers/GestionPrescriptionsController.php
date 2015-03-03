<?php

class GestionPrescriptionsController extends Zend_Controller_Action
{

    public function init()
    {
        // Actions à effectuées en AJAX
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('selectiontexte', 'json')
                        ->addActionContext('selectionarticle', 'json')
                        ->initContext();

        $this->_helper->layout->setLayout('menu_admin');
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $dbPrescriptionCat = new Model_DbTable_PrescriptionCat;
        $listePrescriptionCat = $dbPrescriptionCat->recupPrescriptionCat();

        $this->view->listePrescriptionCat = $listePrescriptionCat;

        //on recupere les prescriptions qui n'ont ni catégories, ni texte, ni article
        $this->showprescriptionTypeAction(0,0,0);
    }

    public function showprescriptionTypeAction($categorie,$texte,$article)
    {
        $dbPrescType = new Model_DbTable_PrescriptionType;
        $listePrescType = $dbPrescType->getPrescriptionType($categorie,$texte,$article);

        $dbPrescAssoc = new Model_DbTable_PrescriptionTypeAssoc;
        $prescriptionArray = array();

        foreach ($listePrescType as $val => $ue) {
            $assoc = $dbPrescAssoc->getPrescriptionAssoc($ue['ID_PRESCRIPTIONTYPE']);
            array_push($prescriptionArray, $assoc);
        }

        $this->view->prescriptionType = $prescriptionArray;
    }

    public function showprescriptiontexteAction()
    {
        if ($this->_getParam('id')) {
            //ici on affiche les textes appartenant à la catégorie dont on passe l'id en param
            $idCategorie = $this->_getParam('id');
            $dbPrescripionTexte = new Model_DbTable_PrescriptionTexte;
            $this->view->idCategorie = $idCategorie;
            $this->view->listePrescriptionTexte = $dbPrescripionTexte->recupPrescriptionTexte($idCategorie);
            $this->showprescriptionTypeAction($this->_getParam('id'),0,0);
        }
    }

    public function showprescriptionarticleAction()
    {
        if ($this->_getParam('idTexte')) {
            $this->view->idTexte = $this->_getParam('idTexte');
            //ici on affiche les textes appartenant à la catégorie dont on passe l'id en param
            $idTexte = $this->_getParam('idTexte');
            $dbPrescripionArticle = new Model_DbTable_PrescriptionArticle;
            $this->view->listePrescriptionArticle = $dbPrescripionArticle->recupPrescriptionArticle($idTexte);

            $dbPrescripionTexte = new Model_DbTable_PrescriptionTexte;
            $idCategorie = $dbPrescripionTexte->find($idTexte)->current()->toArray();
            $idCategorie = $idCategorie['ID_PRESCRIPTIONCAT'];
            $this->showprescriptionTypeAction($idCategorie,$this->_getParam('idTexte'),0);
        }
    }

    public function showarticlecontenuAction()
    {
        //On affiche les prescriptions contenues dans la catégorie d'article selectionnée
        if ($this->_getParam('idArticle')) {
            $dbArticle = new Model_DbTable_PrescriptionArticle;
            $article = $dbArticle->find($this->_getParam('idArticle'))->current();
            $dbTexte = new Model_DbTable_PrescriptionTexte;
            $texte = $dbTexte->find($article['ID_PRESCRIPTIONTEXTE'])->current();
            $this->showprescriptionTypeAction($texte['ID_PRESCRIPTIONCAT'],$article['ID_PRESCRIPTIONTEXTE'],$this->_getParam('idArticle'));
            $this->view->idArticle = $this->_getParam('idArticle');
        }
    }

    public function formcategorieAction()
    {
        if ($this->_getParam('id')) {
            $this->view->idCategorie = $this->_getParam('id');
            $dbCat = new Model_DbTable_PrescriptionCat;
            $this->view->catInfo = $dbCat->find($this->_getParam('id'))->current();
        }
    }

    public function savecategorieAction()
    {
        try {
            $dbCat = new Model_DbTable_PrescriptionCat;
            if ($this->_getParam('idCat')) {
                //Edition
                $this->view->do = 'edit';
                $categorie = $dbCat->find($this->_getParam('idCat'))->current();
            } else {
                //Création
                $this->view->do = 'new';
                $categorie = $dbCat->createRow();
                //On recupere le max num pour inserer la nouvelle catégorie
                $numMax = $dbCat->recupMaxNumCat();
                $numCategorie = $numMax['maxnum'];
                $numCategorie++;
                $categorie->NUM_PRESCRIPTION_CAT = $numCategorie++;
            }
            $categorie->LIBELLE_PRESCRIPTION_CAT = $this->_getParam('LIBELLE_PRESCRIPTION_CAT');
            $categorie->save();
            $this->view->categorie = $categorie;

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'La catégorie a bien été sauvegardée',
                'message' => ''
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la sauvegarde de la catégorie',
                'message' => $e->getMessage()
            ));
        }
    }

    public function formtexteAction()
    {
        if ($this->_getParam('idCat')) {
            //Création d'un nouveau texte
            $this->view->idCategorie = $this->_getParam('idCat');
        } elseif ($this->_getParam('idTexte')) {
            //edition d'un texte existant
            $this->view->idTexte = $this->_getParam('idTexte');
            $dbTexte = new Model_DbTable_PrescriptionTexte;
            $this->view->texteInfo = $dbTexte->find($this->_getParam('idTexte'))->current();
        }
    }

    public function savetexteAction()
    {
        try {
            $dbTexte = new Model_DbTable_PrescriptionTexte;
            if ($this->_getParam('idCategorie')) {
                //Sauvegarde d'un nouveau texte
                $this->view->do = 'new';
                $texte = $dbTexte->createRow();
                //On recupere le num max de la catégorie
                $numMax = $dbTexte->recupMaxNumTexte($this->_getParam('idCategorie'));
                if (!$numMax['maxnum']) {
                    $numTexte = 1;
                } else {
                    $numTexte = $numMax['maxnum'];
                    $numTexte++;
                }
                $texte->NUM_PRESCRIPTIONTEXTE = $numTexte;
                $texte->ID_PRESCRIPTIONCAT = $this->_getParam('idCategorie');
            } elseif ($this->_getParam('idTexte')) {
                //edition d'un texte
                $this->view->do = 'edit';
                $texte = $dbTexte->find($this->_getParam('idTexte'))->current();
            }
            $texte->LIBELLE_PRESCRIPTIONTEXTE = $this->_getParam('LIBELLE_PRESCRIPTIONTEXTE');
            $texte->save();
            $this->view->texte = $texte;

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
    }

    public function formarticleAction()
    {
        if ($this->_getParam('idTexte')) {
            //Création d'un nouveau texte
            $this->view->idTexte = $this->_getParam('idTexte');
        } elseif ($this->_getParam('idArticle')) {
            //edition d'un texte existant
            $this->view->idArticle = $this->_getParam('idArticle');
            $dbArticle = new Model_DbTable_PrescriptionArticle;
            $this->view->articleInfo = $dbArticle->find($this->_getParam('idArticle'))->current();
        }
    }

    public function savearticleAction()
    {
        try {
            $dbArticle = new Model_DbTable_PrescriptionArticle;
            if ($this->_getParam('idTexte')) {
                //Sauvegarde d'un nouveau article
                $this->view->do = 'new';
                $article = $dbArticle->createRow();
                //On recupere le num max de la catégorie
                $numMax = $dbArticle->recupMaxNumArticle($this->_getParam('idTexte'));
                if (!$numMax['maxnum']) {
                    $numArticle = 1;
                } else {
                    $numArticle = $numMax['maxnum'];
                    $numArticle++;
                }
                $article->NUM_PRESCRIPTIONARTICLE = $numArticle;
                $article->ID_PRESCRIPTIONTEXTE = $this->_getParam('idTexte');
            } elseif ($this->_getParam('idArticle')) {
                //edition d'un article
                $this->view->do = 'edit';
                $article = $dbArticle->find($this->_getParam('idArticle'))->current();
            }
            $article->LIBELLE_PRESCRIPTIONARTICLE = $this->_getParam('LIBELLE_PRESCRIPTIONARTICLE');
            $article->save();
            $this->view->article = $article;

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'L\'article a bien été sauvegardé',
                'message' => ''
            ));

        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la sauvegarde de l\'article',
                'message' => $e->getMessage()
            ));
        }
    }

    public function formprescriptionAction()
    {
        if ($this->_getParam('idPrescType')) {
            $this->view->idPrescType = $this->_getParam('idPrescType');
            $this->view->do = 'edit';
            $dbPrescTypeAssoc = new Model_DbTable_PrescriptionTypeAssoc;
            $this->view->assoc = $dbPrescTypeAssoc->getPrescriptionAssoc($this->_getParam('idPrescType'));
        } else {
            $this->view->do = 'new';
            $dbCategorie = new Model_DbTable_PrescriptionCat;
            $this->view->listeCategorie = $dbCategorie->recupPrescriptionCat();
            switch ($this->_getParam('typePresc')) {
                case "addPrescriptionCat":
                    //cas d'une prescription dans une catégorie
                    $this->view->categorie = $this->_getParam('empl');
                break;
                case "addPrescriptionTexte":
                    //cas d'une prescription dans un texte
                    $dbPrescTexte = new Model_DbTable_PrescriptionTexte;
                    $texteInfo = $dbPrescTexte->find($this->_getParam('empl'))->current();
                    $this->view->categorie = $texteInfo->ID_PRESCRIPTIONCAT;
                    $this->view->texte = $this->_getParam('empl');

                break;
                case "addPrescriptionArticle":
                    //cas d'une prescription dans un article
                    $dbPrescArticle = new Model_DbTable_PrescriptionArticle;
                    $articleInfo = $dbPrescArticle->find($this->_getParam('empl'))->current();
                    $this->view->texte = $articleInfo->ID_PRESCRIPTIONTEXTE;

                    $dbPrescTexte = new Model_DbTable_PrescriptionTexte;
                    $texteInfo = $dbPrescTexte->find($this->view->texte)->current();
                    $this->view->categorie = $texteInfo->ID_PRESCRIPTIONCAT;

                    $this->view->article = $this->_getParam('empl');
                break;
                default:

                break;
            }
        }
    }

    public function emplacementAction()
    {
        $this->view->categorie = $this->_getParam('PRESCRIPTIONTYPE_CATEGORIE');
        $this->view->texte = $this->_getParam('PRESCRIPTIONTYPE_TEXTE');
        $this->view->article = $this->_getParam('PRESCRIPTIONTYPE_ARTICLE');


        if (!$this->view->categorie && !$this->view->texte && !$this->view->article) {
            //on affiche les catégories
            $dbPrescriptionCat = new Model_DbTable_PrescriptionCat;
            $listePrescriptionCat = $dbPrescriptionCat->recupPrescriptionCat();
            $this->view->categorieListe = $listePrescriptionCat;
        } elseif (!$this->view->texte && !$this->view->article) {
            $dbPrescriptionCat = new Model_DbTable_PrescriptionCat;
            $categorieLibelle = $dbPrescriptionCat->find($this->view->categorie)->current()->toArray();
            $this->view->categorieLibelle = $categorieLibelle['LIBELLE_PRESCRIPTION_CAT'];
            //on viens de choisir une catégorie il faut afficher les texte de la catégorie
            $dbTexte = new Model_DbTable_PrescriptionTexte;
            $this->view->texteListe = $dbTexte->recupPrescriptionTexte($this->_getParam('PRESCRIPTIONTYPE_CATEGORIE'));

        } elseif (!$this->view->article) {
            $dbPrescriptionCat = new Model_DbTable_PrescriptionCat;
            $categorieLibelle = $dbPrescriptionCat->find($this->view->categorie)->current()->toArray();
            $this->view->categorieLibelle = $categorieLibelle['LIBELLE_PRESCRIPTION_CAT'];
            $dbTexte = new Model_DbTable_PrescriptionTexte;
            $texteLibelle = $dbTexte->find($this->view->texte)->current()->toArray();
            $this->view->texteLibelle = $texteLibelle['LIBELLE_PRESCRIPTIONTEXTE'];
            //on viens de choisir un texte il faut afficher les articles
            $dbArticle = new Model_DbTable_PrescriptionArticle;
            $this->view->texteArticle = $dbArticle->recupPrescriptionArticle($this->_getParam('PRESCRIPTIONTYPE_TEXTE'));

        } else {
            $dbPrescriptionCat = new Model_DbTable_PrescriptionCat;
            $categorieLibelle = $dbPrescriptionCat->find($this->view->categorie)->current()->toArray();
            $this->view->categorieLibelle = $categorieLibelle['LIBELLE_PRESCRIPTION_CAT'];

            $dbTexte = new Model_DbTable_PrescriptionTexte;
            $texteLibelle = $dbTexte->find($this->view->texte)->current()->toArray();
            $this->view->texteLibelle = $texteLibelle['LIBELLE_PRESCRIPTIONTEXTE'];

            $dbArticle = new Model_DbTable_PrescriptionArticle;
            $articleLibelle = $dbArticle->find($this->view->article)->current()->toArray();
            $this->view->articleLibelle = $articleLibelle['LIBELLE_PRESCRIPTIONARTICLE'];
        }
    }

    //Autocomplétion pour selection TEXTE
    public function selectiontexteAction()
    {
        if (isset($_GET['q'])) {
            $DBprescTexte = new Model_DbTable_PrescriptionTexteListe;
            $this->view->selectTexte = $DBprescTexte->fetchAll('LIBELLE_TEXTE LIKE "%'.$_GET['q'].'%"')->toArray();
        }
    }

    //Autocomplétion pour selection ARTICLE
    public function selectionarticleAction()
    {
        if (isset($_GET['q'])) {
            $DBprescArticle = new Model_DbTable_PrescriptionArticleListe;
            $this->view->selectArticle = $DBprescArticle->fetchAll('LIBELLE_ARTICLE LIKE "%'.$_GET['q'].'%"')->toArray();
        }
    }

    public function saveprescriptiontypeAction()
    {
        try {
            $dbPrescType = new Model_DbTable_PrescriptionType;
            $dbTexte = new Model_DbTable_PrescriptionTexteListe;
            $dbArticle = new Model_DbTable_PrescriptionArticleListe;
            $dbPresTypeAssoc = new Model_DbTable_PrescriptionTypeAssoc;
            if ($this->_getParam('ID_PRESCRIPTIONTYPE') != '') {
                //Lorsque l'on édite une prescription type on la supprime ainsi que les assoc (via CASCADE) puis on l'enregistre à nouveau
                $prescToDelete = $dbPrescType->find($this->_getParam('ID_PRESCRIPTIONTYPE'))->current();
                $prescToDelete->delete();
                $this->view->do = 'edit';
            } else {
                $this->view->do = 'new';
            }

            //Lorsque l'on crée une prescription TYPE
            $prescType = $dbPrescType->createRow();
            $prescType->PRESCRIPTIONTYPE_CATEGORIE = $this->_getParam('PRESCRIPTIONTYPE_CATEGORIE');
            $prescType->PRESCRIPTIONTYPE_TEXTE = $this->_getParam('PRESCRIPTIONTYPE_TEXTE');
            $prescType->PRESCRIPTIONTYPE_ARTICLE = $this->_getParam('PRESCRIPTIONTYPE_ARTICLE');
            $prescType->PRESCRIPTIONTYPE_LIBELLE = $this->_getParam('PRESCRIPTIONTYPE_LIBELLE');
            $prescType->save();
            //on recupere l'id de la prescription que l'on vient d'enregistrer
            $idPrescType = $prescType->ID_PRESCRIPTIONTYPE;

            //on s'occupe de verifier les textes et articles pour les inserer ou récuperer l'id si besoin puis on insert dans assoc
            $texteArray = array();
            $articleArray = array();

            foreach ($_POST['article'] as $libelle => $value) {
                array_push($articleArray, $value);
            }

            foreach ($_POST['texte'] as $libelle => $value) {
                array_push($texteArray, $value);
            }


            $numAssoc = 1;

            for ($i = 0; $i < count($articleArray); $i++) {
                //pour chacun des articles et des textes on verifie leurs existance ou non
                if ($articleArray[$i] != '') {
                    $article = $dbArticle->fetchAll('LIBELLE_ARTICLE LIKE "'.$articleArray[$i].'"')->toArray();
                    if (count($article) == 0) {
                        //l'article n'existe pas donc on l'enregistre
                        $article = $dbArticle->createRow();
                        $article->LIBELLE_ARTICLE = $articleArray[$i];
                        $article->save();
                        $idArticle = $article->ID_ARTICLE;
                    } elseif (count($article) == 1) {
                        //l'article existe donc on récupere son ID
                        $idArticle = $article[0]['ID_ARTICLE'];
                    }
                } else {
                    $idArticle = 1;
                }

                if ($texteArray[$i] != '') {
                    $texte = $dbTexte->fetchAll('LIBELLE_TEXTE LIKE "'.$texteArray[$i].'"')->toArray();
                    if (count($texte) == 0) {
                        //le texte n'existe pas donc on l'enregistre
                        $texte = $dbTexte->createRow();
                        $texte->LIBELLE_TEXTE = $texteArray[$i];
                        $texte->save();
                        $idTexte = $texte->ID_TEXTE;
                    } elseif (count($texte) == 1) {
                        //le texte existe donc on récupere son ID
                        $idTexte = $texte[0]['ID_TEXTE'];
                    }
                } else {
                    $idTexte = 1;
                }
                $prescTypeAssoc = $dbPresTypeAssoc->createRow();
                $prescTypeAssoc->ID_PRESCRIPTIONTYPE = $idPrescType;
                $prescTypeAssoc->NUM_PRESCRIPTIONASSOC = $numAssoc;

                $prescTypeAssoc->ID_TEXTE = $idTexte;

                $prescTypeAssoc->ID_ARTICLE = $idArticle;
                $prescTypeAssoc->save();
                $idArticle = NULL;
                $idTexte = NULL;
                $numAssoc++;
            }
            $this->view->idPrescriptionType = $prescType['ID_PRESCRIPTIONTYPE'];
            $this->view->textes = $texteArray;
            $this->view->articles = $articleArray;
            $this->view->libelle = $this->_getParam('PRESCRIPTIONTYPE_LIBELLE');

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'La prescription a bien été sauvegardée',
                'message' => ''
            ));

        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la sauvegarde de la prescription',
                'message' => $e->getMessage()
            ));
        }
    }


/* GESTION DES TEXTES */
	
    public function gestionTextesAction(){
        $this->_helper->layout->setLayout('menu_admin');
        $service_prescTextes = new Service_Prescriptions();
        if ( $this->_request->isPost() ) {
            try {
                $post = $this->_request->getPost();
                if($post['action'] == 'add'){
                    $service_prescTextes->saveTexte($post);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Enregistrement effectué.', 'message' => 'La texte a bien été enregistré'));
                }else if($post['action'] == 'edit'){
                    $service_prescTextes->saveTexte($post,$post['id_texte']);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Modification effectuée.', 'message' => 'La texte a bien été enregistré'));
                }else if($post['action'] == 'replace'){
                    $service_prescTextes->replaceTexte($post['id_texte'],$post['idTexteReplace']);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression effectuée.', 'message' => 'Le texte a bien été supprimé'));
                }

            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Erreur lors de l\'enregistrement.', 'message' => 'Une erreur s\'est produite lors de l\enregistrement de la prescription ('.$e->getMessage().')'));
            }
        }

        $liste_textes = $service_prescTextes->getTextesListe();

        $this->view->liste_textes = $liste_textes;
        //Zend_Debug::dump($liste_textes);
    }

    public function gestionTextesAddAction(){
        $this->_helper->layout->setLayout('menu_admin');
        $this->_helper->viewRenderer->setNoRender();
        
        $this->view->action = 'add';

        $this->render('gestion-textes-edit');
    }

    public function gestionTextesEditAction(){
        $this->_helper->layout->setLayout('menu_admin');
        
        $this->view->action = 'edit';
        
        $service_prescTextes = new Service_Prescriptions();
        $texteInfo = $service_prescTextes->getTexte($this->_getParam('id'));
        $this->view->texteInfo = $texteInfo;
    }

    public function gestionTextesReplaceAction(){
        $this->_helper->layout->setLayout('menu_admin');

        $this->view->action = 'replace';

        $service_prescTextes = new Service_Prescriptions();
        $texteInfo = $service_prescTextes->getTexte($this->_getParam('id'));
        $this->view->texteInfo = $texteInfo;

        $liste_textes = $service_prescTextes->getTextesListe($this->_getParam('id'));
        $this->view->liste_textes = $liste_textes;
    }


/* GESTION DES ARTICLES */

    public function gestionArticlesAction(){
        $this->_helper->layout->setLayout('menu_admin');
        //1 On affiche tous les textes accessible dans les prescriptions
        $service_prescription = new Service_Prescriptions();


        if ( $this->_request->isPost() ) {
            try {
                $post = $this->_request->getPost();
                if($post['action'] == 'add'){
                    $service_prescription->saveArticle($post);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Enregistrement effectué.', 'message' => 'L\'article a bien été enregistré'));
                }else if($post['action'] == 'edit'){
                    $service_prescription->saveArticle($post,$post['id_article']);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Modification effectuée.', 'message' => 'L\'article a bien été enregistré'));
                }else if($post['action'] == 'replace'){
                    $service_prescription->replaceArticle($post['id_article'],$post['idArticleReplace']);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression effectuée.', 'message' => 'L\'article a bien été supprimé'));
                }

            } catch (Exception $e) {
                //$this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Erreur lors de l\'enregistrement.', 'message' => 'Une erreur s\'est produite lors de l\enregistrement de la prescription ('.$e->getMessage().')'));
            }
        }

        $liste_articles = $service_prescription->getArticlesListe();

        $this->view->liste_articles = $liste_articles;
        //Zend_Debug::dump($liste_textes);
    }

    public function gestionArticlesAddAction(){
        $this->_helper->layout->setLayout('menu_admin');
        $this->_helper->viewRenderer->setNoRender();
        
        $this->view->action = 'add';

        $this->render('gestion-articles-edit');
    }

    public function gestionArticlesEditAction(){
        $this->_helper->layout->setLayout('menu_admin');
        
        $this->view->action = 'edit';
        
        $service_prescription = new Service_Prescriptions();
        $articleInfo = $service_prescription->getArticle($this->_getParam('id'));
        $this->view->articleInfo = $articleInfo;
    }

    public function gestionArticlesReplaceAction(){
        $this->_helper->layout->setLayout('menu_admin');

        $this->view->action = 'replace';

        $service_prescription = new Service_Prescriptions();
        $articleInfo = $service_prescription->getArticle($this->_getParam('id'));
        $this->view->articleInfo = $articleInfo;

        $liste_articles = $service_prescription->getArticlesListe();
        $this->view->liste_articles = $liste_articles;
    }

/* GESTION DES RAPPELS REGLEMENTAIRES */

    public function gestionRappelRegAction(){
        $service_prescription = new Service_Prescriptions();
        
        if ($this->_request->isPost()) {
                $post = $this->_request->getPost();

                if($post['action'] == 'add'){
                    $service_prescription->savePrescription($post);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Enregistrement effectué.', 'message' => 'Le rappel réglementaire a bien été enregistré'));
                }else if($post['action'] == 'edit'){
                    $service_prescription->savePrescription($post,$post['idPrescription']);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Rappel réglementaire modifié.', 'message' => 'Le rappel réglementaire a bien été modifié'));
                }
        }

        $this->view->listePrescEtude = $service_prescription->getPrescriptions('etude');
        $this->view->listePrescVisite = $service_prescription->getPrescriptions('visite');        
    }

    public function gestionRappelRegAddAction(){
        $this->_forward('prescription-form');

        //On envoi à la vue l'ensemble des textes et articles
        $dbTexte = new Model_DbTable_PrescriptionTexteListe();
        $this->view->listeTextes = $dbTexte->getAllTextes();
        $dbArticle = new Model_DbTable_PrescriptionArticleListe();
        $this->view->listeArticles = $dbArticle->getAllArticles();

        $this->view->action = 'add';
        $this->view->typeAction = 'rappel-reg';
    }

    public function gestionRappelRegEditAction(){
        $this->_forward('prescription-form');

        $dbTexte = new Model_DbTable_PrescriptionTexteListe();
        $dbArticle = new Model_DbTable_PrescriptionArticleListe();

        $this->view->listeTextes = $dbTexte->getAllTextes();
        $this->view->listeArticles = $dbArticle->getAllArticles();

        
        $idPrescription = $this->_getParam('id');
        $typeAction = 'rappel-reg';

        $service_prescription = new Service_Prescriptions();        
        $prescriptionInfo = $service_prescription->getPrescriptionInfo($this->_getParam('id'),$typeAction);

        $this->view->infosPrescription = $prescriptionInfo;
        $this->view->idPrescription = $idPrescription;
        $this->view->action = 'edit';
        $this->view->typeAction = $typeAction;
        $this->view->libelle = $prescriptionInfo[0]["PRESCRIPTIONREGL_LIBELLE"];
    }
/* FORMULAIRE DE PRESCRIPTIONS */
    
    public function prescriptionFormAction(){
        $this->_helper->layout->setLayout('menu_admin');
    }
}
