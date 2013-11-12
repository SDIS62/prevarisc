<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function run()
    {
        // Configuration de l'auto-loader
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));

        // Chargement et activation des plugins
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_ACL);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_XmlHttpRequest);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_View);
        
        // Ajout des aides d'action
        Zend_Controller_Action_HelperBroker::addPath(
			APPLICATION_PATH . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . "helpers",
			"Application_Controller_Helper_"
		);

        return parent::run();
    }
    
    public function _initView()
    {
        $view = new Zend_View();
        
        $view->headMeta()
            ->appendName('viewport', 'width=device-width,initial-scale=1')
            ->appendHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1')
            ->appendName('description', 'Logiciel de gestion du service Prévention')
            ->appendName('author', 'SDIS62 - Service Recherche et Développement');
            
        return $view;
    }
    
    protected function _initSession()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . DS . 'configs' . DS . 'application.ini', APPLICATION_ENV);
        Zend_Session::setOptions($config->resources->session->toArray());
    }
}
