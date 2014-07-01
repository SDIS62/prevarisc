<?php

class Plugin_View extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if($request->getModuleName() == 'default')
        {
            // On récupère la vue
            $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

            // Chargement de la navigation par défaut
            $view->navigation(new Zend_Navigation(new Zend_Config_Xml(APPLICATION_PATH . DS . 'navigation.xml', 'nav')));

            // On définie le titre de l'application
            $view->headTitle(strip_tags($view->navigation()->breadcrumbs()->setMinDepth(0)->setSeparator(" / ")));

            // Liens vers les fichiers combinés CSS / JS
            $view->inlineScript()->appendFile("/js/application.combined.js")->appendFile("/js/jquery.dateentry.js");
            $view->headLink()->appendStylesheet('/css/application.combined.css', 'all');

            // Envoi de la version en cours sur la vue
            $view->version_prevarisc = '1.2.1';

            // Icône du site
            $view->headLink()->headLink(array("rel" => "shortcut icon","href" => "/images/favicon.ico"));

            // Chargement des aides de vue
            $view->registerHelper(new View_Helper_AfficheDoc, 'afficheDoc');
            $view->registerHelper(new View_Helper_AgendaMois, 'agendaMois');
            $view->registerHelper(new View_Helper_Avatar, 'avatar');
            $view->registerHelper(new View_Helper_Carte, 'carte');
            $view->registerHelper(new View_Helper_DateJqueryToBd, 'dateJqueryToBd');
            $view->registerHelper(new View_Helper_ListeGroupement, 'listeGroupement');
            $view->registerHelper(new SDIS62_View_Helper_FlashMessenger, 'flashMessenger');
            
            // Définition du partial de vue à utiliser pour le rendu d'une recherche
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('search' . DIRECTORY_SEPARATOR . 'pagination_control.phtml');

            // On charge la vue correctement configurée dans le viewRenderer
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setView($view);
        }
    }
}
