<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @inheritdoc
     */    
    public function run()
    {
        // Chargement et activation des plugins
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_View);
        //Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_ACL);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_XmlHttpRequest);
        
        // Ajout des aides d'action
        Zend_Controller_Action_HelperBroker::addPath(
			APPLICATION_PATH . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . "helpers",
			"Application_Controller_Helper_"
		);

        return parent::run();
    }
    
    /**
     * Initialisation du cache APC
     */
    protected function _initCache()
    {
        // Import des paramètres de connexion à la base de données
        $config_cache = $this->getOption('cache');
            
        return Zend_Cache::factory('Core', 'APC', array(
            'lifetime' => $config_cache['lifetime'],
            'cache_id_prefix' => 'prevarisc'
        )); 
    }
    
    /**
     * Initialisation de l'auto-loader
     */
    protected function _initAutoLoader()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'basePath'    => APPLICATION_PATH,
            'namespace'  => '',
        ));
        
        return $autoloader;
    }
    
    /**
     * Initialisation de la vue
     */   
    protected function _initView()
    {
        $view = new Zend_View();

        $view->headMeta()
            ->appendName('viewport', 'width=device-width,initial-scale=1')
            ->appendHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1')
            ->appendName('description', 'Logiciel de gestion du service Prévention')
            ->appendName('author', 'SDIS62 - Service Recherche et Développement');
            
        $view->addHelperPath(APPLICATION_PATH . "/views/helpers");
            
        return $view;
    }
    
    /**
     * Initialisation du layout
     */   
    protected function _initLayout()
    {
        return Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . DS . 'layouts'));
    }
    
    /**
     * On force le stockage des sessions pour tous les modules
     */
    protected function _initSession()
    {
        Zend_Session::setOptions($this->getOption('session') ? $this->getOption('session') : array());
    }
}
