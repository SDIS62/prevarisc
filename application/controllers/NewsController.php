<?php

    class NewsController extends Zend_Controller_Action
    {
        // Initialisation
        public function init()
        {
            // Gestionnaire de contexte
            $this->_helper->AjaxContext->addActionContext('index', 'json')
                                        ->addActionContext('get', 'json')
                                       ->addActionContext('add', 'json')
                                        ->addActionContext('next', 'json')
                                       ->initContext();
        }

        // Affichage des news
        public function indexAction()
        {
            // Récupération du fil d'actualité
            $DB_messages = new Model_DbTable_News;
            $user = Zend_Auth::getInstance()->getIdentity();
            $this->view->flux = $DB_messages->getNews($user->ID_GROUPE);

            $DB_groupe = new Model_DbTable_Groupe;
            $this->view->groupes = $DB_groupe->fetchAll()->toArray();
        }

        public function nextAction()
        {
            // Mod�le du fild 'actualit�
            $fil_model = new Model_DbTable_News;

            // R�cup�ration du fil d'actualit�
            $user = Zend_Auth::getInstance()->getIdentity();
            $flux = $fil_model->getNews($user->ID_GROUPE, $this->_request->page );
            $this->view->html = $this->view->partialLoop('news/display.phtml', $flux);
        }

        public function getAction()
        {
            // Mod�le du fild 'actualit�
            $fil_model = new Model_DbTable_News;

            // R�cup�ration du fil d'actualit�
            $user = Zend_Auth::getInstance()->getIdentity();
            $flux = $fil_model->getNews($user->ID_GROUPE, null, $this->_request->timestamp );
            $this->view->html = $this->view->partialLoop('news/display.phtml', $flux);
            $this->view->int_timestamp = time() + 1;
            $this->view->int_count = count($flux);
        }

        public function addAction()
        {
            // On ajoute la news dans la db
            $model = new Model_DbTable_News;
            $model->add($this->_request->type, $this->_request->text, $_GET["conf"] );

            // Timestamp actuel
            $this->view->int_timestamp = time();
        }

        public function deleteAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();

            // On supprime la news dans la db
            $model = new Model_DbTable_News;
            $news = $model->find($this->_request->id)->current();
        }
    }
