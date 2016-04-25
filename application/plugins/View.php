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

            // Envoi de la version en cours sur la vue
            if (getenv('PREVARISC_BRANCH') != false) {
                $view->branch_prevarisc = getenv('PREVARISC_BRANCH');
                $view->revision_prevarisc = getenv('PREVARISC_REVISION');
                $view->version_prevarisc = getenv('PREVARISC_BRANCH').'.'.getenv('PREVARISC_REVISION');
            } else {
                $git = new SebastianBergmann\Git\Git(APPLICATION_PATH . DS . '..');
                $view->branch_prevarisc = $git->getCurrentBranch();
                $revisions = $git->getRevisions();
                $last_revision = end($revisions);
                $view->revision_prevarisc = $last_revision['sha1'];
                $view->version_prevarisc = $view->branch_prevarisc . '@' . substr((string) $view->revision_prevarisc, 0, 7);
            }

            // Chargement des aides de vue
            $view->registerHelper(new View_Helper_MinifyHeadLink($view->version_prevarisc), 'headLink');
            $view->registerHelper(new View_Helper_MinifyInlineScript($view->version_prevarisc), 'inlineScript');
            $view->registerHelper(new SDIS62_View_Helper_FlashMessenger, 'flashMessenger');
            $view->registerHelper(new View_Helper_AfficheDoc, 'afficheDoc');
            $view->registerHelper(new View_Helper_AgendaMois, 'agendaMois');
            $view->registerHelper(new View_Helper_Dates, 'formatDateDiff');
            $view->registerHelper(new View_Helper_Avatar, 'avatar');
            $view->registerHelper(new View_Helper_Carte, 'carte');
            $view->registerHelper(new View_Helper_ListeGroupement, 'listeGroupement');
            $view->registerHelper(new SDIS62_View_Helper_FlashMessenger, 'flashMessenger');

            // JS
            $view->inlineScript()->appendFile("/js/jquery-1.10.2.min.js");
            $view->inlineScript()->appendFile("/js/jquery-migrate-1.2.1.min.js");
            $view->inlineScript()->appendFile("/js/jquery-ui.min.js");
            $view->inlineScript()->appendFile("/js/jquery.fullcalendar.js");
            $view->inlineScript()->appendFile("/js/jquery.autocomplete.min.js");
            $view->inlineScript()->appendFile("/js/jquery.timeentry.js");
            $view->inlineScript()->appendFile("/js/jquery.elastic.js");
            $view->inlineScript()->appendFile("/js/jquery.toggletext.js");
            $view->inlineScript()->appendFile("/js/jquery.multiselect.min.js");
            $view->inlineScript()->appendFile("/js/jquery.tablesorter.js");
            $view->inlineScript()->appendFile("/js/jquery.tablesorter.pager.js");
            $view->inlineScript()->appendFile("/js/jquery.tipsy.js");
            $view->inlineScript()->appendFile("/js/jquery.fancybox-1.3.4.js");
            $view->inlineScript()->appendFile("/js/bootstrap.min.js");
            $view->inlineScript()->appendFile("/js/dropzone.min.js");
            $view->inlineScript()->appendFile("/js/chosen.jquery.min.js");
            $view->inlineScript()->appendFile("/js/jquery.dateentry.js");
            $view->inlineScript()->appendFile("/js/jquery-ui.datepicker.fr.js");
            $view->inlineScript()->appendFile("/js/jquery.marquee.min.js");
            $view->inlineScript()->appendFile("/js/jquery.hoverintent.js");
            $view->inlineScript()->appendFile("/js/main.js");

            // CSS
            $view->headLink()->appendStylesheet('/css/bootstrap.min.css', 'all');
            $view->headLink()->appendStylesheet('/css/main.css', 'all');
            $view->headLink()->appendStylesheet('/css/login.css', 'all');
            $view->headLink()->appendStylesheet('/css/components/panel.css', 'all');
            $view->headLink()->appendStylesheet('/css/chosen.min.css', 'all');
            $view->headLink()->appendStylesheet('/css/jquery/jquery-ui-1.8.11.custom.css', 'all');
            $view->headLink()->appendStylesheet('/css/jquery/jquery.tablesorter.css', 'all');
            $view->headLink()->appendStylesheet('/css/jquery/jquery.multiselect.css', 'all');
            $view->headLink()->appendStylesheet('/css/jquery/jquery.fullcalendar.css', 'all');
            $view->headLink()->appendStylesheet('/css/jquery/jquery.fancybox-1.3.4.css', 'all');
            $view->headLink()->appendStylesheet('/css/jquery/jquery.tipsy.css', 'all');
            $view->headLink()->appendStylesheet('/css/dropzone/basic.css', 'all');
            $view->headLink()->appendStylesheet('/css/dropzone/basic.css', 'all');
            $view->headLink()->appendStylesheet('/css/dropzone/dropzone.css', 'all');

            // Définition du partial de vue à utiliser pour le rendu d'une recherche
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('search' . DIRECTORY_SEPARATOR . 'pagination_control.phtml');

            // On charge la vue correctement configurée dans le viewRenderer
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setView($view);
        }
    }
}
