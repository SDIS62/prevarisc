<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    private $version = "1.2.1";

    protected function _initAutoload()
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));

        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_GlobalAction);
        
        Zend_Controller_Action_HelperBroker::addPath(
			APPLICATION_PATH . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . "helpers",
			"Application_Controller_Helper_"
		);

        return $moduleLoader;
    }

    protected function _initMyView()
    {
        // Instance de la vue
        $this->bootstrap('view');
        $view = $this->getResource('view');

        // Titre
        $view->headTitle('Prevarisc')
             ->setSeparator(' - ');

        // Balises META
        $view->headMeta()
            ->appendName('viewport', 'width=device-width,initial-scale=1')
            ->appendHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1')
            ->appendName('description', "Logiciel de gestion du service Prévention")
            ->appendName('author', 'SDIS 62');

        // Liens vers le CSS
        $view->headLink()	->appendStylesheet('/css/reset.css')
                            ->appendStylesheet('/css/text.css')
                            ->appendStylesheet('/css/form.css')
                            ->appendStylesheet('/css/grid_fluid.css')
                            ->appendStylesheet('/css/sprite.css')
                            ->appendStylesheet('/css/main.css')
                            ->appendStylesheet('/css/jquery/jquery-ui-1.8.11.custom.css')
                            ->appendStylesheet('/css/jquery/jquery.tablesorter.css')
                            ->appendStylesheet('/css/jquery/jquery.multiselect.css')
                            ->appendStylesheet('/css/jquery/jquery.fullcalendar.css')
                            ->appendStylesheet('/css/jquery/jquery.fancybox-1.3.4.css')
                            ->appendStylesheet('/css/jquery/jquery.tipsy.css')
                            ->headLink(array('rel' => 'shortcut icon', 'href' => '/images/favicon.ico', 'type' => 'image/x-icon'), 'PREPEND')
                            ->headLink(array('rel' => 'icon', 'href' => '/images/favicon_32.png', 'sizes' => '32x32'));

        // Lien vers les scripts Javascript
        $view->inlineScript()	->appendFile('/js/jquery.min.js')
                                ->appendFile('/js/jquery-ui.min.js')
                                ->appendFile('/js/jquery.fullcalendar.js')
                                ->appendFile('/js/jquery.autocomplete.min.js')
                                ->appendFile('/js/jquery.timeentry.js')
                                ->appendFile('/js/jquery.dateentry.js')
                                ->appendFile('/js/jquery.elastic.js')
                                ->appendFile('/js/jquery.toggletext.js')
                                ->appendFile('/js/jquery.multiselect.min.js')
                                ->appendFile('/js/jquery.tablesorter.js')
                                ->appendFile('/js/jquery.tablesorter.pager.js')
                                ->appendFile('/js/jquery.tipsy.js')
                                ->appendFile('/js/jquery.fancybox-1.3.4.js');
                                
        // Envoi de l'identité sur la vue
        if(Zend_Auth::getInstance()->hasIdentity())
        {
            $identity = Zend_Auth::getInstance()->getIdentity();
            $view->auth_user = $identity;
        }
        
        // Envoi de la version en cours sur la vue
        $view->version_prevarisc = $this->version;
    }
}
