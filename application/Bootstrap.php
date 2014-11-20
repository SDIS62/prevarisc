<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        // Chargement des plugins de base
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_View);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_ACL);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_XmlHttpRequest);
        //Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_Security);

        // Chargement des plugins tiers
        if (getenv('PREVARISC_THIRDPARTY_PLUGINS')) {
            $thirdparty_plugins = explode(';', getenv('PREVARISC_THIRDPARTY_PLUGINS'));
            foreach($thirdparty_plugins as $thirdparty_plugin) {
                Zend_Controller_Front::getInstance()->registerPlugin(new $thirdparty_plugin);
            }
        }

        return parent::run();
    }

    /**
     * Initialisation du cache APC
     */
    protected function _initCache()
    {
        return Zend_Cache::factory('Core', 'APC', array(
            'lifetime' => getenv('PREVARISC_CACHE_LIFETIME'),
            'cache_id_prefix' => 'prevarisc'
        ));
    }

    /**
     * Initialisation du cache APC spécial recherches
     */
    protected function _initCacheSearch()
    {
        return Zend_Cache::factory('Core', 'APC', array(
            'lifetime' => getenv('PREVARISC_CACHE_LIFETIME'),
            'cache_id_prefix' => 'prevarisc_search'
        ));
    }

    /**
     * Initialisation de l'auto-loader
     */
    protected function _initAutoLoader()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();

        $autoloader_application = new Zend_Application_Module_Autoloader(array('basePath' => APPLICATION_PATH, 'namespace'  => null));

        $autoloader->pushAutoloader($autoloader_application);

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
     * Initialisation du data store à utiliser
     */
    public function _initDataStore()
    {
        $options = $this->getOption('resources');
        $options = $options['dataStore'];
        $className = $options['adapter'];

        return new $className($options);
    }

}
